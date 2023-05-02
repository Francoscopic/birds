<?php

namespace App\Controller;

// use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Database\DatabaseAccess;
use App\Vunction\IndexFunction;
use App\Validation\SigninValidation;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SigninController extends AbstractController
{

    #[Route('/o/signin/', name: 'note_signin')]
    // public function signin(): Response
    public function signin(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $canvas = array(
            'notes'   => array(),
            'profile' => array(
                'username'      => 'signin-visitor',
                'visitor_state' => true,
            ),
            'misc' => array(
                'outside'     => true,
                'theme_state' => '',
                'theme_logo'  => '',
            ),
            'headers' => array(
                'title' => 'Login',
                'robot' => false,
                'description' => 'Notes, from Netintui, is a platform to share great ideas in seven paragraphs or less. It\'s never been so simple to share great ideas, inspire and touch lives with writing.',
            ),
        );

        return $this->render('pages/in/signin.html.twig', [
            'canvas' => $canvas,
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
}
