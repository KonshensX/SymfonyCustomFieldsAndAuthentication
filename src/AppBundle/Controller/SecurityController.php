<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @param AuthenticationUtils $authenticationUtils
     * @return \Symfony\Component\HttpFoundation\Response
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
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function registerAction (Request $request) {

        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        if ($form->isSubmitted() && $form->isValid()) {
            dump($request->request);
        }

        return $this->render('AppBundle:Security:register.html.twig', [
            'form'      => $form->createView()
        ]);
    }


    /**
     * @Route("/logout", name="logout")
     * @param Request $request
     */
    public function logoutAction (Request $request) {

    }

}
