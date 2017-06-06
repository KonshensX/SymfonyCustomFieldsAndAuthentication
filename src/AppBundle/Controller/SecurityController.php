<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Tests\Authentication\Provider\UserAuthenticationProviderTest;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class SecurityController extends Controller
{


    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastusername = $authenticationUtils->getLastUsername();

        return $this->render('AppBundle:Security:login.html.twig', array(
            'username'  => $lastusername,
            'error'     => $error
        ));
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param PasswordEncoderInterface|UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function registerAction (Request $request, UserPasswordEncoderInterface $encoder) {
        $em = $this->getDoctrine()->getManager();
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));

            $em->persist($user);
            $em->flush();
            // Log the user in after registering
            $userToken = new UsernamePasswordToken($user, null, 'main', $user->getRoles());

            $securityContext = new TokenStorage();
            $securityContext->setToken($userToken);

            $event = new InteractiveLoginEvent($request, $userToken);
            $eventDispatcher = new EventDispatcher();
            $eventDispatcher->dispatch('security.interactive_login', $event);

            return $this->redirect('/');
        }

        return $this->render('AppBundle:Security:register.html.twig', [
            'form'      => $form->createView()
        ]);
    }


    /**
     * @Route("/attempt", name="haha")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @internal param AuthenticationManagerInterface $authenticationManagerInterface
     */
    public function attemptAction (Request $request, ManagerRegistry $managerRegistry) {
        try {
            $eventDispatcher = new EventDispatcher();
            // Get the user from the database
            $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy([
                'username' => 'admin'
            ]);


            // Log the user in after registering
            $userToken = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());
            // I need an authentication manager?

            // $provider = new EntityUserProvider($managerRegistry, User::class);
            $provider = new UserAuthenticationProviderTest();
            $authenticationManager = new AuthenticationProviderManager($provider);
            $authenticatedToken = $authenticationManager->authenticate($userToken);
            $securityContext = new TokenStorage();
            $securityContext->setToken($authenticatedToken);

            $event = new InteractiveLoginEvent($request, $userToken);
            $eventDispatcher->dispatch('security.interactive_login', $event);

            return $this->redirectToRoute('homepage');
        } catch (Exception $e) {
            var_dump($e);
        }

    }


    /**
     * @Route("/logout", name="logout")
     * @param Request $request
     */
    public function logoutAction (Request $request) {

    }


}
