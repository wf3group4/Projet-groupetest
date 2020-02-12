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
use App\Repository\AvisRepository;
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


            $em = $this->getDoctrine()->getManager();
            $user->setVues($user->getVues()+1);

            $em->flush();

        //Ajout de liens/images au portfolio
        $portfolios = $portfolioRepo->getUserPortfolios($id);
        
        $new_image = new Portfolio();
        $form = $this->createForm(PortfolioType::class, $new_image );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $portfolios = $form->getData()
                ->setUser($id)
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
                'id' => $id,
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
                ->setNom($data['nom'])
                ->setPrenom($data['prenom'])
                ->setContenu($data['contenu'])
                ->setRgpd(1)
                ->setCreateAt(new \DateTime())
                ->setNote($data['note'])
                ->setUsers($user);

            $em->persist($avis);
            $em->flush();



            $this->addFlash('success', 'Avis Ajouté !');
            return $this->redirectToRoute('mon_compte', [
                'id' => $id,
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
                    'id' => $id,
                ]);
            }
        }

        $moyenne = $user->getMoyenne();
        return $this->render('main/mon_compte.html.twig', [
            'annonces' => $annonces,
            'portfolios' => $portfolios, 
            'avis' => $avis,
            'liens' => $liens,
            'id' => $id,
            'user' => $user,
            'form' => $form->createView(),
            'moyenne' => $moyenne
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
}
