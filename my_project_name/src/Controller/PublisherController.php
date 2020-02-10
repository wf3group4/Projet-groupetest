<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PublisherController extends AbstractController
{
    /**
     * @Route("/quoi-de-neuf", name="publisher_admin")
     */
    public function publisher_admin()
    {
        if ($this->getUser()->hasRoles('ROLE_GOD')){
            return $this->redirectToRoute('god');
        }
        return $this->render('publisher/publisher_admin.html.twig', [
            'controller_name' => 'PublisherController',
        ]);
    }
}
