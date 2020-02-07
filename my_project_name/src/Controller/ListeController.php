<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\Users;
use App\Repository\AnnoncesRepository;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Repository\UsersRepository;


class ListeController extends AbstractController
{
    /**
     * @Route("/listes_profils", name="listes-profils")
     */
    public function Profils(UsersRepository $usersRepo)
    {
        return $this->render('liste/LesProfils.html.twig', [
            'profiles' => $usersRepo->findAll(),
        ]);
    }

    /**
     * @Route("/profil/{id}", name="profil")
     */
    public function Profil($id)
    {
        // On récupère User repository
        $em = $this->getDoctrine()->getManager();
        $usersRepo = $em->getRepository(Users::class);

        // requête pour récupérer tous les profil
        $profil = $usersRepo->find($id);

        if(!$profil) {
            $this->addFlash('danger', "Le profil demandé n'a pas été trouvé.");
            return $this->redirectToRoute('accueil');
        }

        return $this->render('liste/profil.html.twig', [
            'profil' => $profil,
        ]);
    }

    /**
     * @Route("/annonces", name="annonces")
     */
    public function annonces(AnnoncesRepository $annoncesRepo)
    {

        // Requete pour récupérer toutes les annonces
        $annonces = $annoncesRepo->findAll();

        return $this->render('liste/annonces.html.twig', [
            'annonces' => $annonces,
        ]);
    }

    /**
     * @Route("/annonce/{id}", name="annonce")
     */
    public function annonce($id)
    {

        // On récupère l' AnnoncesRepository
        $em = $this->getDoctrine()->getManager();
        $annoncesRepo = $em->getRepository(Annonces::class);

        // On récupère l'annonce, en fonction de l'ID qui est dans l'URL
        $annonce = $annoncesRepo->find($id);

        

        if (!$annonce) {
            $this->addFlash('danger', "L'article demandé n'a pas été trouvé.");
            return $this->redirectToRoute('annonces');
        }


        return $this->render('liste/annonce.html.twig', [
            'annonce' => $annonce,
        ]);
    }
}


