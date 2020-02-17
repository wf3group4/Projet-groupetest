<?php

namespace App\Controller;


use App\Entity\Annonces;
use App\Repository\AnnoncesRepository;
use App\Repository\NotifsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class PublisherController extends AbstractController
{
    /**
     * @Route("/quoi-de-neuf", name="publisher_admin")
     */
    public function publisher_admin(AnnoncesRepository $annoncesRepo, NotifsRepository $notifsRepo)
    {
        if ($this->getUser()->hasRoles('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin');
        }
        $annonce = $annoncesRepo->find($id);
        return $this->render('publisher/publisher_admin.html.twig', [
         'annonce' => $annonce,
          'notifs' => $notifsRepo->notifs($this->getUser())
        ]);  
    }
}
