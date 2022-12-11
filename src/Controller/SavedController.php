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

class SavedController extends AbstractController
{
    private array $profile_found;
    private string $profile_message = 'Undiscovered';
    private array $canvas = array();

    #[Route('/{user_name}/saved/', name: 'note_saved')]
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
                'title'       => '(Saved)',
                'robot'       => false,
                'description' => 'See the articles you saved.',
            ),
        );

        if(!$this->profile_found['state']) {
            // We could not find user.
            return $this->render('pages/in/saved.html.twig', [
                'canvas' => $this->canvas,
            ]);
        }

        // Work
        $ProfileFunction = new ProfileFunction();
        $this->canvas['notes']['profile']   = $ProfileFunction->notes_profile($this->profile_found['uid']);
        $this->canvas['notes']['nav_menu']  = IndexFunction::profile_navigation('saved');
        $this->canvas['notes']['articles']  = $ProfileFunction->notes_saved($this->profile_found['uid']);

        $this->canvas['headers']['title'] = $this->canvas['notes']['profile']['name'] . ' - (Saved)';

        return $this->render('pages/in/saved.html.twig', [
            'canvas' => $this->canvas,
        ]);
    }
}


?>
