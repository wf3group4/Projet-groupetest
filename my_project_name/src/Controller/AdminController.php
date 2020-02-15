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

class AdminController extends AbstractController
{
    /**
     * @Route("/a/admin", name="admin")
     */
    public function admin(
        SignalementRepository $signalementRepo,
        UsersRepository $userRepo,
        AnnoncesRepository $annoncesRepo,
        Request $request
    ) {
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

        //Suppression compte/annonce
        if ($cible == "user") {
            if ($action = "delete") {
                $user = $userRepo->find($id);
                $user
                    ->setActive(0);
                $em->persist($user);
                $em->flush();
            }
            if ($action = "ok") {
            }
        }
        if ($cible == "annonce") {
            $annonce = $annoncesRepo->find($id);
            $annonce
                ->setActive(0);
            $em->persist($annonce);
            $em->flush();
        }

        return $this->render('admin/admin.html.twig', [
            'signalements_annonces' => $signalements_annonces,
            'signalements_user' => $signalements_user,
            'all_signalements' => $all_signalements,
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
        if (!$fin) {
            $fin = date("Y-m-d");
        }
        if (!$debut) {
            $time = strtotime("-1 year", time());
            $debut = date('Y-m-d', $time);
        }

        // Stats sur le nombre d'annonce crée \\

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
            return $this->redirectToRoute('admin');
        }
        foreach ($annonceCree as $annonce) {
            $fmt = new IntlDateFormatter('fr_FR', IntlDateFormatter::MEDIUM, IntlDateFormatter::NONE);
            $fmt->setPattern('MMMM yyyy');
            $liste[] = ucfirst($fmt->format(strtotime(date_format($annonce->getDateCreation(), "F-Y"))));
            $annonceMois = array_count_values($liste);
        }
        $data['labels'] = array_keys($annonceMois);
        $data['values'] = array_values($annonceMois);

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
            'erreur' => $erreur,
            // 'test' => $test
        ]);
    }
}
