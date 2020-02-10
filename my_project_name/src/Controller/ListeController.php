<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\Users;
use App\Entity\Avis;
use App\Form\CreerAnnonceType;
use App\Repository\AnnoncesRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Repository\UsersRepository;
use Exception;

class ListeController extends AbstractController
{
    /**
     * @Route("/listes_profils", name="listes-profils")
     */
    public function Profils(UsersRepository $usersRepo)
    {
        return $this->render('liste/LesProfils.html.twig', [
            'profiles' => $usersRepo->findAll(),
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

        if (!$profil) {
            $this->addFlash('danger', "Le profil demandé n'a pas été trouvé.");
            return $this->redirectToRoute('accueil');
        }

        //Ajoute un commentaire

        if ($request->isMethod('POST')) {
            $data = $request->request->all();

            $avis = (new Avis())
                ->setEmail($data['email'])
                ->setContenu($data['contenu'])
                ->setRgpd(1)
                ->setCreateAt(new \DateTime())
                ->setUsers($profil);

            $em->persist($avis);
            $em->flush();

            $this->addFlash('success', 'Avis Ajouté !');
            return $this->redirectToRoute('profil', ['id' => $profil->getId()]);
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
                return $this->redirectToRoute('profil', ['id' => $profil->getId()]);
            }
        }




        return $this->render('liste/profil.html.twig', [
            'profil' => $profil,
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
    public function annonce($id, Request $request)
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

        // Traitement du bouton ça m'interesse, on ajoute un utilisateur intéressé
        $action = $request->query->get('action');
        if ($action == 'add') {
            $annonce->addUserPostulant($this->getUser());
            $em->flush();

            $this->addFlash('success', "Votre intérêt a bien été enregistré");
            return $this->redirectToRoute('annonce', ['id' => $annonce->getId()]);

        // On retire l'utilisateur qui n'est plus intéressé par l'annonce lorsqu'il clique sur "ça ne m'interesse plus"
        } elseif ($action == 'remove') {
            $annonce->removeUserPostulant($this->getUser());
            $em->flush();

            $this->addFlash('success', "Votre retrait de l'annonce a bien été enregistré");
            return $this->redirectToRoute('annonce', ['id' => $annonce->getId()]);

        }


        return $this->render('liste/annonce.html.twig', [
            'annonce' => $annonce,
        ]);
    }

    /**
     * @Route("/annonce-crud/{id}", name="annonce_crud")
     */
    public function annonce_crud($id, Request $request, AnnoncesRepository $annoncesRepo)
    {

        $this->denyAccessUnlessGranted('ROLE_PUBLISHER');

        $em = $this->getDoctrine()->getManager();

        if ($id == 0) {
            if ($this->getUser()->hasRoles('ROLE_GOD')) {
                throw new Exception('Access denied');
            }

            $annonce = new Annonces();
            $nouveau = true;

        } else {
            $annonce = $annoncesRepo->find($id);
            if (!$annonce) {
                $this->addFlash('danger', "Cet annonce n'a pas été trouvé.");
                return $this->redirectToRoute('mon_compte');
            }

            // On vérifie si l'utilisateur à écrit l'annonce
            if (!$this->getUser()->hasRoles('ROLE_GOD')) {
                if ($annonce->getUser() != $this->getUser()) {
                    throw new Exception("C'est pas ton annonce");
                }
            }
            $nouveau = false;
        }


        // Supprimer un annonce
        $action = $request->query->get('action');
        if ($action == 'delete') {
            $annonce->setActive(0);
            $em->flush();

            $this->addFlash('danger', "L'annonce a bien été supprimé.");
            return $this->redirectToRoute('mon_compte');
        }


        $form = $this->createForm(CreerAnnonceType::class, $annonce);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $annonce = $form->getData();

            if ($nouveau) {
                $annonce
                    ->setDateCreation(new \DateTime())
                    ->setActive(1)
                    ->setUser($this->getUser());
            }

            $em->persist($annonce);
            $em->flush();

            $this->addFlash('success', "L'article a bien été " . ($nouveau ? 'créé' : 'modifié') . ".");
            return $this->redirectToRoute('mon_compte');
        }

        return $this->render('liste/annonce_crud.html.twig', [
            'form' => $form->createView(),
            'nouveau' => $nouveau,
            'annonce' => $annonce,
        ]);
    }
}
