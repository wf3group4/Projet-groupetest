<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UsersRepository;
use App\Repository\AnnoncesRepository;

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
     * @Route("/mon-compte/", name="mon_compte")
     */
    public function mon_compte( AnnoncesRepository $annoncesRepo)
    {
        $annonces = $annoncesRepo->getUserAnnonces($this->getUser());
        // dump($annonces);die;    
        return $this->render('main/mon_compte.html.twig', [
            'annonces' => $annonces
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
