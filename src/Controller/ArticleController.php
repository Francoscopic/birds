<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use App\Database\DatabaseAccess;
use App\Function\IndexFunction;
use App\Validation\SigninValidation;

class ArticleController extends AbstractController
{
    protected bool $article_found = false;
    protected string $article_message = 'Found article';

    #[Route('/a/{post_id}', name: 'note_posts')]
    public function article_start(string $post_id, Request $request): Response
    {

        // Profile data
        $login = new SigninValidation();
        $login_state = $login->alright($login->page_state);
        $uid = $login_state['uid'];
        $visitor_state = $login_state['visit'];

        $link = $this->generateUrl('note_posts', ['post_id'=>$post_id], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->article_found = $this->article_validate_post_id($post_id);

        if($this->article_found === false ) {
            $this->article_message = 'We could not find your article';
            // Show the report.
        }
        
        $canvas = array(
            'notes' => array(),
            'profile' => array(
                'uid' => $uid,
                'visitor_state' => $visitor_state,
                'message' => $this->article_message,
            ),
            'misc' => array(),
        );

        # WORK
            # Details for the Note, itself: FUNCTION = GET_MY_NOTE()
                $get_note_result_array = IndexFunction::get_my_note($post_id);
                $uid_poster = $get_note_result_array['poster_id']; # uid
                $pid_note = $get_note_result_array['post_id']; # pid
                $note_title = stripslashes($get_note_result_array['title']); # title
                $note_note = $get_note_result_array['note']; # note
                $note_cover = IndexFunction::note_cover_article($get_note_result_array['cover'], '../../');
                $note_extensions = IndexFunction::note_cover_extensions($get_note_result_array['cover'], $get_note_result_array['extensions'])['images'];
                $note_date = $get_note_result_array['date']; # date posted of article

                $note_font = IndexFunction::note_font_family($get_note_result_array['font']);
                $note_theme = $get_note_result_array['theme'];

                $cover_width = IndexFunction::imgNomenclature($note_cover)['width'];
                $cover_height = IndexFunction::imgNomenclature($note_cover)['height'];
            #

            # Details of the Noter, themselves: FUNCTION = GET_NOTE_POSTER()
                $get_note_poster = IndexFunction::get_note_poster($uid_poster);

                $note_poster_name = $get_note_poster['name'];
                $note_poster_username = $get_note_poster['username'];
                $note_poster_display = $get_note_poster['display'];
            #

            # Details of Viewer
                $viewer_array = ($visitor_state == true) ? IndexFunction::get_note_poster(false) : IndexFunction::get_note_poster($uid);
                $name = $viewer_array['name'];
                $username = $viewer_array['username'];
                $display = IndexFunction::note_cover($viewer_array['display'], 'profile', 'pages', 'small');
            #

            // Divide

            # Save
                $get_noted = IndexFunction::save_like_verb('saves', $uid, $uid_poster, $pid_note, 'save');
                $save_icon = $get_noted['icon'];
                $save_checked = $get_noted['check'];
            #
            # Like
                $get_like = IndexFunction::save_like_verb('likes', $uid, $uid_poster, $pid_note, 'like');
                $like_icon = $get_like['icon'];
                $like_checked = $get_like['check'];

                $get_unlike = IndexFunction::save_like_verb('unlikes', $uid, $uid_poster, $pid_note, 'unlike');
                $unlike_icon = $get_unlike['icon'];
                $unlike_checked = $get_unlike['check'];
            #
            # Liked Number
                $liked_number = IndexFunction::verb_number($pid_note, 'likes')['number'];
            #
            # Subscribe
                # Get the subscribe state between the user and people
                $subscribe_state = IndexFunction::get_subscribe_state($uid_poster, $uid);
                $state_variables = IndexFunction::subscribe_state_variables($subscribe_state);
                $subs_text  = $state_variables['title'];
                $subs_state = $state_variables['state'];

                # Get the number of subscribers Author has
                $subscribe_followers = IndexFunction::subscribes($uid_poster, 'followers');

                # Get the number of NOTES posted
                $note_posted = IndexFunction::get_number_of_notes($uid_poster);
            #
            # Comments
                // Let's get the number of comments
                $comment_number = IndexFunction::get_comments_number($pid_note, 'number')[0];
            #

            // Divide 2

            $encode_url   = urlencode($link);
            $url_facebook = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode( $this->article_share_url($link, ['media'=>'facebook', 't'=>$note_title]) );
            $url_twitter  = 'https://twitter.com/intent/tweet?url=' . urlencode( $this->article_share_url($link, ['media'=>'twitter', 'text'=>$note_title]) );
            $url_linkedin = 'https://www.linkedin.com/shareArticle/?mini=true&url=' . urlencode( $this->article_share_url($link, ['media'=>'linkedin']) );
            $url_link     = $this->article_share_url($link, ['media'=>'link']);
        # WORK - END

        // $canvas['notes'][] = [
        //     'pid'          => $the_pid,
        //     'puid'         => $poster_uid,
        //     'title'        => $note_title,
        //     'paragraphs'   => $note_parags,
        //     'cover'        => $note_cover,
        //     'note_is_img'  => $note_state_is_image,
        //     'date'         => $note_date,
        //     'poster_name'  => $note_poster_name,
        //     'poster_uname' => $note_poster_uname,
        //     'if_view'      => $view_eye,
        //     'save'         => $save_state,
        //     'like'         => $like_state,
        //     'unlike'       => $unlike_state,
        //     'post_url'     => $article_url,
        //     'profile_url'  => $profile_url,
        // ];


        return $this->render('pages/in/article.html.twig', [
            'canvas' => $canvas,
        ]);
    }

    protected function article_block_a($pid, $pid_note) 
    {

        $content = '';

        # Database Access
        $connection_verb = new DatabaseAccess();
        $connection_verb = $connection_verb->connect('verb');

        $stmt = $connection_verb->prepare('SELECT uid, cid FROM comments WHERE pid = ? ORDER BY sid DESC LIMIT 7');
        $stmt->bind_param("s", $pid_note);
        $stmt->execute();
        $get_result = $stmt->get_result();

        while( $get_result_row = $get_result->fetch_array(MYSQLI_ASSOC) ) {

            # Instantiate the variables.
                $comment_id = $get_result_row['cid'];
                $comment_poster_uid = $get_result_row['uid'];
            #
            # Get the user i.e. commenter
                $commenter_row = get_comment_poster($comment_poster_uid);
                $comment_poster = $commenter_row['name'];
            #
            # Get the comment
                $comments_row = get_comment($comment_id);
                $comment_comment = $comments_row['comment'];
            #

            $content .='
            <li id="article-note-comment-park" class="nu-li ft-sect">
                <a class="a" href="comments.php?wp='. $pid .'">
                    <strong>'. $comment_poster .'</strong>
                    <span>'. $comment_comment .'</span>
                </a>
            </li>';
        }
        return $content;
    }

    protected function article_block_b($uid)
    {

        $content = '';

        # Database Access
        $connection_verb = new DatabaseAccess();
        $connection_verb = $connection_verb->connect('verb');

        $stmt = $connection_verb->prepare("SELECT DISTINCT(pid) FROM views WHERE access=1 AND uid != ? ORDER BY sid DESC LIMIT 1, 10");
        $stmt->execute();
        $get_result = $stmt->get_result();
        while( $get_rows = $get_result->fetch_array(MYSQLI_ASSOC) )
        {
            # Get post and my details
                $the_pid = $get_rows['pid'];
            #
            # Instantiate acting variables
                $my_note_row = get_this_note($the_pid);
                $note_parags = $my_note_row['paragraphs'];

                $get_note_result_array = get_my_note($the_pid);
                $poster_uid = $get_note_result_array['poster_id']; # uid
                $note_title = stripslashes($get_note_result_array['title']); # title
                $note_cover = note_cover($get_note_result_array['cover'], 'notes');
            #
            $note_poster_name = get_me($poster_uid)['name'];
            # View details
                $if_view  = get_if_views($the_pid, $uid);
                $view_eye = ($if_view == true) ? '' : '*';
            #
            $content .= '
            <div class="artHist">
                <span id="page-assistant" class="hd" pid="'. $the_pid .'" uid="'. $uid .'"></span>
                <a href="article.php?wp='. $the_pid .'" class="vw-anchor-pages artHist-a a">
                    <div class="artHist-display lozad rad4 bck fwl" data-background-image="'. $note_cover .'">

                    </div>
                    <div class="artHist-title fwl">
                        <div>
                            <p>'. $note_poster_name .'</p>
                            <h1 class="artHist-h1 trn3-color">'. $view_eye .' '. ShowMore($note_title, 14) .'</h1>
                            <p>'. $note_parags .' paragraphs</p>
                        </div>
                    </div>
                </a>
            </div>';
        }
        return $content;
    }

    protected function article_share_url($url, array $params) 
    {
        foreach($params as $key => $value) {
            $url .= '&'.$key.'='.$value;
        }
        return $url;
    }

    protected static function article_validate_post_id($post_id)
    {
        return ( IndexFunction::GET_validate($post_id) === true ) ? true : false;
    }
}


?>
