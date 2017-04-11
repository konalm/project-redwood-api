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
      error_log('invoince create ()');
      error_log($request);

      $requestContent = json_decode($request->getContent());
      $clientId = json_decode($requestContent->ClientId);

      $authToken = $request->headers->get('key');

      $userId = $this->getDoctrine()
        ->getRepository('AppBundle:authToken')
        ->findOneByauth_token($authToken)
        ->getClient();

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
      $user = new userData();
      // $authToken = $request->headers->get('key');
      $authToken = '22590f0a7ca2b927e31a69e14c828ed8';
      $userId = $this->getDoctrine()
        ->getRepository('AppBundle:authToken')
        ->findOneByauth_token($authToken)
        ->getClient();

      if (!$userId) {
        $response = new Response();
        $response->setStatusCode('401');
        $response->setBody('not authenticated user');
        return $response;
      }

      error_log('invoice id -->');
      error_log($invoice_id);

      $userSettings = $this->getDoctrine()
        ->getRepository('AppBundle:userSettings')
        ->findOneByuser_id($userId)
        ->getAll();


      $clientId = $this->getDoctrine()
        ->getRepository('AppBundle:invoice')
        ->findOneByid($invoice_id)
        ->getClientId();

      $client = $this->getDoctrine()
        ->getRepository('AppBundle:client')
        ->findOneByid($clientId);



      require $this->get('kernel')->getRootDir() .  '/../vendor/autoload.php';

      $template_html = $this->renderView('template-a.html.twig',
        array(
          'user' => $userSettings,
          'client' => $client
      ));

      error_log('invoice preview()');
      error_log($invoice_id);

      $dompdf = new Dompdf();
      $dompdf->loadHtml($template_html);
      $dompdf->setPaper('A4', 'portrait');
      $dompdf->render();
      $dompdf->stream("template_output.pdf", array("Attachment" => false));

      return new Response('previewing invoice');
    }

    /**
     * @Route("/pdf")
     */
     public function pdfAction()
     {
       $html = $this->renderView('base.html.twig');

        $filename = sprintf('test-%s.pdf', date('Y-m-d'));

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
     }


    /**
     * @Route("/create-invoice-pdf")
     */
     public function createInvoicePdfAction(Request $request)
     {
        require $this->get('kernel')->getRootDir() .  '/../vendor/autoload.php';

        // <!-- load template html -->
        $template_html = $this->renderView('template-a.html.twig',
          array('userData' => $this->userDataCollected)
        );

        $dompdf = new Dompdf();
        $dompdf->loadHtml($template_html);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();
        $dompdf->stream("dompdf_out.pdf", array("Attachment" => false));

        return new Response('rendering');
      }

    /**
    * @Route("/send-invoice")
    */
    public function sendInvoiceAction()
    {
      // <!-- load template html -->
      $template_html = $this->render('template-a.html.twig',
        array()
      );

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
  }
