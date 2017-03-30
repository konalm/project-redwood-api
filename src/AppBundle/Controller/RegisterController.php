<?php

  namespace AppBundle\Controller;

  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\Request;
  use AppBundle\Entity\userData;


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
      } else {
        error_log('no error -> 1');
      }

      if (property_exists($formData, 'companyName') && ($formData->companyName)) {
        error_log('assigned company name');
        $companyName = $formData->companyName;
      } else {
        error_log('no error -> 2');
      }

      // $sql = "INSERT INTO user_data (first_name, last_name, email, company_name, passw)" .
      //   "('$formData->firstName','$formData->lastName','$formData->email','$companyName', '$formData->passw')";

      $userData = new userData();
      $userData->setFirstName($formData->firstName);
      $userData->setLastName($formData->lastName);
      $userData->setEmail($formData->email);
      $userData->setCompanyName($formData->companyName);
      $userData->setPassw($formData->passw);

      error_log('added data');

      $em = $this->getDoctrine()->getManager();
      error_log('a');

      $em->persist($userData);
      error_log('b');

      $em->flush();
      error_log('doctrine ??');


      return new Response(
        'you are now registered'
      );
    }
  }
