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
    public function createClient(Request $request)
    {
      $requestContent = json_decode($request->getContent());
      $clientData = json_decode($requestContent->ClientData);

      $newClient = new client();
      $newClient->setAll($clientData);
      $em = $this->getDoctrine()->getManager();
      $em->persist($newClient);
      $em->flush();

      $newClientId = $newClient->getId();

      return new Response(
        json_encode(
          array(
            'message' => 'client created',
            'client_id' => $newClientId
      )));
    }
  }
