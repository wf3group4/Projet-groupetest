<?php

namespace App\Controller;

use App\Form\ModifCompteType;
use App\Form\PortfolioType;
use App\Form\SignalementType;
use App\Form\ContactProType;
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
use App\Entity\Facture;
use App\Service\EmailService;
use DateTime;
use Exception;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="accueil")
     */
    public function accueil(UsersRepository $usersRepo, AnnoncesRepository $annoncesRepo, PortfolioRepository $portfolioRepo)
    {
        $personnes = $usersRepo->getLastUser(); //cherche les dernieres personnes
        $annonces = $annoncesRepo->getLastAnnonces(3); //cherche les 3 dernieres annonces à poster dans l'accueil
        $portfolios = $portfolioRepo->getLastPortfolios(9); //cherche les 9 dernieres arts à poster dans l'accueil

        return $this->render('main/index.html.twig', [
            'personnes' => $personnes,
            'annonces' => $annonces,
            'portfolios' => $portfolios,
        ]);
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
        Request $request
    ) {
        $user = $userRepo->find($id);
        $annonces = $annoncesRepo->getUserAnnonces($id);
        $liens = $portfolioRepo->getUserLiens($id);
        $avis = $avisRepo->getUserAvis($id);

        if (!$user) {
            $this->addFlash('danger', "Le profil demandé n'a pas été trouvé.");

            return $this->redirectToRoute('accueil');
        }

        $em = $this->getDoctrine()->getManager();
        $user->setVues($user->getVues() + 1);

        $em->flush();

        //Ajout de liens/images au portfolio
        $portfolios = $portfolioRepo->getUserLastPortfolio($id);

        $new_image = new Portfolio();
        $form = $this->createForm(PortfolioType::class, $new_image);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $portfolios = $form->getData()
                ->setUser($user);

            $file = $form['img_url']->getData();
            if ($file) {
                $repertoire = $this->getParameter('images');
                $nameOfPicture = 'portfolio-' . uniqid() . '.' . $file->guessExtension();
                $file->move($repertoire, $nameOfPicture);
                $portfolios->setImgUrl($nameOfPicture);
            }
            $em->persist($portfolios);
            $em->flush();

            $this->addFlash('success', 'Les réalisations on bien été modifiées');

            return $this->redirectToRoute('mon_compte', [
                'id' => $id,
            ]);
        }

        //Supprimer l'image
        $action = $request->query->get('action');
        if ($action && $action == 'delete-img') {
            $id_img = $request->query->get('id_img');
            $portfolios = $portfolioRepo->find($id_img);
            $portfolios->setImgUrl(null);
            $em->flush();
            $this->addFlash('danger', "L'image a bien été supprimé.");

            return $this->redirectToRoute('mon_compte', [
                'id' => $id,
            ]);
        }

        //Supprimer le lien
        $action = $request->query->get('action');
        if ($action && $action == 'delete-lien') {
            $id_lien = $request->query->get('id_lien');
            $liens = $portfolioRepo->find($id_lien);
            $liens->setLiens(null);
            $em->flush();
            $this->addFlash('danger', 'Le lien a bien été supprimé.');

            return $this->redirectToRoute('mon_compte', [
                'id' => $id,
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
                return $this->redirectToRoute('mon_compte', [
                    'id' => $id
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
            'moyenne' => $moyenne,
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

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData()
                ->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('password')->getData()
                    )
                )
                ->setUpdatedAt(new \DateTime());

            $file = $form['avatar']->getData();
            if ($file) {
                $repertoire = $this->getParameter('images');
                $nameOfPicture = 'avatar-'.rand(1, 99999).'.'.$file->guessExtension();
                $file->move($repertoire, $nameOfPicture);
                $user->setAvatar($nameOfPicture);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Le profil a bien été modifié.');

            return $this->redirectToRoute('mon_compte', [
                'id' => $user->getId(),
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
        PortfolioRepository $portfolioRepo
    ) {
        $user = $userRepo->find($id);
        $portfolios = $portfolioRepo->getUserPortfolios($id);

        return $this->render('main/portfolio.html.twig', [
            'id' => $id,
            'user' => $user,
            'portfolios' => $portfolios,
        ]);
    }

    /**
     * @Route("/portfolio/image/{id}", name="image")
     */
        public function image(
        $id,
        Request $request,
        UsersRepository $userRepo,
        PortfolioRepository $portfolioRepo
    ) {
        $id_image = $request->query->get('id');
        $user_id = $request->query->get('user');
        $img_url = $portfolioRepo->find($id)->getImgUrl();
        return $this->render('main/image.html.twig', [
            'img_url' => $img_url,
            'user' =>$user_id
        ]);
    }


    /**
     * @Route("/signalement/{id}", name="signalement")
     */
    public function signalement(
        $id,
        UsersRepository $userRepo,
        AnnoncesRepository $annonceRepo,
        Request $request
    ) {
        //Récupération des variables
        $cible = $request->query->get('cible');
        $user = $userRepo->find($id);
        $annonce = $annonceRepo->find($id);
        $em = $this->getDoctrine()->getManager();

        //Création du signalement
        $signalement = new Signalement();

        $form = $this->createForm(SignalementType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $signalement = $form->getData();

            if ($cible == 'user') {
                $signalement
                    ->setUser($user)
                    ->setDate(new \DateTime())
                    ->setActive(1);

                $em->persist($signalement);
                $em->flush();

                $this->addFlash('success', 'Votre signalement à bien été envoyé !');

                return $this->redirectToRoute('mon_compte', [
                    'id' => $id
                ]);
            } else {
                $signalement
                    ->setAnnonce($annonce)
                    ->setDate(new \DateTime())
                    ->setActive(1);
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
            'annonce' => $annonce,
            'cible' => $cible,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/mes_candidatures/{id}", name="mes_candidatures")
     */
    public function mes_candidatures($id, UsersRepository $userRepo, AnnoncesRepository $annoncesRepo, Request $request)
    {
        // On récupère l'utilisateur
        $user = $userRepo->find($id);

        // On récupère les annonces auquel l'utilisateur a postulé
        $annonces_postule = $user->getAnnoncesPostule();

        // On récupère les annonces pour lesquels l'utilisateur a été séléctionné comme prestataire
        $candidature_valide = $user->getAnnoncesPrestataire();

        // On récupère le paramètre action en url pour savoit si le bouton 'déclarer un projet' a été cliqué
        $action = $request->query->get('action');

        $em = $this->getDoctrine()->getManager();

        if ($this->getUser() != $user) {
            throw new \Exception('Vous devez être connecté');

            return $this->redirectToRoute('accueil');
        }

        if ($action == 'projet_fini') {
            $annonce = $annoncesRepo->find($request->query->get('annonce_id'));

            $annonce->setActive(3);
            $em->flush();

            return $this->redirectToRoute('mes_candidatures', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('main/mes_candidatures.html.twig', [
            'user' => $user,
            'annonces_postule' => $annonces_postule,
            'candidature_valide' => $candidature_valide,
        ]);
    }

    /**
     * @Route("/mes_annonces/{id}", name="mes_annonces")
     */
    public function mes_annonces($id, UsersRepository $userRepo, AnnoncesRepository $annoncesRepo, Request $request, EmailService $emailService)
    {
        $user = $userRepo->find($id);
        $annonces = $annoncesRepo->getUserAnnonces($id);

        // On récupère les annonces pour lesquels l'utilisateur a été séléctionné comme prestataire
        $candidature_valide = $user->getAnnoncesPrestataire();

        $action = $request->query->get('action');
        $em = $this->getDoctrine()->getManager();

        if ($this->getUser() != $user) {
            throw new \Exception('Vous devez être connecté');
            $this->redirectToRoute('accueil');
        }

        if ($action == 'paiement_valide') {
            $annonce = $annoncesRepo->find($request->query->get('annonce_id'));
            $annonce->setActive(4)
                ->setClosedAt(new DateTime());
            $prestataire = $annonce->getPrestataire();
            $prestataire->setCommission($prestataire->getCommission() + ($annonce->getPrix() * 0.1));
            $em->flush();

            $emailService->envoi_facture($user, $annonce);

            return $this->redirectToRoute('mes_annonces', [
                'id' => $user->getId()
            ]);
        }

        return $this->render('main/mes_annonces.html.twig', [
            'user' => $user,
            'annonces' => $annonces,
            'candidature_valide' => $candidature_valide,
        ]);
    }

    /**
     * @Route("/nous-contacter", name="nous_contacter")
     */
    public function contac(Request $request, EmailService $emailService
    ) {
        $contactProtype = $this->createForm(ContactProType::class);
        if (
            $request->isMethod('POST')
        ) {
            $params = $request->request->all();
            $emailService->contact_webmaster($params['contact_pro']);
            $this->addFlash('success', 'votre message a bien été envoyé.');

            return $this->redirectToRoute('accueil');
        }

        return $this->render('pied/nous_contacter.html.twig', [
            'form' => $contactProtype->createView(),
        ]);
    }

    /**
     * @Route("/apropos", name="aPropos")
     */
    public function apropos()
    {
        return $this->render('pied/apropos.html.twig'
            );
    }

    /**
     * @Route("/mentions-legales", name="mentions_legales")
     */
    public function mentions_legales()
    {
        return $this->render('pied/mentions_legales.html.twig'
            );
    }

    /**
     * @Route("/recrutement", name="recrutement")
     */
    public function recrutement()
    {
        return $this->render('pied/recrutement.html.twig'
            );
    }
}
