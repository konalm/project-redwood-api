<?php

 namespace AppBundle\Controller;

 use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
 use Symfony\Bundle\FrameworkBundle\Controller\Controller;
 use Symfony\Component\HttpFoundation\Response;
 use Symfony\Component\HttpFoundation\Request;

 use AppBundle\Entity\client;

 class ClientController extends Controller
 {

   /**
    * @Route("/client-create")
    */
  public function createNewClient(Request $request)
  {
    $requestContent = json_decode($request->getContent());
    $clientData = json_decode($requestContent->ClientData);

    $newClient = new client();

    // <!-- user token to get id for details --->
    // <!-- save data  -->
    // <!-- flush it -->
  }

 }
