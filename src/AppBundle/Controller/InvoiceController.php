<?php

  namespace AppBundle\Controller;

  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\Request;
  use Dompdf\Dompdf;

  use AppBundle\Model\InvoiceData;


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

       // store the user data
       return new Response('collected invoice data');
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
     * @Route("/create-invoice")
     */
     public function createInvoiceAction(Request $request)
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
