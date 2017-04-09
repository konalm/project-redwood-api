<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


class LoginController extends Controller
{
  /**
   * @route("/login")
   */
   public function loginAction(Request $request)
   {
      $requestContent = json_decode($request->getContent());
      $formData = json_decode($requestContent->LoginData);

      $response = new Response();

      // validation
      $errorMessage = $this->validateForm($formData);

      if ($errorMessage) {
        $response->setStatusCode('401');
        $response->setContent('Enter email and password');
        return $response;
      }

      $dbDetails = $this->getDoctrine()
        ->getRepository('AppBundle:userData')
        ->findOneByemail($formData->email);

      // check if email exists
      if (!$dbDetails) {
        $response->setStatusCode('401');
        $response->setContent('Email address not found');
        return $response;
      }

      // ckeck email and password match
      if (!password_verify($formData->passw, $dbDetails->getPassw())) {
        $response->setStatusCode('401');
        $response->setContent('Incorrect Password');
        return $response;
      }

      $key = md5(microtime().rand());
      $this->saveAuthToken($dbDetails, $key);

      return new Response(
        json_encode(
          array(
            'message' => 'Login Successful',
            'key' => $key
      )));
    }


    function validateForm($formData) {
      if (!property_exists($formData, 'email') || (!$formData->email)) {
        return 'Enter Email';
      } else if (!property_exists($formData, 'passw') || (!$formData->passw)) {
        return 'Enter Password';
      }
    }

    function saveAuthToken($dbDetails, $key) {
      $authToken = $this->getDoctrine() // Get ID from token
        ->getRepository('AppBundle:authToken')
        ->findOneByclient($dbDetails->getId());

      $authToken->setAuthToken($key);
      $em = $this->getDoctrine()->getManager();
      $em->persist($authToken);
      $em->flush();
    }
}
