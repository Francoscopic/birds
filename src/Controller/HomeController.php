<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

//
use App\Database\DatabaseAccess;
use App\Functions\IndexFunction;
use mysqli;

class HomeController extends AbstractController
{
    #[Route('/', name: 'note_home')]
    public function corpus(Connection $connection): Response
    {
        $uid = '6364fhhf';
        $connection_sur = new DatabaseAccess();
        $connection_sur = $connection_sur->connect('sur');

        // HERE
            $canvas = array(
                'notes' => array(),
                'profile' => array(
                    'visitor_state' => true,
                ),
            );
            $stmt = $connection_sur->prepare("SELECT uid, pid FROM big_sur WHERE access = 1 ORDER BY sid DESC LIMIT 3");
            $stmt->execute();
            $get_result = $stmt->get_result();
            $num_rows = $get_result->num_rows;
            // $get_rows = $get_result->fetch_array(MYSQLI_ASSOC);
            while( $get_rows = $get_result->fetch_array(MYSQLI_ASSOC) ) {
                # Get post and my details
                    $the_pid = $get_rows['pid'];
                    $poster_uid = $get_rows['uid'];
                #
                # Instantiate acting variables
                    $my_note_row = IndexFunction::get_this_note($the_pid);
                    $note_title  = stripslashes($my_note_row['title']);
                    $note_parags = $my_note_row['paragraphs'];
                    $note_cover  = IndexFunction::note_cover($my_note_row['cover'], 'notes', 'home');
                    $note_state_article_or_image = ($my_note_row['state'] == 'art') ? 'hd' : '';
                    $note_date   = IndexFunction::timeAgo($my_note_row['date']);
                #
                $get_me            = IndexFunction::get_me($poster_uid);
                $note_poster_name  = $get_me['name'];
                $note_poster_uname = $get_me['username'];
                # Get me view details
                    $if_view = IndexFunction::get_if_views($the_pid, $uid);
                    $view_eye = ($if_view === true) ? '*' : '';
                #
                # Get small_menu details
                    $small_menu_state = IndexFunction::small_menu_validations($the_pid, $uid);
                    $save_state = $small_menu_state['save'];
                    $like_state = $small_menu_state['like'];
                    $unlike_state = $small_menu_state['unlike'];
                #
                $article_url = $this->generateUrl('note_posts', array('post_id'=>$the_pid));
                $profile_url = $this->generateUrl('note_profiles', array('user_id'=>$note_poster_uname));
                // $article_url = 'note_posts';
                // $profile_url = 'note_profiles';

                $canvas['notes'][] = [
                    'pid'          => $the_pid,
                    'puid'         => $poster_uid,
                    'title'        => $note_title,
                    'paragraphs'   => $note_parags,
                    'cover'        => $note_cover,
                    'art_state'    => $note_state_article_or_image,
                    'date'         => $note_date,
                    'poster_name'  => $note_poster_name,
                    'poster_uname' => $note_poster_uname,
                    'if_view'      => $view_eye,
                    'save'         => $save_state,
                    'like'         => $like_state,
                    'unlike'       => $unlike_state,
                    'post_url'     => $article_url,
                    'profile_url'  => $profile_url,
                ];
                // */
            }
        // HERE - END

        return $this->render('pages/in/index.html.twig', [
            'canvas' => $canvas,
        ]);
    }

    #[Route('/posts/{post_id}', name: 'note_posts')]
    public function posts_show(string $post_id, Request $request): Response
    {
        $routeName = $request->attributes->get('_route');
        $routeParameters = $request->attributes->get('_route_params');

        // use this to get all the available attributes (not only routing ones):
        $allAttributes = $request->attributes->all();

        //
        return $this->render('pages/in/article.html.twig', [
            'canvas' => '',
        ]);
    }

    #[Route('/{user_id}', name: 'note_profiles')]
    public function profiles(string $user_id): Response
    {
        $contents = $this->renderView('pages/in/profiles.html.twig', [
            'user_name' => $user_id,
        ]);

        return new Response($contents);
    }

    #[Route('/about', name: 'note_about')]
    public function about(): Response
    {
        //
    }
}


?>
