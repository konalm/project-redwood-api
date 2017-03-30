<?php

  namespace AppBundle\Controller;

  use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
  use Symfony\Component\HttpFoundation\Response;
  use AppBundle\Entity\userData;

  class UserDataController
  {
    /**
     * @Route("/user-data")
     */
     public function UserDataAction()
     {
       return new Response(
        'user data response'
      );
     }
  }
