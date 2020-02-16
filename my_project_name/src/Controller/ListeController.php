<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\Users;
use App\Entity\Avis;
use App\Entity\Tags;
use App\Form\CreerAnnonceType;
use App\Repository\AnnoncesRepository;
use App\Form\ContactProType;

use App\Repository\TagsRepository;
use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;


use App\Repository\UsersRepository;
use Exception;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ListeController extends AbstractController
{
    /**
     * @Route("/liste-profils", name="liste-profils")
     */
    public function Profils(Request $request, PaginatorInterface $paginator)
    {
        $em = $this->getDoctrine()->getManager();
        $usersRepo = $em->getRepository(Users::class)->findAll();

        $profil = $paginator->paginate(
            $usersRepo, // Requête contenant les données à paginer
            $request->query->getInt('page',1), // Numéro de la page en cours, passé dans l'URL, si aucune page
            3
        );


        $search = $request->query->get('search');
            if ($search)
            {
                $profil = $usersRepo->SearchByName($search);

                if(!$profil)
                {
                    $this->addFlash('danger', 'Aucun résultat trouvé');
                    return $this->redirectToRoute('liste-profils');
                }


                    $this->addFlash('success', 'Résultat trouvée !');

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
        if (!$profil) {
            $this->addFlash('danger', "Le profil demandé n'a pas été trouvé.");
            return $this->redirectToRoute('accueil');
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

            if($request->isMethod("POST"))
            {
                $params = $request->request->all();
//                dump($params);die;
                $emailService->ContactProfil($params['contact_pro']);

                $this->addFlash('success', 'Votre message à bien été envoyé !');

                return $this->redirectToRoute('mon_compte', [
                    'id' => $id
                ]);

            }



        return $this->render('liste/contactProfil.html.twig',[
            'profil' => $profil,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/annonces", name="annonces")
     */
    public function annonces(AnnoncesRepository $annoncesRepo,Request $request, TagsRepository $tagsRepo, PaginatorInterface $paginator)
    {

        $query = $annoncesRepo->createQueryBuilder('a')
            ->addSelect('a', 't')
            ->leftJoin('a.tag', 't')
            ->andWhere('a.active = 1');





        $searchParNom = $request->query->get('titre');
        if($searchParNom)
        {
            $query
                ->orWhere('a.titre LIKE :titre')
                ->setParameter('titre', "%$searchParNom%");

        }

        $tri = $request->query->get('ordre');
        if($tri)
        {
            $query
                ->orderBy('a.prix', "$tri");

        }

        $tag = $request->query->get('tag');
        if ($tag) {
            $query
                ->andWhere('t.nom = :nom')
                ->setParameter('nom', $tag);

        }

        $annonces = $query
            ->getQuery()
            ->getResult();

        $annonces = $paginator->paginate(
            $query,
            $request->query->getInt('page',1),
            5
        );

        return $this->render('liste/annonces.html.twig', [
            'annonces' => $annonces,
            'tags' => $tags = $tagsRepo->findAll(),
        ]);
    }

    /**
     * @Route("/annonce/{id}", name="annonce")
     */
    public function annonce($id, Request $request, AnnoncesRepository $annoncesRepo, UsersRepository $usersRepo, EmailService $emailService)
    {
        
        // On récupère l' AnnoncesRepository
        $em = $this->getDoctrine()->getManager();
        $annoncesRepo = $em->getRepository(Annonces::class);
        // On récupère l'annonce, en fonction de l'ID qui est dans l'URL
        $annonce = $annoncesRepo->find($id);

        $annonce->setVues($annonce->getVues()+1);

        $em->flush();


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

        // Traitement du bouton Choisir cet artiste
        $action = $request->query->get('action');

        if ($action == 'add-prestataire') {

            $user_id = $request->query->get('user_id');
            $user_choisi = $usersRepo->find($user_id);   
            $annonce
                ->setPrestataire($user_choisi)
                ->setActive(2)
                ;
            $em->flush();

            $link = $this->generateUrl('annonce', ['id' => $annonce->getId()],UrlGeneratorInterface::ABSOLUTE_URL );
            $emailService->choix_prestataire($user_choisi, $link);

            $this->addFlash('success', "Votre choix a bien été enregistré");
            return $this->redirectToRoute('annonce', ['id' => $annonce->getId()]);

        // Bouton pour changer d'artiste qui permet de remettre le prestataire à 'null'
        } elseif ($action == 'remove-prestataire') {
            $annonce->setPrestataire(null);

            $em->flush();

            $this->addFlash('success', "Le retrait de l'artiste a bien été enregistré");
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
        $user_id = $this->getUser()->getId();
        $this->denyAccessUnlessGranted('ROLE_PUBLISHER');

        $em = $this->getDoctrine()->getManager();

        if ($id == 0) {
            if ($this->getUser()->hasRoles('ROLE_ADMIN')) {
                throw new Exception('Access denied');
            }

            $annonce = new Annonces();
            $nouveau = true;

        } else {
            $annonce = $annoncesRepo->find($id);
            if (!$annonce) {
                $this->addFlash('danger', "Cet annonce n'a pas été trouvé.");
                return $this->redirectToRoute('mon_compte', [
                    'id' =>  $user_id
                ]);
            }

            // On vérifie si l'utilisateur à écrit l'annonce
            if (!$this->getUser()->hasRoles('ROLE_ADMIN')) {
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
            return $this->redirectToRoute('mon_compte', [
                'id' => $user_id
            ]);
        }


        $form = $this->createForm(CreerAnnonceType::class, $annonce);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $annonce = $form->getData();

            if ($nouveau) {
                $annonce
                    ->setDateCreation(new \DateTime())
                    ->setActive(1)
                    ->setVues(0)
                    ->setUser($this->getUser());
            }

            $em->persist($annonce);
            $em->flush();

            $this->addFlash('success', "L'annonce a bien été " . ($nouveau ? 'créé' : 'modifié') . ".");
            return $this->redirectToRoute('mon_compte', [
                'id' => $user_id
            ]);
        }

        return $this->render('liste/annonce_crud.html.twig', [
            'form' => $form->createView(),
            'nouveau' => $nouveau,
            'annonce' => $annonce,
        ]);
    }
}
