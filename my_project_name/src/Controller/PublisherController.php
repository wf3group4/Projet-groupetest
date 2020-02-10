<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PublisherController extends AbstractController
{
    /**
     * @Route("/a/", name="publisher_admin")
     */
    public function publisher_admin()
    {
        if ($this->getUser()->hasRoles('ROLE_ADMIN')){
            return $this->redirectToRoute('admin');
        }
        return $this->render('publisher/publisher_admin.html.twig', [
            'controller_name' => 'PublisherController',
        ]);
    }
}
