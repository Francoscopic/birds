<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Vunction\IndexFunction;
use App\Validation\SigninValidation;

class HomeController extends AbstractController
{
    private $conn;
    #[Route('/', name: 'note_home')]
    public function index(Connection $connection): Response
    {
        $this->conn = $connection;
        # Profile data
        $login          = new SigninValidation($connection);
        $login_state    = $login->alright($login->page_state);
        $uid            = $login_state['uid'];
        $visitor_state  = $login_state['visit'];
        $intruder_state = $login_state['intruder'];

        if( $intruder_state == true ) {
            $this->redirectToRoute('note_signin');
        }

        // data
        $theme_data = IndexFunction::get_user_state($connection, $uid, $visitor_state);

        $canvas = array(
            'notes' => array(),
            'profile' => array(
                'user'          => $login_state['user'],
                'message'       => '[200] Welcome',
                'visitor_state' => $visitor_state,
            ),
            'misc' => array(
                'outside'     => false,
                'load_more'   => null,
                'theme_state' => $theme_data['state'],
                'theme_logo'  => $theme_data['logo'],
            ),
            'headers' => array(
                'title'       => 'Home',
                'robot'       => false,
                'description' => 'Notes helps you share, educate and inspire with writing. And do it in just 7 paragraphs. It\'s for the creators who write.',
            ),
        );

        # Work
        $articles_list_home          = $this->articles_list_home($uid, $connection);
        $canvas['notes']             = $articles_list_home['article'];
        $canvas['misc']['load_more'] = $articles_list_home['more'];

        return $this->render('/pages/in/index.html.twig', [
            'canvas' => $canvas,
        ]);
    }

    protected function articles_list_home($uid, $conn)
    {
        $num_rows = 0;
        foreach($conn->iterateAssociativeIndexed(
            'SELECT uid, pid FROM big_sur WHERE access = 1 ORDER BY id DESC LIMIT 15', [], [])
            as $uid => $data
        ) {
            $num_rows++;
            # Get post and my details
                $the_pid    = $data['pid'];
                $poster_uid = $uid;
            #
            # Instantiate acting variables
                $aa                  = IndexFunction::get_this_note($conn, $the_pid);
                $note_title          = stripslashes($aa['title']);
                $note_parags         = $aa['paragraphs'];
                $note_cover          = IndexFunction::note_cover($aa['cover'], 'notes');
                $note_state_is_image = ($aa['state'] == 'art') ? false : true;
                $note_date           = IndexFunction::timeAgo($aa['date']);
            #
            $ab                = IndexFunction::get_me($conn, $poster_uid);
            $note_poster_name  = $ab['name'];
            $note_poster_uname = $ab['username'];
            # Get me view details
                $if_view = IndexFunction::get_if_views($conn, $the_pid, $uid);
                $view_eye = ($if_view == true) ? '*' : '';
            #
            # Get small_menu details
                $small_menu_state = IndexFunction::small_menu_validations($conn, $the_pid, $uid);
                $save_state       = $small_menu_state['save'];
                $like_state       = $small_menu_state['like'];
                $unlike_state     = $small_menu_state['unlike'];
            #
            $article_url = $this->generateUrl('note_posts', array('post_id'=>$the_pid));
            $article_url_absolute = $this->generateUrl('note_posts', ['post_id'=>$the_pid], UrlGeneratorInterface::ABSOLUTE_URL);
            $profile_url = $this->generateUrl('note_profile', array('user_name'=>$note_poster_uname));

            $content[] = [
                'pid'          => $the_pid,
                'title'        => $note_title,
                'paragraphs'   => $note_parags,
                'cover'        => $note_cover,
                'note_is_img'  => $note_state_is_image,
                'date'         => $note_date,
                'poster_name'  => $note_poster_name,
                'poster_uname' => $note_poster_uname,
                'if_view'      => $view_eye,
                'save'         => $save_state,
                'like'         => $like_state,
                'unlike'       => $unlike_state,
                'post_url'     => $article_url,
                'post_link'    => $article_url_absolute,
                'profile_url'  => $profile_url,
            ];
        }
        $show_load_more = ($num_rows == 15) ? true : false;
        return [
            'article' => $content,
            'more'    => $show_load_more
        ];
    }

    // OTHERS

    #[Route('/about/', name: 'note_about')]
    public function about(): Response
    {
        return $this->render('');
    }

    #[Route('/support/forgot_password/', name: 'note_forgot_password')]
    public function forgot_password(): Response
    {
        return $this->render('');
    }

    #[Route('/signup/', name: 'note_signup')]
    public function signup(): Response
    {
        return $this->render('');
    }

    #[Route('/support/', name: 'note_support')]
    public function support(): Response
    {
        return $this->render('');
    }
}


?>
