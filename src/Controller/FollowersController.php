<?php

namespace App\Controller;

// use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use App\Database\DatabaseAccess;
use App\Function\IndexFunction;
use App\Validation\SigninValidation;

use App\Function\ProfileFunction;

class FollowersController extends AbstractController
{
    private array $profile_found;
    private string $profile_message = 'Found account';
    private array $canvas = array();

    #[Route('/{user_name}/followers/', name: 'note_followers')]
    public function index(string $user_name, Request $request): Response
    {
        // Profile data
        $login          = new SigninValidation();
        $login_state    = $login->alright($login->page_state);
        $uid            = $login_state['uid'];
        $visitor_state  = $login_state['visit'];
        $intruder_state = $login_state['intruder'];

        // data
        $this->profile_found = IndexFunction::profile_check_username($user_name);
        $theme_data = IndexFunction::get_user_state($uid, $visitor_state);

        // my profile or not
        $my_profile = ($this->profile_found['uid'] == $uid) ? true : false;

        if( $intruder_state === true ) {
            $this->redirectToRoute('note_home');
        }

        if($this->profile_found['content'] === false) {
            $this->profile_message = $this->profile_found['message'];
            // Show the error report.
            $this->redirectToRoute('note_home'); // redirect, for now
        }

        if($my_profile == false) {
            $uid = $this->profile_found['uid'];
        }
        
        $this->canvas = array(
            'notes' => [
                'nav_menu'   => array(),
                'profile'    => array(),
                'subscribe'  => array(),
                'follows'    => array(),
                'validation' => [
                    'check'      => $this->profile_found['content'],
                    'my_profile' => $my_profile,
                ],
            ],
            'profile' => array(
                'user'          => $login_state['user'],
                'visitor_state' => $visitor_state,
                'message'       => $this->profile_message,
            ),
            'misc' => array(
                'outside'     => false,
                'theme_state' => $theme_data['state'],
                'theme_logo'  => $theme_data['logo'],
            ),
            'headers' => array(
                'title'       => 'Followers',
                'robot'       => true,
                'description' => 'Check out the subscribers of your blog',
            ),
        );

        // Work
        $ProfileFunction = new ProfileFunction();
        $this->canvas['notes']['profile']           = $ProfileFunction->notes_profile($uid);
        $this->canvas['notes']['nav_menu']          = IndexFunction::profile_navigation('profile');
        $this->canvas['notes']['follows']           = $ProfileFunction->notes_follows($uid);

        $this->canvas['headers']['title'] = $this->canvas['notes']['profile']['name'] . ' - ' . '(Followers)';

        return $this->render('pages/in/followers.html.twig', [
            'canvas' => $this->canvas,
        ]);
    }
}


?>
