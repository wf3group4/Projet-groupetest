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
        $signalements = $signalementRepo->getSignalement();


        return $this->render('admin/admin.html.twig', [
            'signalements' => $signalements
        ]);
    }
}
