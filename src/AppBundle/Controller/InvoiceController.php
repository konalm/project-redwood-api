<?php

  namespace AppBundle\Controller;

  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\Request;
  use Dompdf\Dompdf;

  use AppBundle\Entity\userData;
  use AppBundle\Entity\invoice;


  class InvoiceController extends Controller
  {
    /**
     * @Route("/collect-data")
     */
    public function collectData(Request $request)
    {
      $requestContent = json_decode($request->getContent());
      $invoiceData = json_decode($requestContent->InvoiceData);

      $this->userDataCollected = $invoiceData;
      $this->container->get('app.Model.InvoiceData')->setUserData($invoiceData);

      return new Response('collected invoice data');
    }

    /**
     * @Route("/invoice-create")
     */
    public function invoiceCreate(Request $request)
    {
      $requestContent = json_decode($request->getContent());
      $clientId = json_decode($requestContent->ClientId);

      $authToken = $request->headers->get('key');
      $userId = $this->getUserId($authToken);

      $newInvoice = new invoice();
      $newInvoice->setUserId($userId);
      $newInvoice->setClientId($clientId);

      $em = $this->getDoctrine()->getManager();
      $em->persist($newInvoice);
      $em->flush();

      return new Response(
        json_encode(
          array(
            'message' => 'new invoice created',
            'invoice_id' => $newInvoice->getId()
      )));
    }

    /**
     * @Route("/invoice-preview/{invoice_id}")
     */
    public function invoicePreview(Request $request, $invoice_id)
    {
      $authToken = $request->query->get('auth-token');
      $userId = $this->getUserid($authToken);

      // not authenticated
      if (!$userId) {
        $response = notAuthenticatedResponse();
        return $response;
      }

      $userSettings = $this->getUserSettings($userId);
      $clientId = $this->getClientId($invoice_id);
      $client = $this->getClient($clientId);
      $template_html = $this->getTemplateHtml($userSettings, $client);

      require $this->get('kernel')->getRootDir() .  '/../vendor/autoload.php';

      $dompdf = new Dompdf();
      $dompdf->loadHtml($template_html);
      $dompdf->setPaper('A4', 'portrait');
      $dompdf->render();
      $dompdf->stream("template_output.pdf", array("Attachment" => false));

      return new Response('previewing invoice');
    }

    /**
     * @Route("/invoice-download/{invoice_id}")
     */
    public function invoiceDownload(Request $request, $invoice_id)
    {
      $authToken = $request->query->get('auth-token');
      $userId = $this->getUserId($authToken);

      // not authenticated
      if (!$userId) {
        $response = notAuthenticatedResponse();
        return $response;
      }

      $userSettings = $this->getUserSettings($userId);
      $client = $this->getClient($this->getClientId($invoice_id));
      $template_html = $this->getTemplateHtml($userSettings, $client);

      require $this->get('kernel')->getRootDir() .  '/../vendor/autoload.php';

      $dompdf = new Dompdf();
      $dompdf->loadHtml($template_html);
      $dompdf->setPaper('A4', 'portrait');
      $dompdf->render();
      $dompdf->stream();

      return new Response('download invoice');
    }

    /**
    * @Route("/send-invoice/{invoice_id}")
    */
    public function sendInvoice(Request $request, $invoice_id)
    {
      error_log('send invoice');

      $authToken = $request->headers->get('key');
      $userId = $this->getUserId($authToken);

      // not authenticated
      if (!$userId) {
        $response = $this->notAuthenticatedResponse();
        return $response;
      }

      $userSettings = $this->getUserSettings($userId);
      $clientId =  $this->getClientId($invoice_id);
      $client = $this->getClient($clientId);
      $template_html = $this->getTemplateHtml($userSettings, $client);

      require $this->get('kernel')->getRootDir() .  '/../vendor/autoload.php';

      $dompdf = new Dompdf();
      $dompdf->loadHtml($template_html);
      $dompdf->setPaper('A4', 'portrait');
      $dompdf->render();
      $output = $dompdf->output();

      $attachment = \Swift_Attachment::newInstance()
        ->setFilename('myfile.pdf')
        ->setContentType('application/pdf')
        ->setBody($output);

      $message = \Swift_Message::newInstance()
        ->setSubject('Hello Email')
        ->setFrom('connorlloydmoore@gmail.com')
        ->setTo('connor@codegood.co')
        ->setBody('this is an email from connor =]')
        ->attach($attachment);

      $this->get('mailer')->send($message);

      return new Response('message sent');
    }


    function getUserId($authToken) {
      return $this->getDoctrine()
        ->getRepository('AppBundle:authToken')
        ->findOneByauth_token($authToken)
        ->getClient();
    }

    function notAuthenticatedResponse() {
      $response = new Response();
      $response->setStatusCode('401');
      $response->setBody('not authenticated user');
      return $response;
    }

    function getUserSettings($userId) {
      return $this->getDoctrine()
        ->getRepository('AppBundle:userSettings')
        ->findOneByuser_id($userId)
        ->getAll();
    }

    function getClientId($invoice_id) {
      return  $this->getDoctrine()
        ->getRepository('AppBundle:invoice')
        ->findOneByid($invoice_id)
        ->getClientId();
    }

    function getClient($clientId) {
      return $this->getDoctrine()
        ->getRepository('AppBundle:client')
        ->findOneByid($clientId);
    }

    function getTemplateHtml($userSettings, $client) {
      return $this->renderView(
        'template-a.html.twig',
          array(
            'user' => $userSettings,
            'client' => $client
          ));
    }
  }
