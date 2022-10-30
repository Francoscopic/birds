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
                'profile'   => array(),
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

        return $this->render('pages/in/profile.html.twig', [
            'canvas' => $this->canvas,
        ]);
    }

    protected function article_block_comments($pid_note): array
    {
        $content = array();

        # Database Access
        $connection_verb = new DatabaseAccess();
        $connection_verb = $connection_verb->connect('verb');

        $stmt = $connection_verb->prepare('SELECT co.uid, co.cid, cl.date FROM comments co INNER JOIN comments_list cl WHERE co.cid=cl.cid AND co.pid = ? ORDER BY sid DESC LIMIT 7');
        $stmt->bind_param("s", $pid_note);
        $stmt->execute();
        $get_result = $stmt->get_result();

        while( $get_result_row = $get_result->fetch_array(MYSQLI_ASSOC) ) {

            # Instantiate the variables.
                $comment_id         = $get_result_row['cid'];
                $comment_poster_uid = $get_result_row['uid'];
                $comment_date       = IndexFunction::timeAgo($get_result_row['date']);
            #
            # Get the user i.e. commenter
                $commenter_row = IndexFunction::get_comment_poster($comment_poster_uid);
                $comment_poster = $commenter_row['name'];
            #
            # Get the comment
                $comments_row    = IndexFunction::get_comment($comment_id);
                $comment_comment = htmlspecialchars_decode($comments_row['comment']);
                $comment_url     = $this->generateUrl('note_comment', array('post_id'=>$pid_note));
            #

            $content[] = [
                'pid'         => $pid_note,
                'name'        => $comment_poster,
                'comment'     => $comment_comment,
                'comment_url' => $comment_url,
                'date'        => $comment_date,
            ];
        }
        return $content;
    }

    function block_a() {

        $content = '';
        global $connection_sur, $uid;

        $stmt = $connection_sur->prepare("SELECT uid, pid FROM big_sur WHERE uid = ? AND access = 1 ORDER BY sid DESC LIMIT 12");
        $stmt->bind_param('s', $uid);
        $stmt->execute();
        $get_result = $stmt->get_result();
        $num_rows = $get_result->num_rows;

        while( $get_rows = $get_result->fetch_array(MYSQLI_ASSOC) ) {

            # Get post and my details
                $the_pid = $get_rows['pid'];
                $poster_uid = $get_rows['uid'];
            #

            # Instantiate acting variables
                $my_note_row = get_this_note($the_pid);
                $note_title  = stripslashes($my_note_row['title']);
                $note_parags = $my_note_row['paragraphs'];
                $note_cover  = note_cover($my_note_row['cover'], 'notes');
                $note_state_article_or_image = ($my_note_row['state'] == 'art') ? 'hd' : '';
            #

            $note_poster_name  = get_me($poster_uid)['name'];
            $note_poster_uname = get_me($poster_uid)['username'];

            # Get me view details
                $if_view = get_if_views($the_pid, $uid);
                $view_eye = ($if_view === true) ? '*' : '';
            #
            $content .='
            <div class="nts-host relative">
                <span id="page-assistant" class="hd" page="notbase" pid="'. $the_pid .'" uid="'. $uid .'" read="'. PageRoutes('notbase', '?wp='. $the_pid)['article'] .'" title="'. $note_title .'" poster="'. $note_poster_name .'"></span>
                <a href="article.php?wp='. $the_pid .'" class="vw-anchor nts-host-anchor a">
                    <div class="nts-host-banner relative">
                        <div class="nts-host-display lozad rad2 bck" data-background-image="'. $note_cover .'">
                            <div class="nts-host-display-type nt-ui-rad4 ft-sect '. $note_state_article_or_image .'"><span>photo</span></div>
                            <div class="nts-host-display-filter"></div>
                        </div>
                    </div>
                    <div class="nts-host-verb ft-sect">
                        <p>
                            <strong title="Paragraphs">'. $note_parags .'</strong><span class=""> paragraphs</span>
                        </p>
                    </div>
                    <div id="nts-host-title" class="nts-host-title">
                        <p class="trn3-color">'. $view_eye .' '. ShowMore($note_title, 14) .'</p>
                    </div>
                </a>
                <div class="nts-host-verb-author ft-sect">
                    <a href="people.php?up='. $note_poster_uname .'" class="a">
                        <p>'. $note_poster_name .'</p>
                    </a>
                    <a href="#" class="a">
                        <button class="nts-show-menu-profiles no-bod"><i class="lg-i fa fa-ellipsis-v"></i></button>
                    </a>
                </div>
            </div>';
        }
        return $content;
    }
}


?>
