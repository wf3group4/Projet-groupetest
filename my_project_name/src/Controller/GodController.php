<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GodController extends AbstractController
{
    /**
     * @Route("/a/god", name="god")
     */
    public function god()
    {
        return $this->render('god/god.html.twig', [
            'controller_name' => 'GodController',
        ]);
    }
}
