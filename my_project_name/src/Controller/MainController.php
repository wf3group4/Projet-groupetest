<?php

namespace App\Controller;

use App\Form\ModifCompteType;
use App\Form\PortfolioType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UsersRepository;
use App\Repository\AnnoncesRepository;
use App\Repository\PortfolioRepository;
use App\Entity\Portfolio;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function accueil(UsersRepository $usersRepo)
    {
        $personnes = $usersRepo->getLastUser();
        //dump($titre); die();

        return $this->render('main/index.html.twig', [
            'personnes'=> $personnes
        ]);
    }

    /**
     * @Route("/mon-compte/", name="mon_compte")
     */
    public function mon_compte(
        AnnoncesRepository $annoncesRepo, 
        PortfolioRepository $portfolioRepo,
        Request $request)
    {
        $user = $this->getUser();
        $annonces = $annoncesRepo->getUserAnnonces($user);
        $liens = $portfolioRepo->getUserLiens($user);

        //Ajout de liens/images au portfolio
        $em = $this->getDoctrine()->getManager();
        $portfolios = $portfolioRepo->getUserPortfolios($user);
        
        $new_image = new Portfolio();
        $form = $this->createForm(PortfolioType::class, $new_image );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $portfolios = $form->getData()
                ->setUser($user)
            ;

                $file = $form['img_url']->getData();
                    if($file){
                       $repertoire = $this->getParameter('images');
                       $nameOfPicture = 'portfolio-'.uniqid().'.'.$file->guessExtension();
                       $file->move($repertoire, $nameOfPicture);
                       $portfolios->setImgUrl($nameOfPicture);
                    }
            $em->persist($portfolios);
            $em->flush();
            
            $this->addFlash('success', "Les réalisations on bien été modifiées");

            return $this->redirectToRoute('mon_compte', [
                'id' => $user
            ]);
        }

        //Supprimer l'image
        $action = $request->query->get('action');
        if($action && $action == 'delete-img'){
            $id_img = $request->query->get('id_img');
            $portfolios= $portfolioRepo->find($id_img);
            $portfolios->setImgUrl(NULL);
            $em->flush();
            $this->addFlash('danger', "L'image a bien été supprimé.");
            return $this->redirectToRoute('mon_compte',[
                    'id' => $user
                ]);
        }

        //Supprimer le lien
         $action = $request->query->get('action');
         if($action && $action == 'delete-lien'){
             $id_lien = $request->query->get('id_lien');
             $liens= $portfolioRepo->find($id_lien);
             $liens->setLiens(NULL);
             $em->flush();
             $this->addFlash('danger', "Le lien a bien été supprimé.");
             return $this->redirectToRoute('mon_compte',[
                     'id' => $user
                 ]);
         }

        return $this->render('main/mon_compte.html.twig', [
            'annonces' => $annonces,
            'portfolios' => $portfolios, 
            'liens' => $liens,
            'id' => $user,
            'form' => $form->createView(),
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
