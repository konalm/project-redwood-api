<?php

  namespace AppBundle\Model;

  use Symfony\Component\DependencyInjection\ContainerInterface;

  class InvoiceData
  {

    private $container;
    private $userData;

    public function __construct(ContainerInterface $container)
    {
      $this->$container = $container;
    }

    public function getUserData() {
      return $this->userData;
    }

    public function setUserData($userData) {
      $this->userData = $userData;
    }
  }
