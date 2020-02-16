<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SignalementRepository;
use App\Repository\AnnoncesRepository;
use App\Repository\UsersRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Service\EmailService;

class AdminController extends AbstractController
{

    /**
     * @Route("/a/admin", name="admin")
     */
    public function admin(
        SignalementRepository $signalementRepo,
        UsersRepository $userRepo,
        AnnoncesRepository $annoncesRepo,
        EmailService $emailService,
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

        //Suppression compte
        if($cible == "user"){
            if($action == "delete")
            {
            //On ban l'utilisateur
            $user = $userRepo->find($id);
            $user
                ->setActive(2);
            //On désactive les signalements liées à l'utilisateur
                $messages = $signalementRepo->getUserSignalement($id);
                    foreach($messages as $message){
                        $message
                            ->setActive(0);

                    $em->persist($message);
                    $em->flush();
                }

                $em->persist($user);
                $em->flush();

                $emailService->suppression_compte($user);
                $this->addFlash('success', "L'utilisateur a bien été banni!");
                return $this->redirectToRoute('admin');
            }
            if($action == "ok"){
             //On désactive les signalements liées à l'utilisateur
                $messages = $signalementRepo->getUserSignalement($id);
                    foreach($messages as $message){
                        $message
                            ->setActive(0);

                        $em->persist($message);
                        $em->flush();

                    }
                $this->addFlash('success', "L'utilisateur a bien été relaxer!");
                return $this->redirectToRoute('admin');
            }
        }
        //Suppression annonce
        if($cible == "annonce"){
            if($action == "delete")
            {
            //On ban l'utilisateur
            $annonce = $annoncesRepo->find($id);
            $annonce
                ->setActive(0);
            //On désactive les signalements liées à l'utilisateur
            $messages = $signalementRepo->getAnnonceSignalement($id);
                foreach($messages as $message){
                    $message
                        ->setActive(0);

                    $em->persist($message);
                    $em->flush();

                }
                
                $em->persist($annonce);
                $em->flush();

                $emailService->suppression_annonce($annonce);
                $this->addFlash('success', "L'annonce a bien été supprimer!");
                return $this->redirectToRoute('admin');
            }
            if($action == "ok"){
             //On désactive les signalements liées à l'utilisateur
                $messages = $signalementRepo->getAnnonceSignalement($id);
                    foreach($messages as $message){
                        $message
                            ->setActive(0);

                        $em->persist($message);
                        $em->flush();

                    }
                    $this->addFlash('success', "Le signalement est ignoré!");
                    return $this->redirectToRoute('admin');
            }
        }
        //Récupération des bannis
        $bannis = $userRepo->getLesBannis();

        //Réactivation du compte
        if($action == "debann"){
            $user = $userRepo->find($id);
            $user
                ->setActive(1);
            $em->persist($user);
            $em->flush();

            $emailService->reactivation_compte($user);

            $this->addFlash('success', "L'utilisateur a bien été activé!");
            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/admin.html.twig', [
            'signalements_annonces' => $signalements_annonces,
            'signalements_user' => $signalements_user,
            'bannis' => $bannis
        ]);
    }
}
