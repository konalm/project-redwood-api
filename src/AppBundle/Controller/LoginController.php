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
      $response = new Response();
      $requestContent = json_decode($request->getContent());
      $formData = json_decode($requestContent->LoginData);
      $errorMessage = '';

      // backend validation
      if (!property_exists($formData, 'email') || (!$formData->email)) {
        $errorMessage = 'Enter Email';
      } else if (!property_exists($formData, 'passw') || (!$formData->passw)) {
        $errorMessage = 'Enter Password';
      }

      if ($errorMessage) {
        error_log('error');
        $response->setStatusCode('400');
        $response->setContent('Enter email and password');
        return $response;
      }

      // check details in DB
      $DbDetails = $this->getDoctrine()
        ->getRepository('AppBundle:userData')
        ->findOneByemail($formData->email);

      error_log('db details -->');
      error_log(print_r($DbDetails,true));

      // check for email and password match
      if ($DbDetails->getPassw() !== $formData->passw) {
        $response->setStatusCode('401');
        $response->setContent('Email and Password do not match');
        return $response;
      }

      error_log('reach me ??');

      // Auth Token
      // get ID from email
      $authToken = $this->getDoctrine()
        ->getRepository('AppBundle:authToken')
        ->findOneByclient($DbDetails->getId());

      // generate API token
      $key = md5(microtime().rand());
      error_log($key);

      // update in token database where client equal id
      $authToken->setAuthToken($key);
      $em = $this->getDoctrine()->getManager();
      $em->persist($authToken);
      $em->flush();

      error_log('saved auth token');

      $response->setContent(json_encode(array(
        'message' => 'Login successful',
        'key' => $key
      )));

      return $response;
    }
}
