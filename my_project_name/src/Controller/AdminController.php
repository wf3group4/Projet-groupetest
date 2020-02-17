<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SignalementRepository;
use App\Repository\AnnoncesRepository;
use App\Repository\UsersRepository;
use DateTime;
use IntlDateFormatter;
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

        foreach ($all_signalements as $signalement) {
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
            if (count($annonces) < 3) {
                unset($signalements_annonces[$id_annonce]);
            }
        }

        foreach ($signalements_user as $id_user => $users) {
            if (count($users) < 3) {
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

    /**
     * @Route("/a/admin-stat", name="admin_stat")
     */
    public function admin_stat(AnnoncesRepository $annoncesRepo, UsersRepository $userRepo, Request $request)
    {

        // Paramètre globaux
        $erreur = null;
        $debut = $request->query->get('debut');
        $fin = $request->query->get('fin');
        $debutGraphCoture = $request->query->get('debutGraphCoture');
        $finGraphCoture = $request->query->get('finGraphCoture');

        // Stats sur le nombre d'annonce crée \\
        if (!$fin) {
            $fin = date("Y-m-d");
        }
        if (!$debut) {
            $time = strtotime("-1 year", time());
            $debut = date('Y-m-d', $time);
        }

        $query = $annoncesRepo->createQueryBuilder('a')
            ->where("a.date_creation BETWEEN :debut AND :fin")
            ->setParameter('debut', $debut)
            ->setParameter('fin', $fin)
            ->orderBy("a.date_creation", "ASC");

        $annonceCree = $query
            ->getQuery()
            ->getResult();
        if (!$annonceCree) {
            $this->addFlash('danger', 'Aucun résultat trouvé');;
            return $this->redirectToRoute('admin_stat');
        }
        foreach ($annonceCree as $annonce) {
            $fmt = new IntlDateFormatter('en', IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE);
            $fmt->setPattern('MMMM yyyy');
            $liste[] = ucfirst($fmt->format(strtotime(date_format($annonce->getDateCreation(), "F-Y"))));
            $annonceMois = array_count_values($liste);
        }
        $data['labels'] = array_keys($annonceMois);
        $data['values'] = array_values($annonceMois);



        // Stats sur le nombre d'annonce cloturé \\

        if (!$finGraphCoture) {
            $finGraphCoture = date("Y-m-d");
        }
        if (!$debutGraphCoture) {
            $time = strtotime("-1 year", time());
            $debutGraphCoture = date('Y-m-d', $time);
        }

        $query2 = $annoncesRepo->createQueryBuilder('a')
            ->where("a.closed_at BETWEEN :debut AND :fin")
            ->setParameter('debut', $debutGraphCoture)
            ->setParameter('fin', $finGraphCoture)
            ->orderBy("a.closed_at", "ASC");

        $annonceCloture = $query2
            ->getQuery()
            ->getResult();
        if (!$annonceCloture) {
            $this->addFlash('danger', 'Aucun résultat trouvé');;
            return $this->redirectToRoute('admin_stat');
        }
        foreach ($annonceCloture as $annonce) {
            $fmt = new IntlDateFormatter('en', IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE);
            $fmt->setPattern('MMMM yyyy');
            $listecloture[] = ucfirst($fmt->format(strtotime(date_format($annonce->getClosedAt(), "F-Y"))));
            $annonceClotureMois = array_count_values($listecloture);
        }
        $dataCloture['labels'] = array_keys($annonceClotureMois);
        $dataCloture['values'] = array_values($annonceClotureMois);

        // Stats sur le CA \\

        //     $query2 = $annoncesRepo->createQueryBuilder('a')
        //         ->where("a.closed_at BETWEEN :debut AND :fin")
        //         ->setParameter('debut', $debut)
        //         ->setParameter('fin', $fin)
        //         ->orderBy("a.closed_at", "ASC");;
        //     $stats = $query2
        //         ->getQuery()
        //         ->getResult();
        //     foreach ($stats as $stat) {
        //         $fmt = new IntlDateFormatter('fr_FR', IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE);
        //         $fmt->setPattern('MMMM yyyy');
        //         $testdate[] = ucfirst($fmt->format(strtotime(date_format($stat->getClosedAt(), "F-Y"))));
        //         $testprix[]= $stat->getPrix();
        //         $test[] = array_combine($testdate, $testprix);

        // }dump($test);die;

        return $this->render('admin/admin-stat.html.twig', [
            'data' => $data,
            'dataCloture' =>$dataCloture,
            'erreur' => $erreur,
            // 'test' => $test
        ]);
    }
}
