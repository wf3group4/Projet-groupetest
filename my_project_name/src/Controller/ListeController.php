<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\Users;
use App\Entity\Avis;
use App\Form\ContactProType;
use App\Repository\AnnoncesRepository;


use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


use App\Repository\UsersRepository;


class ListeController extends AbstractController
{
    /**
     * @Route("/liste-profils", name="liste-profils")
     */
    public function Profils(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $usersRepo = $em->getRepository(Users::class);
        $profil = $usersRepo->findAll();


        $search = $request->query->get('name');
            if ($search)
            {
                $profil = $usersRepo->SearchByName($search);

                if(!$profil)
                {
                    $this->addFlash('danger', 'Aucun résultat trouvé');
                    return $this->redirectToRoute('liste-profils');
                }
                else {
                    $profil = $usersRepo->findOneBy(['Name' => $search, 'Lastname' => $search]);
                    $this->addFlash('success', 'Résultat trouvée !');

                    return $this->redirectToRoute('liste-profils',[
                        'profiles' => $profil,
                    ]);

                }


            }




        return $this->render('liste/LesProfils.html.twig', [
            'profiles' => $profil,
        ]);
    }


    /**
     * @Route("/profil/{id}", name="profil")
     */
    public function Profil($id, Request $request)
    {
        // On récupère User repository
        $em = $this->getDoctrine()->getManager();
        $usersRepo = $em->getRepository(Users::class);
        // requête pour récupérer tous les profil
        $profil = $usersRepo->find($id);

        if(!$profil) {
            $this->addFlash('danger', "Le profil demandé n'a pas été trouvé.");
            return $this->redirectToRoute('accueil');
        }

        //Ajoute un commentaire

        if($request->isMethod('POST'))
        {
            $data = $request->request->all();

            $avis = (new Avis())
                ->setEmail($data['email'])
                ->setContenu($data['contenu'])
                ->setRgpd(1)
                ->setCreateAt(new \DateTime())
                ->setUsers($profil)
                ;

            $em->persist($avis);
            $em->flush();

            $this->addFlash('success', 'Avis Ajouté !');
            return $this->redirectToRoute('profil', ['id' => $profil->getId()] );

        }

        // Supprimer un avis

        $action = $request->query->get('action');
        if($action && $action == 'delete')
        {
            $id_avis = $request->query->get('id_avis');

            if($id_avis)
            {
                $avisRepo = $em->getRepository(Avis::class);
                $avis = $avisRepo->find($id_avis);

                $em->remove($avis);
                $em->flush();

                $this->addFlash('success', 'Vous venez de supprimer un avis !');
                return $this->redirectToRoute('profil', ['id' => $profil->getId()]);
            }
        }

        return $this->render('liste/profil.html.twig', [
            'profil' => $profil,
        ]);
    }

    /**
     * @Route("/contact-profil/{id}", name="contact-profil")
    */
    public function contactProfil($id, EmailService $emailService, Request $request)
    {
        // On récupère User repository
        $em = $this->getDoctrine()->getManager();
        $usersRepo = $em->getRepository(Users::class);
        // requête pour récupérer tous les profil
        $profil = $usersRepo->find($id);


        $form = $this->createForm(ContactProType::class);

        if($form->isSubmitted() && $form->isValid())
        {
            $params = $request->request->all();


            $emailService->send($params['form']);

            $this->addFlash('success', 'Votre message à bien été envoyé !');

            return $this->redirectToRoute('liste-profils');
        }

        return $this->render('liste/contactProfil.html.twig',[
            'profil' => $profil,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/annonces", name="annonces")
     */
    public function annonces(AnnoncesRepository $annoncesRepo)
    {

        // Requete pour récupérer toutes les annonces
        $annonces = $annoncesRepo->findAll();

        return $this->render('liste/annonces.html.twig', [
            'annonces' => $annonces,
        ]);
    }

    /**
     * @Route("/annonce/{id}", name="annonce")
     */
    public function annonce($id)
    {

        // On récupère l' AnnoncesRepository
        $em = $this->getDoctrine()->getManager();
        $annoncesRepo = $em->getRepository(Annonces::class);

        // On récupère l'annonce, en fonction de l'ID qui est dans l'URL
        $annonce = $annoncesRepo->find($id);

        

        if (!$annonce) {
            $this->addFlash('danger', "L'article demandé n'a pas été trouvé.");
            return $this->redirectToRoute('annonces');
        }


        return $this->render('liste/annonce.html.twig', [
            'annonce' => $annonce,
        ]);
    }
}


