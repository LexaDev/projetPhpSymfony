<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
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
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }



    /**
     * @Route("/forgottenPwd", name="forgotten_pwd")
     */
    public function forgottenPwd(Request $request,
                                 EntityManagerInterface $em,
                                 \Swift_Mailer $mailer,
                                 TokenGeneratorInterface $tokenGenerator) : Response
    {
        if ($request->isMethod('POST')){
            $email = $request->get('email');
            $user = $em->getRepository(Participant::class)->findOneByEmail($email);
            if ($user === null){
                $this->addFlash('danger', 'Email inconnu.');
                return $this->redirectToRoute('app_login');
            }
            $token = $tokenGenerator->generateToken();

            try {
                $user->setResetToken($token);
                $em->flush();
            } catch (\Exception $e){
                $this->addFlash('danger', $e->getMessage());
                return $this->redirectToRoute('app_login');
            }
            $url = $this->generateUrl('reset_pwd', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
            $message = (new \Swift_Message('Mot de passe oublié'))
                ->setFrom('aleou-cheval@hotmail.fr')   //l'adresse mail de la societe
                ->setTo($user->getEmail())
                ->setBody('Voici le lien pour enregistrer un nouveau mot de passe : '. $url, 'text/html')
            ;
            //dd($message);
            $mailer->send($message);
            $this->addFlash('success', 'Mail envoyé (pensez à regarder dans vos spams)');
            return  $this->redirectToRoute('app_login');
        }

        return $this->render('security/forgotten_pwd.html.twig');
    }

    /**
     * @Route("/resetPwd/{token}", name="reset_pwd")
     */
    public function resetPwd(Request $request,
                             string $token,
                             EntityManagerInterface $em,
                             UserPasswordEncoderInterface $encoder)
    {
        if ($request->isMethod('POST')){
            $user = $em->getRepository(Participant::class)->findOneByResetToken($token);
            if ($user === null){
                $this->addFlash('danger', 'Token inconnu');
                return $this->redirectToRoute('app_login');
            }
            if ($request->get('password') !== $request->get('passwordRepeat')){
                $this->addFlash('danger', 'Les deux mot de passes ne sont pas identiques.');
                return $this->render('security/reset_pwd.html.twig', [
                    'token' => $token
                ]);
            }
            $user->setResetToken(null);
            $user->setPassword($encoder->encodePassword($user, $request->get('password')));
            $em->flush();
            $this->addFlash('success', 'Mot de passe mise à jour');
            return $this->redirectToRoute('app_login');
        } else {
            return $this->render('security/reset_pwd.html.twig', [
                'token' => $token
            ]);
        }
    }

    /**
     * @Route("/addadmin/{pseudo}")
     */
    public function addadmin($pseudo, ParticipantRepository $repository, EntityManagerInterface $em)
    {
        $user = $repository->findOneBy(['username' => $pseudo]);
        $user->setRoles(['ROLE_ADMIN']);
        $em->flush();
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/removeadmin/{pseudo}")
     */
    public function removeadmin($pseudo, ParticipantRepository $repository, EntityManagerInterface $em)
    {
        $user = $repository->findOneBy(['username' => $pseudo]);
        $user->setRoles([]);
        $user->setRoles(['ROLE_USER']);
        $em->flush();
        return $this->redirectToRoute('home');
    }

    //Methode pour encode un mot de passe et voir le resultat
    /**
     * @Route("/encode/{mdp}")
     */
    public function encode(UserPasswordEncoderInterface $encoder, $mdp){
        $participant = new Participant();
        $pass = $encoder->encodePassword($participant, $mdp);
        dd($pass);
    }
}
