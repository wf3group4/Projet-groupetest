<?php

namespace App\Controller;

use App\Form\ModifCompteType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UsersRepository;
use App\Repository\AnnoncesRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function index()
    {



        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }

    /**
     * @Route("/mon-compte/", name="mon_compte")
     */
    public function mon_compte( AnnoncesRepository $annoncesRepo)
    {
        $annonces = $annoncesRepo->getUserAnnonces($this->getUser());
        return $this->render('main/mon_compte.html.twig', [
            'annonces' => $annonces
        ]);
    }

     
    /**
     * @Route("/modification-compte/{id}", name="modif_compte")
     */
    public function modif_compte($id, Request $request, UsersRepository $usersRepo, UserPasswordEncoderInterface $passwordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $usersRepo->find($id);
        $form = $this->createForm(ModifCompteType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData()
                ->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                )
            ;

                $file = $form['avatar']->getData();
                    if($file){
                       $repertoire = $this->getParameter('images');
                       $nameOfPicture = 'avatar-'.rand(1,99999).'.'.$file->guessExtension();
                       $file->move($repertoire, $nameOfPicture);
                       $user->setAvatar($nameOfPicture);
                    }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "Le profil a bien été modifié.");

            return $this->redirectToRoute('mon_compte', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('main/modif_compte.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
