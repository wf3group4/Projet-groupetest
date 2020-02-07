<?php

namespace App\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Repository\UsersRepository;

use App\Entity\Users;

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

}
