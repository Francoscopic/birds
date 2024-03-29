<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use App\Vunction\IndexFunction;
use App\Validation\SigninValidation;

use App\Vunction\ProfileFunction;

class DeskController extends AbstractController
{
    private array $canvas = array();
    private $conn;

    #[Route('/desk/', name: 'note_write')]
    public function index(Request $request, Connection $connection): Response
    {
        $this->conn = $connection;
        // Profile data
        $login          = new SigninValidation($this->conn);
        $login_state    = $login->alright($login->page_state);
        $uid            = $login_state['uid'];
        $visitor_state  = $login_state['visit'];
        $intruder_state = $login_state['intruder'];

        // theme
        $theme_data = IndexFunction::get_user_state($this->conn, $uid, $visitor_state);
        
        $this->canvas = array(
            'notes' => array(),
            'profile' => array(
                'user'          => $login_state['user'],
                'visitor_state' => $visitor_state,
                'message'       => 'Account was not found',
            ),
            'misc' => array(
                'outside'     => false,
                'theme_state' => $theme_data['state'],
                'theme_logo'  => $theme_data['logo'],
            ),
            'headers' => array(
                'title'       => '(Desk)',
                'robot'       => true,
                'description' => 'Share your ideas, the old way.',
            ),
        );

        // Work
        // here

        return $this->render('pages/in/desk.html.twig', [
            'canvas' => $this->canvas,
        ]);
    }
}


?>
