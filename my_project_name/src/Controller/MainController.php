<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index()
    {
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    
    /**
     * @Route("/mon-compte", name="mon_compte")
     */
    public function mon_compte()
    {
        return $this->render('main/mon_compte.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
