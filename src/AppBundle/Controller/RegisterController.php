<?php

  namespace AppBundle\Controller;

  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\Request;
  use AppBundle\Entity\userData;
  use AppBundle\Entity\authToken;


  class RegisterController extends Controller
  {
    /**
     * @Route("/register")
     */
    public function registerAction (Request $request)
    {
      $a = json_decode($request->getContent());
      $formData = json_decode($a->JsonData);
      $errorMessage = '';
      $companyName = 'unknown';

      error_log($request);
      error_log(json_encode($formData,true));

      // backend validation
      if (!property_exists($formData, 'firstName') || (!$formData->firstName)) {
        $errorMessage = 'Enter first name';
      } else if (!property_exists($formData, 'lastName') || (!$formData->lastName)) {
        $errorMessage = 'Enter last name';
      } else if (!property_exists($formData, 'email') || (!$formData->email)) {
        $errorMessage = 'Enter email';
      } else if (!property_exists($formData, 'passw') || (!$formData->passw)) {
        $errorMessage = 'Enter password';
      }

      if ($errorMessage) {
        error_log('error !!');
        return new Response ($errorMessage);
        error_log('see me ?');
      }

      if (property_exists($formData, 'companyName') && ($formData->companyName)) {
        $companyName = $formData->companyName;
      }

      // Prepare data to be inserted into Database
      $userData = new userData();
      $userData->setFirstName($formData->firstName);
      $userData->setLastName($formData->lastName);
      $userData->setEmail($formData->email);
      $userData->setCompanyName($formData->companyName);
      $userData->setPassw($formData->passw);

      $em = $this->getDoctrine()->getManager();
      $em->persist($userData);
      $em->flush();

      // generate API token
      $key = md5(microtime().rand());

      $authToken = new authToken();
      $authToken->setClient($userData->getId());
      $authToken->setAuthToken($key);

      $em->persist($authToken);
      $em->flush();

      $registerResponse = array(
        "message" => "successfully registered",
        "key" => $key
      );

      return new Response(
        json_encode($registerResponse)
      );
    }
  }
