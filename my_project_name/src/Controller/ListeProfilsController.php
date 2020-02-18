<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Users;
use App\Form\ContactProType;
use App\Repository\UsersRepository;
use App\Service\EmailService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ListeProfilsController extends AbstractController
{
    // Récupération de tous les profils
    /**
     * @Route("/liste-profils", name="liste-profils")
     */
    public function Profils(Request $request, PaginatorInterface $paginator, UsersRepository $usersRepo)
    {


        $query = $usersRepo->createQueryBuilder('b');

        $searchParNomOrPrenom = $request->query->get('search');
        if($searchParNomOrPrenom)
        {
            $query
                ->orWhere('b.Name LIKE :search')
                ->orWhere('b.Lastname LIKE :search')
                ->setParameter('search', "%$searchParNomOrPrenom%");

            $profil = $query
                ->getQuery()
                ->getResult();

            if(!$profil) {
                $this->addFlash('danger', 'Aucun résultat trouvée !!');

            } else {
                $this->addFlash('success', "Résultat trouvée !!");
            }

        }

        $profil = $paginator->paginate(
            $query, // Requête contenant les données à paginer
            $request->query->getInt('page',1), // Numéro de la page en cours, passé dans l'URL, si aucune page
            3
        );


        return $this->render('liste/LesProfils.html.twig', [
            'profiles' => $profil = $usersRepo->findAll(),
        ]);
    }

    // Quand l'utilisateur clique pour voir en détail un profil
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

        // Si une personne se trompe dans l'url ou autre cela redirige
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


    // Quand l'utilisateur après avoir regarder un profil veut contacter la personne en particulier
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
}
