<?php

  namespace AppBundle\Controller;

  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\Request;

  class LogoutController extends Controller
  {
    /**
     * @route("/logout")
     */
    public function logoutAction(Request $request)
    {
      $authenticationKey = $request->headers->get('key');

      $verifyClient = $this->getDoctrine()
        ->getRepository('AppBundle:authToken')
        ->findOneByauth_token($authenticationKey);

      // check client authentication
      if (!$this->clientAuthenticated($authenticationKey, $verifyClient)) {
        $response = new Response();
        $response->setStatusCode('401');
        $response->setContent('no user data for given access token');
        return $response;
      }

      // terminate token in DB
      $this->terminateToken($verifyClient);

      return new Response('logged out successfully');
    }

    function terminateToken($verifyClient) {
      $verifyClient->setAuthToken('');

      $em = $this->getDoctrine()->getManager();
      $em->persist($verifyClient);
      $em->flush();
    }

    function clientAuthenticated($authenticationKey, $verifyClient) {

      if (!$verifyClient) {
        return false;
      }

      return true;
    }
  }
