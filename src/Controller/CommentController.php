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


class CommentController extends AbstractController
{
    private bool $article_found     = false;
    private string $article_message = 'Found article';

    #[Route('/c/{post_id}/', name: 'note_comment')]
    public function index(string $post_id, Request $request): Response
    {
        // Profile data
        $login          = new SigninValidation();
        $login_state    = $login->alright($login->page_state);
        $uid            = $login_state['uid'];
        $visitor_state  = $login_state['visit'];
        $intruder_state = $login_state['intruder'];

        // data
        $link = $this->generateUrl('note_posts', ['post_id'=>$post_id], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->article_found = IndexFunction::article_validate_post_id($post_id);
        $theme_data = IndexFunction::get_user_state($uid, $visitor_state);

        if( $intruder_state === true ) {
            $this->redirectToRoute('note_home');
        }

        if( $this->article_found === false ) {
            $this->article_message = 'We could not find your article';
            // Show the error report.
            $this->redirectToRoute('note_home'); // redirect, for now
        }

        $canvas = array(
            'notes' => array(
                'article'   => array(),
                'poster'    => array(),
                'viewer'    => array(),
                'comment'   => array(),
            ),
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
                'title'       => 'Comment',
                'robot'       => true,
                'description' => '',
            ),
        );

        $canvas['notes']['article']  = $this->notes_article($post_id);
        $canvas['notes']['poster']   = $this->notes_poster($post_id, $visitor_state);
        $canvas['notes']['viewer']   = $this->notes_viewer($uid, $visitor_state);
        $canvas['notes']['comment']  = $this->notes_comment($post_id);

        // modify headers
        $canvas['headers']['title'] = $this->notes_article($post_id)['title'];

        return $this->render('/pages/in/comment.html.twig', [
            'canvas' => $canvas,
        ]);
    }

    protected function notes_comment($pid_note)
    {
        $connection_verb = new DatabaseAccess();
        $connection_verb = $connection_verb->connect('verb');

        $content = array();

        $stmt = $connection_verb->prepare('SELECT uid, cid FROM comments WHERE pid = ? ORDER BY sid DESC LIMIT 200');
        $stmt->bind_param("s", $pid_note);
        $stmt->execute();
        $get_result = $stmt->get_result();
        while( $get_result_row = $get_result->fetch_array(MYSQLI_ASSOC) ) {

            # Instantiate the variables.
                $comment_id         = $get_result_row['cid'];
                $comment_poster_uid = $get_result_row['uid'];
            #
            # Get the user i.e. commenter
                $commenter_row  = IndexFunction::get_comment_poster($comment_poster_uid);
                $comment_poster = $commenter_row['name'];
                $comment_uname  = $commenter_row['username'];
            #
            # Get the comment
                $comments_row    = IndexFunction::get_comment($comment_id);
                $comment_comment = $comments_row['comment'];
                $comment_date    = $comments_row['date'];
            #

            $profile_url = $this->generateUrl('note_profile', array('user_name'=>$comment_uname));

            $content[] = [
                'name'        => $comment_poster,
                'comment'     => $comment_comment,
                'profile_url' => $profile_url,
                'date'        => IndexFunction::timeAgo($comment_date),
            ];
        }
        return $content;
    }

    protected function notes_poster($pid, $visitor_state)
    {
        $uid_poster = IndexFunction::get_poster_uid($pid)['uid'];
        $get_user_result_array = ($visitor_state == true) ? IndexFunction::retrieve_details(false) : IndexFunction::retrieve_details($uid_poster);

        $content = [
            'username' => $get_user_result_array['username'],
            'name'     => $get_user_result_array['name'],
            'state'    => $get_user_result_array['state'] === 1 ? 'darkmode' : 'lightmode',
            'display'  => $get_user_result_array['display'],
        ];
        return $content;
    }

    protected function notes_viewer($uid, $visitor_state)
    {
        $viewer_array = ($visitor_state == true) ? IndexFunction::get_note_poster(false) : IndexFunction::get_note_poster($uid);

        $content = [
            'name'     => $viewer_array['name'],
            'username' => $viewer_array['username'],
            'display'  => $viewer_array['display'],
        ];
        return $content;
    }

    protected function notes_article($pid)
    {
        $get_note_result_array = IndexFunction::get_my_note($pid);

        $article_url = $this->generateUrl('note_posts', array('post_id'=>$pid));

        $content = [
            'pid'   => $pid,
            'title' => $get_note_result_array['title'],
            'url'   => $article_url,
        ];
        return $content;
    }
}