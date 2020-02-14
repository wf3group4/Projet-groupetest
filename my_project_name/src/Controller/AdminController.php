<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SignalementRepository;
use App\Repository\AnnoncesRepository;
use App\Repository\UsersRepository;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends AbstractController
{
    /**
     * @Route("/a/admin", name="admin")
     */
    public function admin(
        SignalementRepository $signalementRepo,
        UsersRepository $userRepo,
        AnnoncesRepository $annoncesRepo,
        Request $request)
    {
        //Recuperation query
        $em = $this->getDoctrine()->getManager();
        $id = $request->query->get('id');
        $action = $request->query->get('action');
        $cible = $request->query->get('cible');

        //Gestion des signalements
        $all_signalements = $signalementRepo->getLastWeekSignalements();
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

        //Suppression compte/annonce
        if($cible == "user"){
            if($action ="delete")
            {
            $user = $userRepo->find($id);
            $user
                ->setActive(0);
            $em->persist($user);
            $em->flush();
            }
            if($action ="ok")
            {

            }
        }
        if($cible == "annonce"){
            $annonce = $annoncesRepo->find($id);
            $annonce
                ->setActive(0);
            $em->persist($annonce);
            $em->flush();
        }




        return $this->render('admin/admin.html.twig', [
            'signalements_annonces' => $signalements_annonces,
            'signalements_user' => $signalements_user,
            'all_signalements' => $all_signalements
        ]);
    }
}
