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

class HistoryController extends AbstractController
{
    private array $profile_found;
    private string $profile_message = 'Undiscovered';
    private array $canvas = array();
    private $conn;

    #[Route('/{user_name}/history/', name: 'note_history')]
    public function index(string $user_name, Connection $connection): Response
    {
        $this->conn = $connection;
        // Profile data
        $login          = new SigninValidation($this->conn);
        $login_state    = $login->alright($login->page_state);
        $uid            = $login_state['uid'];
        $visitor_state  = $login_state['visit'];
        $intruder_state = $login_state['intruder'];

        // data
        $this->profile_found = IndexFunction::profile_check_username($this->conn, $user_name);
        $theme_data = IndexFunction::get_user_state($this->conn, $uid, $visitor_state);

        // my profile or not
        $my_profile = ($this->profile_found['uid'] == $uid) ? true : false;

        if($intruder_state) {
            $this->redirectToRoute('note_home');
        }
        
        $this->canvas = array(
            'notes' => [
                'nav_menu'   => array(),
                'profile'    => array(),
                'articles'   => array(),
                'validation' => [
                    'check'      => $this->profile_found['state'],
                    'my_profile' => $my_profile,
                ],
            ],
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
                'title'       => '(History)',
                'robot'       => true,
                'description' => '',
            ),
        );

        if(!$this->profile_found['state']) {
            // We could not find user.
            return $this->render('pages/in/history.html.twig', [
                'canvas' => $this->canvas,
            ]);
        }

        // Work
        $ProfileFunction = new ProfileFunction($this->conn);
        $this->canvas['notes']['profile']   = $ProfileFunction->notes_profile($this->profile_found['uid']);
        $this->canvas['notes']['nav_menu']  = IndexFunction::profile_navigation('history');
        $this->canvas['notes']['articles']  = $ProfileFunction->notes_history($this->profile_found['uid']);

        $this->canvas['headers']['title'] = $this->canvas['notes']['profile']['name'] . ' - (History)';

        return $this->render('pages/in/history.html.twig', [
            'canvas' => $this->canvas,
        ]);
    }
}


?>
