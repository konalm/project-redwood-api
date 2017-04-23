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
      error_log('get user data');
      $authenticationKey = $request->headers->get('key');
      error_log($authenticationKey);
      

      $verifyClient = $this->getDoctrine()
        ->getRepository('AppBundle:authToken')
        ->findOneByauth_token($authenticationKey);

      // check client authentication
      if (!$this->clientAuthenticated($verifyClient, $authenticationKey)) {
        $response = new Response();
        $response->setStatusCode('401');
        $response->setContent('no user data for access token');
        return $response;
      }

      return new response(json_encode($this->getClientData($verifyClient)));
    }


    function clientAuthenticated($verifyClient, $authenticationKey) {
      if (!$verifyClient || $authenticationKey == '') {
        return false;
      }

      return true;
    }

    function getClientData($verifyClient) {
      $clientId = $verifyClient->getClient();

      return $this->getDoctrine()
        ->getRepository('AppBundle:userData')
        ->findOneByid($clientId)
        ->getAll();
    }
  }
