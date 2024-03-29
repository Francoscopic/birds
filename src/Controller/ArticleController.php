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

class ArticleController extends AbstractController
{
    private bool $article_found = false;
    private string $article_message = 'Found article';

    #[Route('/a/{post_id}/', name: 'note_posts')]
    public function article_start(string $post_id, Connection $conn): Response
    {
        // Profile data
        $login = new SigninValidation($conn);
        $login_state = $login->alright($login->page_state);
        $uid = $login_state['uid'];
        $visitor_state  = $login_state['visit'];
        $intruder_state = $login_state['intruder'];

        // data
        $link = $this->generateUrl('note_posts', ['post_id'=>$post_id], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->article_found = IndexFunction::article_validate_post_id($conn, $post_id);
        $theme_data = IndexFunction::get_user_state($conn, $uid, $visitor_state);

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
                'user'          => $login_state['user'],
                'visitor_state' => $visitor_state,
                'message'       => $this->article_message,
            ),
            'misc' => array(
                'outside'     => false,
                'theme_state' => $theme_data['state'],
                'theme_logo'  => $theme_data['logo'],
            ),
            'headers' => array(
                'title'       => 'Article',
                'robot'       => false,
                'description' => '',
            ),
        );

        # WORK
            # Details for the Note, itself: FUNCTION = GET_MY_NOTE()
                $get_note_result_array = IndexFunction::get_my_note($conn, $post_id);
                $uid_poster            = $get_note_result_array['poster_id']; # uid
                $note_title            = stripslashes($get_note_result_array['title']); # title
                $note_note             = IndexFunction::cleanRead($get_note_result_array['note']); # note
                $note_description      = IndexFunction::ShowMore($note_note); # note
                $note_cover            = $get_note_result_array['cover_full'];
                $note_views            = IndexFunction::note_views($conn, $post_id) ?? 'No';
                $note_date             = IndexFunction::timeAgo($get_note_result_array['date']); # date posted of article
                $cover_area            = IndexFunction::imgNomenclature($note_cover);
                $cover_width           = $cover_area['width'];
                $cover_height          = $cover_area['height'];

                $comment_url           = $this->generateUrl('note_comment', array('post_id'=>$post_id));

                $canvas['notes']['article'] = [
                    'pid'          => $post_id,
                    'title'        => $note_title,
                    'body'         => $note_note,
                    'description'  => $note_description,
                    'cover'        => $note_cover,
                    'cover_width'  => $cover_width,
                    'cover_height' => $cover_height,
                    'views'        => $note_views,
                    'date'         => $note_date,
                    'comment_url'  => $comment_url,
                ];
                $canvas['headers']['title'] = $note_title;
            #

            # Details of the Noter, themselves: FUNCTION = GET_NOTE_POSTER()
                $get_note_poster = IndexFunction::get_note_poster($conn, $uid_poster);

                $note_poster_name                 = $get_note_poster['name'];
                $note_poster_username             = $get_note_poster['username'];
                $note_poster_display              = $get_note_poster['display'];
                $note_poster_username_profile_url = $this->generateUrl('note_profile', array('user_name'=>$note_poster_username));

                $canvas['notes']['poster'] = [
                    'name'        => $note_poster_name,
                    'username'    => $note_poster_username,
                    'display'     => $note_poster_display,
                    'profile_url' => $note_poster_username_profile_url,
                ];
            #

            # Details of Viewer
                $viewer_array        = ($visitor_state == true) ? IndexFunction::get_note_poster($conn, false) : IndexFunction::get_note_poster($conn, $uid);
                $name                = $viewer_array['name'];
                $username            = $viewer_array['username'];
                $display             = $viewer_array['display'];
                $note_posted         = IndexFunction::get_number_of_notes($conn, $uid_poster); # Get the number of NOTES posted
                $subscribe_followers = IndexFunction::subscribes($conn, $uid_poster, 'follower'); # Get the number of subscribers Author has

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
                    $get_noted = IndexFunction::save_like_verb($conn, 'saves', $uid, $uid_poster, $post_id, 'save');
                    $save_icon = $get_noted['icon'];
                    $save_checked = $get_noted['check'];
                #
                # Like
                    $get_like = IndexFunction::save_like_verb($conn, 'likes', $uid, $uid_poster, $post_id, 'like');
                    $like_icon = $get_like['icon'];
                    $like_checked = $get_like['check'];
                #
                # Liked Number
                    $liked_number = IndexFunction::verb_number($conn, $post_id, 'likes')['number'];
                #
                # Subscribe
                    # Get the subscribe state between the user and people
                    $subscribe_state = IndexFunction::get_subscribe_state($conn, $uid_poster, $uid);
                    $state_variables = IndexFunction::subscribe_state_variables($subscribe_state);
                    $subs_text  = $state_variables['title'];
                    $subs_state = $state_variables['state'];
                #
                # Comments
                    // Let's get the number of comments
                    $comment_number = IndexFunction::get_comments_number($conn, $post_id, 'number')[0];
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
                $canvas['notes']['comment'] = $this->article_block_comments($conn, $post_id);
            #
            # MORE 
                $canvas['notes']['note_more'] =  $this->article_block_readmore($conn, $uid, $post_id);
            #

