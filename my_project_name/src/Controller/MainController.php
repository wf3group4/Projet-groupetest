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
    public function accueil(UsersRepository $usersRepo, AnnoncesRepository $annoncesRepo)
    {
        $personnes = $usersRepo->getLastUser();
        //dump($titre); die();
        $annonces = $annoncesRepo->getLastAnnonces(3);
       

        return $this->render('main/index.html.twig', [
            'personnes'=> $personnes,
            'annonces'=> $annonces
           
        ]);
        //return $this->findBy(
         //   array('active' => 1),
           // array('date_creation' => 'DESC')
      //  );

     
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
        // $portfolios = $portfolioRepo->getUserPortfolios($this->getUser());

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
                       $user->setImgUrl($nameOfPicture);
                    }

    
            $em->persist($portfolios);
            $em->flush();
            

            $this->addFlash('success', "Les réalisations on bien été modifiées");

            return $this->redirectToRoute('mon_compte', [
                'id' => $user->getId()
            ]);
        }

       
        return $this->render('main/mon_compte.html.twig', [
            'annonces' => $annonces,
            'portfolios' =>$portfolios, 
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
