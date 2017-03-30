<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class LoginController
{
  /**
   * @route("/login")
   */
   public function loginAction()
   {
     return new Response(
       'login'
     );
   }
}
