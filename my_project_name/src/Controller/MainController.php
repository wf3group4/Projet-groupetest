<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UsersRepository;
use App\Controller\ListeController;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index()
    {   
        //requête pour recup données en base de données
            $profils = Profils();

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

     
    /**
     * @Route("/modification-compte/{name}", name="modif_compte")
     */
    public function modif_compte($name, Request $request, UsersRepository $usersRepo )
    {
        $em = $this->getDoctrine()->getManager();
        $user = $usersRepo->find($name); 
       
        return $this->render('main/modif_compte.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
