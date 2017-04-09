<?php

  namespace AppBundle\Controller;

  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Symfony\Bundle\FrameworkBundle\Controller\Controller;
  use Symfony\Component\HttpFoundation\Response;
  use Symfony\Component\HttpFoundation\Request;
  use AppBundle\Entity\authToken;

  class UserSettingsController extends Controller
  {
    /**
     * @Route("/user-settings")
     */
    public function GetUserSettingsAction(Request $request)
    {
      $authToken = $request->headers->get('key');
      error_log('get token -->');
      error_log($authToken);

      $clientId = $this->getClientId($authToken);
      $userSettingsAll = $this->getUserSettings($clientId)->getAll();

      return new Response(json_encode($userSettingsAll));
    }

    /**
     * @Route("/user-settings-update")
     */
    public function UpdateUserSettingsAction(Request $request)
    {
      error_log($request);
      $authToken = $request->headers->get('key');
      error_log('update token --->');
      error_log($authToken);
      
      $userSettings = $this->getUserSettings($this->getClientId($authToken));

      $requestContent = json_decode($request->getContent());
      $settingsData = json_decode($requestContent->SettingsData);

      // validation

      $userSettings->setAll($settingsData);

      $em = $this->getDoctrine()->getManager();
      $em->persist($userSettings);
      $em->flush();

      return new Response('settings updated');
    }


    function getClientId($authToken) {
      error_log('get client id -> auth token');
      error_log($authToken);

      return $this->getDoctrine()
        ->getRepository('AppBundle:authToken')
        ->findOneByauth_token($authToken)
        ->getClient();
    }

    function getUserSettings($clientId) {
      return $this->getDoctrine()
       ->getRepository('AppBundle:userSettings')
       ->findOneByuser_id($clientId);
    }

  }
