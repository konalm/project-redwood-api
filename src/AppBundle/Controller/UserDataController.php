<?php

  namespace AppBundle\Controller;

  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\Request;
  use AppBundle\Entity\userData;
  use AppBundle\Entity\authToken;


  class UserDataController extends Controller
  {
    /**
     * @Route("/user-data")
     */
    public function GetUserDataAction(Request $request)
    {
      $response = new Response();
      $authenticationKey = $request->headers->get('key');

      $verifyClient = $this->getDoctrine()
        ->getRepository('AppBundle:authToken')
        ->findOneByauth_token($authenticationKey);

      // client not authenticated
      if (!$verifyClient) {
        $response->setStatusCode('401');
        $response->setContent('401 - no user data for access token');
        return $response;
      }

      // client is authenticated
      $clientId = $verifyClient->getClient();

      $clientData = $this->getDoctrine()
        ->getRepository('AppBundle:userData')
        ->findOneByid($clientId)
        ->getAll();

      $response->setContent($clientData);

      return $response;
    }
  }
