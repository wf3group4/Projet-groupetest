<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Service\EmailService;
use App\Security\UsersAuthenticator;
use App\Repository\UsersRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, UsersAuthenticator $authenticator, EmailService $emailService): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $now = new \DateTime();
            $user
                ->setCreatedAt($now)
                ->setUpdatedAt($now)
                ->setToken($this->generateToken())
                ->setActive(0)
                ->setRoles(["ROLE_PUBLISHER"])
                ->setAvatar("images/imageDefault.jpg")

            ;
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $emailService->register($user);

           $this->addFlash('success', "Inscription bien prise en compte. Clique sur le lien envoyé à l'email ".$user->getEmail());
           return $this->redirectToRoute('app_login');

            // return $guardHandler->authenticateUserAndHandleSuccess(
            //     $user,
            //     $request,
            //     $authenticator,
            //     'main' // firewall name in security.yaml
            // );
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('mon_compte');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    } 

    /**
     * @Route("/validation-email", name="validation_email")
     */
    public function validation_email(Request $request, UsersRepository $usersRepo)
    {   $error = false;
        //récupération GET email -token
        $email = $request->query->get('email');
        $token = $request->query->get('token');
        //chercher le usen BDD par son email
        $user = $usersRepo->findOneBy(array('email' =>$email));

       

            if(! $user){
            $error = "Votre adresse mail ne correspond à aucun compte";
            }elseif($user->getActive() == 1){
            $error = "Compte déjà valider wesh! ";
            }elseif($token != $user->getToken()){
            $error = "Une erreur est survenue. Contacter notre service com";
            }else{
            $user->setActive(1);
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success','Ton compte a bien été validé, tu peux te connecter');
            return $this->redirectToRoute('app_login');
            }
        
        
        return $this->render('emails/security/validation_emaill.html.twig', [
            'error' => $error,
        ]);
    }

    private function generateToken(){
        return bin2hex(random_bytes(21));
    }

    /**
     * @Route("/password-forgotten", name="password_forgotten")
     */
    public function password_forgotten(Request $request, UsersRepository $usersRepo, EmailService $emailService){
        if($request->isMethod('POST')){
            $email = $request->request->get('email');
            $user = $usersRepo->findOneBy( array('email'=> $email));
            
            if(!$user){
                $this->addFlash('danger', 'Adresse mail non trouvé');
            }else{
                $user->setToken($this->generateToken());
                $em = $this->getDoctrine()->getManager();
                $em->flush();

                $link = $this->generateUrl('password_update', ['email' => $user->getEmail(), 'token' => $user->getToken()],UrlGeneratorInterface::ABSOLUTE_URL );
                $emailService->password_forgotten($user, $link);
                $this->addFlash('success', "On t'as envoyer un email, va vite voir!");
                return $this->redirectToRoute('password_forgotten', ['send' => 'ok']);
            }
        }
        return $this->render('security/password_forgotten.html.twig');
    }
    
    /**
     * @Route("/password-update", name="password_update")
     */
    public function password_update(Request $request, UsersRepository $usersRepo, UserPasswordEncoderInterface $passwordEncoder){
        $user = $this->getUser();
        if(!$user){
            $email = $request->query->get('email');
            $token = $request->query->get('token');
        
            $user = $usersRepo->findOneBy( array('email' => $email));  
        
            if(!$user || $token != $user->getToken()){
                throw new \Exception("Hola mon mignon! Tu t'es égaré!");
            }
        }

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form 
            ->remove('name')
            ->remove('lastname')
            ->remove('email')
            ->remove('agreeTerms');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user
                ->setToken($this->generateToken())
                ->setUpdatedAt( new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', "Ton mot de passe a bien été modifier");
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('security/password_update.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
