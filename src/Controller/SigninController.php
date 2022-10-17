<?php

namespace App\Controller;

// use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Database\DatabaseAccess;
use App\Function\IndexFunction;
use App\Validation\SigninValidation;

class SigninController extends AbstractController
{

    #[Route('/o/signin/', name: 'note_signin')]
    public function signin(): Response
    {
        # Profile data
        $login = new SigninValidation();
        $login_state = $login->alright($login->page_state);
        $uid = $login_state['uid'];
        $visitor_state = $login_state['visit'];

        $canvas = array(
            'notes' => array(),
            'profile' => array(
                'username' => 'Joshua',
                'visitor_state' => $visitor_state,
            ),
            'misc' => array(
                'outside' => true,
            ),
        );

        return $this->render('pages/in/signin.html.twig', [
            'canvas' => $canvas,
        ]);
    }
}
