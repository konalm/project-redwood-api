<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Entity\product;


class DefaultController extends Controller
{
    /**
     * @Route("/admin")
     */
     public function adminAction()
     {
       return new Response('admin page!!');
     }


    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/add", name="add-product")
     */
    public function createAction()
    {
      $product = new product();
      $product->setName('Keyboard');
      $product->setPrice('19.99');
      $product->setDescription('Slick and stylish');

      $em = $this->getDoctrine()->getManager();

      // tells Doctrine you want to (eventually) save the product (no queries yet)
      $em->persist($product);

      // actually execute the queries (.i.e the INSERT query)
      $em->flush();

      return new Response('saved new product with id ' . $product->getId());
    }
}
