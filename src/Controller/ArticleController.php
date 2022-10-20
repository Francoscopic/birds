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
        $intruder_state = $login_state['intruder'];

        // data
        $link = $this->generateUrl('note_posts', ['post_id'=>$post_id], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->article_found = $this->article_validate_post_id($post_id);
        $theme_data = IndexFunction::get_user_state($uid, $visitor_state);

        if( $intruder_state == true ) {
            $this->redirectToRoute('note_home');
        }

        if($this->article_found === false ) {
            $this->article_message = 'We could not find your article';
            // Show the error report.
            $this->redirectToRoute('note_home'); // redirect, for now
        }
        
        $canvas = array(
            'notes' => [
                'article'   => array(),
                'poster'    => array(),
                'viewer'    => array(),
                'reaction'  => array(),
                'link'      => array(),
                'comment'   => array(),
                'note_more' => array(),
            ],
            'profile' => array(
                'visitor_state' => $visitor_state,
                'message'       => $this->article_message,
            ),
            'misc' => array(
                'outside'     => false,
                'theme_state' => $theme_data['state'],
                'theme_logo'  => $theme_data['logo'],
            ),
            'headers' => array(
                'title'       => 'Home',
                'robot'       => false,
                'description' => '',
            ),
        );

        # WORK
            # Details for the Note, itself: FUNCTION = GET_MY_NOTE()
                $get_note_result_array = IndexFunction::get_my_note($post_id);
                $uid_poster = $get_note_result_array['poster_id']; # uid
                $note_title = stripslashes($get_note_result_array['title']); # title
                $note_note = IndexFunction::cleanRead($get_note_result_array['note']); # note
                $note_description = IndexFunction::ShowMore($note_note); # note
                $note_cover = IndexFunction::note_cover_article($get_note_result_array['cover'], '../../');
                $note_extensions = IndexFunction::note_cover_extensions($get_note_result_array['cover'], $get_note_result_array['extensions'])['images'];
                $note_views = IndexFunction::note_views($post_id);
                $note_date = IndexFunction::timeAgo($get_note_result_array['date']); # date posted of article

                $cover_width = IndexFunction::imgNomenclature($note_cover)['width'];
                $cover_height = IndexFunction::imgNomenclature($note_cover)['height'];

                $canvas['notes']['article'] = [
                    'pid'          => $post_id,
                    'title'        => $note_title,
                    'body'         => $note_note,
                    'description'  => $note_description,
                    'cover'        => $note_cover,
                    'cover_width'  => $cover_width,
                    'cover_height' => $cover_height,
                    'extensions'   => $note_extensions,
                    'views'        => $note_views,
                    'date'         => $note_date,
                ];
            #

            # Details of the Noter, themselves: FUNCTION = GET_NOTE_POSTER()
                $get_note_poster = IndexFunction::get_note_poster($uid_poster);

                $note_poster_name = $get_note_poster['name'];
                $note_poster_username = $get_note_poster['username'];
                $note_poster_display = $get_note_poster['display'];

                $canvas['notes']['poster'] = [
                    'name'     => $note_poster_name,
                    'username' => $note_poster_username,
                    'display'  => $note_poster_display,
                ];
            #

            # Details of Viewer
                $viewer_array = ($visitor_state == true) ? IndexFunction::get_note_poster(false) : IndexFunction::get_note_poster($uid);
                $name = $viewer_array['name'];
                $username = $viewer_array['username'];
                $display = IndexFunction::note_cover($viewer_array['display'], 'profile', 'pages', 'small');
                $note_posted = IndexFunction::get_number_of_notes($uid_poster); # Get the number of NOTES posted
                $subscribe_followers = IndexFunction::subscribes($uid_poster, 'followers'); # Get the number of subscribers Author has

                $canvas['notes']['viewer'] = [
                    'name'        => $name,
                    'username'    => $username,
                    'display'     => $display,
                    'note_posted' => $note_posted,
                    'subscribers' => $subscribe_followers,
                ];
            #

            // Divide

            # VERBS
                # Save
                    $get_noted = IndexFunction::save_like_verb('saves', $uid, $uid_poster, $post_id, 'save');
                    $save_icon = $get_noted['icon'];
                    $save_checked = $get_noted['check'];
                #
                # Like
                    $get_like = IndexFunction::save_like_verb('likes', $uid, $uid_poster, $post_id, 'like');
                    $like_icon = $get_like['icon'];
                    $like_checked = $get_like['check'];
                #
                # Liked Number
                    $liked_number = IndexFunction::verb_number($post_id, 'likes')['number'];
                #
                # Subscribe
                    # Get the subscribe state between the user and people
                    $subscribe_state = IndexFunction::get_subscribe_state($uid_poster, $uid);
                    $state_variables = IndexFunction::subscribe_state_variables($subscribe_state);
                    $subs_text  = $state_variables['title'];
                    $subs_state = $state_variables['state'];
                #
                # Comments
                    // Let's get the number of comments
                    $comment_number = IndexFunction::get_comments_number($post_id, 'number')[0];
                #

                $canvas['notes']['reaction'] = [
                    'save_icon'       => $save_icon,
                    'save_checked'    => $save_checked,

                    'like_icon'       => $like_icon,
                    'like_checked'    => $like_checked,
                    'like_number'     => $liked_number,

                    'subscribe_text'  => $subs_text,
                    'subscribe_state' => $subs_state,

                    'comment_number'  => $comment_number,
                ];
            #

            // Divide
            
            # COMMENT
                $canvas['notes']['comment'] = $this->article_block_comments($post_id);
            #
            # MORE 
                $canvas['notes']['note_more'] =  $this->article_block_readmore($uid, $post_id);
            #

            // Divide 2

            $encode_url   = urlencode($link);
            $url_facebook = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode( $this->article_share_url($link, ['media'=>'facebook', 't'=>$note_title]) );
            $url_twitter  = 'https://twitter.com/intent/tweet?url=' . urlencode( $this->article_share_url($link, ['media'=>'twitter', 'text'=>$note_title]) );
            $url_linkedin = 'https://www.linkedin.com/shareArticle/?mini=true&url=' . urlencode( $this->article_share_url($link, ['media'=>'linkedin']) );
            $url_link     = $this->article_share_url($link, ['media'=>'link']);

            $canvas['notes']['share'] = [
                'facebook' => $url_facebook,
                'twitter' => $url_twitter,
                'linkedin' => $url_linkedin,
                'web' => $url_link,
            ];
        # WORK - END


        return $this->render('pages/in/article.html.twig', [
            'canvas' => $canvas,
        ]);
    }

    protected function article_block_comments($pid_note): array
    {

        $content = array();

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

            $content[] = [
                'pid'     => $pid_note,
                'name'    => $comment_poster,
                'comment' => $comment_comment,
            ];
        }
        return $content;
    }

    protected function article_block_readmore($uid, $post_id)
    {
        $content = array();

        # Database Access
        $connection_sur = new DatabaseAccess();
        $connection_sur = $connection_sur->connect('sur');

        // $stmt = $connection_verb->prepare("SELECT DISTINCT(pid) FROM views WHERE access=1 AND uid = ? ORDER BY sid DESC LIMIT 1, 10");
        $stmt = $connection_sur->prepare("SELECT pid FROM big_sur WHERE access = 1 AND pid != ? ORDER BY sid DESC LIMIT 10");
        $stmt->bind_param("s", $post_id);
        $stmt->execute();
        $get_result = $stmt->get_result();
        while( $get_rows = $get_result->fetch_array(MYSQLI_ASSOC) )
        {
            # Get post and my details
                $the_pid = $get_rows['pid'];
            #
            # Instantiate acting variables
                $my_note_row = IndexFunction::get_this_note($the_pid);
                $note_parags = $my_note_row['paragraphs'];

                $get_note_result_array = IndexFunction::get_my_note($the_pid);
                $poster_uid = $get_note_result_array['poster_id']; # uid
                $note_title = IndexFunction::ShowMore(stripslashes($get_note_result_array['title']), 14); # title
                $note_cover = IndexFunction::note_cover($get_note_result_array['cover'], 'notes');
            #
            $note_poster_name = IndexFunction::get_me($poster_uid)['name'];
            # View details
                $if_view  = IndexFunction::get_if_views($the_pid, $uid);
                $view_eye = ($if_view == true) ? '' : '*';
            #

            $content[] = [
                'pid'        => $the_pid,
                'title'      => $note_title,
                'cover'      => $note_cover,
                'name'       => $note_poster_name,
                'eye'        => $view_eye,
                'paragraphs' => $note_parags,
            ];
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
