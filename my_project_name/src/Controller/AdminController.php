<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SignalementRepository;

class AdminController extends AbstractController
{
    /**
     * @Route("/a/admin", name="admin")
     */
    public function admin(SignalementRepository $signalementRepo)
    {
        $all_signalements = $signalementRepo->getSignalement();
        $signalements_user = $signalements_annonces = [];
    
        foreach ($all_signalements as $signalement ) {
            $annonce = $signalement->getAnnonce();
            $user = $signalement->getUser();
        
            if ($annonce) {
                $id_annonce = $signalement->getAnnonce()->getId();
                $signalements_annonces[$id_annonce][] = $signalement;
            }

            if ($user) {
                $id_user = $signalement->getUser()->getId();
                $signalements_user[$id_user][] = $signalement;  
            }
        }

        foreach ($signalements_annonces as $id_annonce => $annonces) {
            if(count($annonces) < 3) {
                unset($signalements_annonces[$id_annonce]);
            }
        }

        foreach ($signalements_user as $id_user => $users) {
            if(count($users) < 3) {
                unset($signalements_user[$id_user]);
            }
        }
        return $this->render('admin/admin.html.twig', [
            'signalements_annonces' => $signalements_annonces,
            'signalements_user' => $signalements_user,
            'all_signalements' => $all_signalements
        ]);
    }
}
