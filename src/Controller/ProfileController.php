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

class ProfileController extends AbstractController
{
    private array $profile_found;
    private string $profile_message = 'Found account';
    private array $canvas = array();

    #[Route('/{user_name}/', name: 'note_profile')]
    public function index(string $user_name, Request $request): Response
    {
        // Profile data
        $login          = new SigninValidation();
        $login_state    = $login->alright($login->page_state);
        $uid            = $login_state['uid'];
        $visitor_state  = $login_state['visit'];
        $intruder_state = $login_state['intruder'];

        // data
        $link = $this->generateUrl('note_profile', ['user_name'=>$user_name], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->profile_found = IndexFunction::profile_check_username($user_name);
        $theme_data = IndexFunction::get_user_state($uid, $visitor_state);

        if( $intruder_state === true ) {
            $this->redirectToRoute('note_home');
        }

        if($this->profile_found['content'] === false) {
            $this->profile_message = $this->profile_found['message'];
            // Show the error report.
            $this->redirectToRoute('note_home'); // redirect, for now
        }
        
        $this->canvas = array(
            'notes' => [
                'nav_menu'  => array(),
                'profile'   => array(),
                'articles'  => array(),
                'check'     => $this->profile_found['content'],
            ],
            'profile' => array(
                'visitor_state' => $visitor_state,
                'message'       => $this->profile_message,
            ),
            'misc' => array(
                'outside'     => false,
                'theme_state' => $theme_data['state'],
                'theme_logo'  => $theme_data['logo'],
            ),
            'headers' => array(
                'title'       => 'Profile',
                'robot'       => false,
                'description' => '',
            ),
        );

        // Work
        $this->canvas['notes']['profile']  = $this->notes_profile($uid);
        $this->canvas['notes']['nav_menu'] = IndexFunction::profile_navigation('profile');
        $this->canvas['notes']['articles'] = $this->notes_articles($uid);

        return $this->render('pages/in/profile.html.twig', [
            'canvas' => $this->canvas,
        ]);
    }

    protected function notes_profile($uid)
    {
        $content = array();

        $get_user_figures_array = IndexFunction::profile_user_figures($uid);

        $username      = strtolower($get_user_figures_array['username']);
        $name          = $get_user_figures_array['name'];
        $state         = ($get_user_figures_array['state'] === 1) ? 'darkmode' : 'lightmode';
        $location      = $get_user_figures_array['location'];
        $website       = $get_user_figures_array['website'];
        $bio           = nl2br($get_user_figures_array['about']);
        $bio_forChange = trim($bio);
        $cover         = $get_user_figures_array['cover'];
        $display       = $get_user_figures_array['display'];

        $subs_number = IndexFunction::subscribes($uid, 'followers');   // the people who follow me
        $my_subs     = IndexFunction::subscribes($uid, 'following');    // the people I follow

        $content = [
            'username'  => $username,
            'name'      => $name,
            'state'     => $state,
            'location'  => $location,
            'website'   => $website,
            'about'     => $bio,
            'cover'     => $cover,
            'display'   => $display,
            'followers' => $subs_number,
            'following' => $my_subs,
        ];
        return $content;
    }

    protected function notes_articles($uid)
    {
        $content = array();

        $connection_sur = new DatabaseAccess();
        $connection_sur = $connection_sur->connect('sur');

        $stmt = $connection_sur->prepare("SELECT uid, pid FROM big_sur WHERE uid = ? AND access = 1 ORDER BY sid DESC LIMIT 15");
        $stmt->bind_param('s', $uid);
        $stmt->execute();
        $get_result = $stmt->get_result();
        $num_rows = $get_result->num_rows;

        while( $get_rows = $get_result->fetch_array(MYSQLI_ASSOC) ) {

            # Get post and my details
                $the_pid    = $get_rows['pid'];
                $poster_uid = $get_rows['uid'];
            #

            # Instantiate acting variables
                $my_note_row = IndexFunction::get_this_note($the_pid);
                $note_title  = stripslashes($my_note_row['title']);
                $note_parags = $my_note_row['paragraphs'];
                $note_cover  = IndexFunction::note_cover($my_note_row['cover'], 'notes');
                $note_state_article_or_image = ($my_note_row['state'] == 'art') ? false : true;
            #

            $get_note_poster_details = IndexFunction::get_me($poster_uid);
            $note_poster_name        = $get_note_poster_details['name'];
            $note_poster_uname       = $get_note_poster_details['username'];

            # Get me view details
                $if_view  = IndexFunction::get_if_views($the_pid, $uid);
                $view_eye = ($if_view === true) ? '*' : '';
            #
            $content[] = [
                'pid'             => $the_pid,
                'title'           => $note_title,
                'paragraphs'      => $note_parags,
                'cover'           => $note_cover,
                'note_is_img'     => $note_state_article_or_image,
                'poster_name'     => $note_poster_name,
                'poster_username' => $note_poster_uname,
                'if_view'         => $view_eye,
            ];
        }
        return $content;
    }
}


?>
