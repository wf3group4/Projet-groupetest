<?php

namespace App\Controller;

use App\Form\ModifCompteType;
use App\Form\PortfolioType;
use App\Form\SignalementType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UsersRepository;
use App\Repository\AnnoncesRepository;
use App\Repository\PortfolioRepository;
use App\Repository\AvisRepository;
use App\Entity\Signalement;
use App\Entity\Portfolio;
use App\Entity\Avis;
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
     * @Route("/mon-compte/{id}", name="mon_compte")
     */
    public function mon_compte(
        $id,
        AnnoncesRepository $annoncesRepo, 
        PortfolioRepository $portfolioRepo,
        UsersRepository $userRepo,
        AvisRepository $avisRepo,
        Request $request)
    {
        $user = $userRepo->find($id);
        $annonces = $annoncesRepo->getUserAnnonces($id);
        $liens = $portfolioRepo->getUserLiens($id);
        $avis = $avisRepo->getUserAvis($id);

        if (!$user) {
            $this->addFlash('danger', "Le profil demandé n'a pas été trouvé.");
            return $this->redirectToRoute('accueil');
        }
        //Ajout de liens/images au portfolio
        $em = $this->getDoctrine()->getManager();
        $portfolios = $portfolioRepo->getUserLastPortfolio($id);
        
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
                'id' => $id
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
                    'id' => $id
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
                     'id' => $id
                 ]);
         }

        //Ajoute un commentaire
        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            $avis = (new Avis())
                ->setEmail($data['email'])
                ->setContenu($data['contenu'])
                ->setRgpd(1)
                ->setCreateAt(new \DateTime())
                ->setUsers($user);

            $em->persist($avis);
            $em->flush();

            $this->addFlash('success', 'Avis Ajouté !');
            return $this->redirectToRoute('mon_compte', [
                'id' => $id
                ]);
        }

        // Supprimer un avis

        $action = $request->query->get('action');
        if ($action && $action == 'delete') {
            $id_avis = $request->query->get('id_avis');

            if ($id_avis) {
                $avisRepo = $em->getRepository(Avis::class);
                $avis = $avisRepo->find($id_avis);

                $em->remove($avis);
                $em->flush();

                $this->addFlash('success', 'Vous venez de supprimer un avis !');
                return $this->redirectToRoute('profil', [
                    'id' => $id 
                ]);
            }
        } 

        

        return $this->render('main/mon_compte.html.twig', [
            'annonces' => $annonces,
            'portfolios' => $portfolios, 
            'avis' => $avis,
            'liens' => $liens,
            'id' => $id,
            'user' => $user,
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
                ->setUpdatedAt(new \DateTime())
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

     /**
     * @Route("/portfolio/{id}", name="portfolio")
     */
    public function portfolio(
        $id, 
        UsersRepository $userRepo,
        PortfolioRepository $portfolioRepo)
    {
        $user = $userRepo->find($id);
        $portfolios = $portfolioRepo->getUserPortfolios($id);

        return $this->render('main/portfolio.html.twig', [
            'id' => $id,
            'user' => $user,
            'portfolios' => $portfolios
        ]);
     
    }

     /**
     * @Route("/signalement/{id}", name="signalement")
     */
    public function signalement(
        $id, 
        UsersRepository $userRepo,
        AnnoncesRepository $annonceRepo,
        Request $request)
    {
        //Récupération des variables
        $cible = $request->query->get('cible');
        $user = $userRepo->find($id);
        $annonce = $annonceRepo->find($id);
        $em = $this->getDoctrine()->getManager();

        //Création du signalement 
        $signalement = new Signalement();

        $form = $this->createForm(SignalementType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $signalement = $form->getData();

            if($cible == 'user'){
                $signalement
                    ->setUser($user);

                $em->persist($signalement);
                $em->flush();
        
                $this->addFlash('success', 'Votre signalement à bien été envoyé !');
                return $this->redirectToRoute('mon_compte', [
                        'id' => $id
                ]);

            }else{
                $signalement
                    ->setAnnonce($annonce);
                    // dump($signalement);die;
                $em->persist($signalement);
                $em->flush();
                    
                $this->addFlash('success', 'Votre signalement à bien été envoyé !');
                return $this->redirectToRoute('annonce', [
                        'id' => $id
                ]);
            }
        }
        return $this->render('main/form_signalement.html.twig', [
            'id' => $id,
            'user' => $user,
            'annonce' =>$annonce,
            'cible' => $cible,
            'form' => $form->createView(),
        ]);

    }
}