            // Divide 2

            $url_facebook = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode( $this->article_share_url($link, ['media'=>'facebook']) );
            $url_twitter  = 'https://twitter.com/intent/tweet?url=' . urlencode( $this->article_share_url($link, ['media'=>'twitter']) );
            $url_linkedin = 'https://www.linkedin.com/shareArticle/?mini=true&url=' . urlencode( $this->article_share_url($link, ['media'=>'linkedin']) );
            $url_link     = $this->article_share_url($link, ['media'=>'link']);

            $canvas['notes']['share'] = [
                'facebook' => $url_facebook,
                'twitter'  => $url_twitter,
                'linkedin' => $url_linkedin,
                'web'      => $url_link,
            ];
        # WORK - END


        return $this->render('pages/in/article.html.twig', [
            'canvas' => $canvas,
        ]);
    }

    protected function article_block_comments($conn, $pid_note): array
    {
        $content = array();

        foreach($conn->iterateAssociativeIndexed(
            'SELECT co.id, co.uid, co.cid, cl.date FROM verb_comments co 
            INNER JOIN verb_comments_list cl WHERE co.cid=cl.cid 
            AND co.pid = ? ORDER BY id DESC LIMIT 7', [$pid_note]) 
            as $id => $data
        ) {

            # Instantiate the variables.
                $comment_id         = $data['cid'];
                $comment_poster_uid = $data['uid'];
                $comment_date       = IndexFunction::timeAgo($data['date']);
            #
            # Get the user i.e. commenter
                $commenter_row = IndexFunction::get_comment_poster($conn, $comment_poster_uid);
                $commenter_name = $commenter_row['name'];
                $commenter_username = $commenter_row['username'];

            #
            # Get the comment
                $comments_row    = IndexFunction::get_comment($conn, $comment_id);
                $comment_comment = htmlspecialchars_decode($comments_row['comment']);
                $commenter_profile = $this->generateUrl('note_profile', array('user_name'=>$commenter_username));
            #

            $content[] = [
                'pid'         => $pid_note,
                'name'        => $commenter_name,
                'comment'     => $comment_comment,
                'profile'     => $commenter_profile,
                'date'        => $comment_date,
            ];
        }
        return $content;
    }

    protected function article_block_readmore($conn, $uid, $post_id)
    {
        $content = array();

        foreach(
            $conn->iterateKeyValue('SELECT id, pid FROM big_sur WHERE access = 1 AND pid != ? ORDER BY id DESC LIMIT 10', [$post_id]) 
            as $id => $the_pid
        )
        {
            # Instantiate acting variables
                $rows1 = IndexFunction::get_this_note($conn, $the_pid);
                $note_parags = $rows1['paragraphs'];

                $rows2 = IndexFunction::get_my_note($conn, $the_pid);
                $poster_uid = $rows2['poster_id']; # uid
                $note_title = IndexFunction::ShowMore(stripslashes($rows2['title']), 14); # title
                $note_cover = $rows2['cover'];
            #
            $note_poster_name = IndexFunction::get_me($conn, $poster_uid)['name'];
            # View details
                $if_view  = IndexFunction::get_if_views($conn, $the_pid, $uid);
                $view_eye = ($if_view == true) ? '' : '*';
            #
            $article_url = $this->generateUrl('note_posts', array('post_id'=>$the_pid));

            $content[] = [
                'pid'        => $the_pid,
                'title'      => $note_title,
                'cover'      => $note_cover,
                'name'       => $note_poster_name,
                'eye'        => $view_eye,
                'paragraphs' => $note_parags,
                'post_url'   => $article_url,
            ];
        }
        return $content;
    }

    protected function article_share_url($url, array $params)
    {
        $url .= '?';
        foreach($params as $key => $value) {
            $url .= $key.'='.$value.'&';
        }
        return $url;
    }
}


?>
