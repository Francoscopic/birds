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


class CommentController extends AbstractController
{
    private bool $article_found     = false;
    private string $article_message = 'Found article';
    private $conn;

    #[Route('/c/{post_id}/', name: 'note_comment')]
    public function index(string $post_id, Connection $connection): Response
    {
        $this->conn = $connection;
        // Profile data
        $login          = new SigninValidation($this->conn);
        $login_state    = $login->alright($login->page_state);
        $uid            = $login_state['uid'];
        $visitor_state  = $login_state['visit'];
        $intruder_state = $login_state['intruder'];

        // data
        $link = $this->generateUrl('note_posts', ['post_id'=>$post_id], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->article_found = IndexFunction::article_validate_post_id($this->conn, $post_id);
        $theme_data = IndexFunction::get_user_state($this->conn, $uid, $visitor_state);

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
        $content = array();

        foreach($this->conn->iterateAssociativeIndexed(
            'SELECT id, uid, cid FROM verb_comments WHERE pid = ? ORDER BY id DESC LIMIT 50', [$pid_note]) 
            as $id => $data
        ) {
            # Instantiate the variables.
                $comment_id         = $data['cid'];
                $comment_poster_uid = $data['uid'];
            #
            # Get the user i.e. commenter
                $commenter_row  = IndexFunction::get_comment_poster($this->conn, $comment_poster_uid);
                $comment_poster = $commenter_row['name'];
                $comment_uname  = $commenter_row['username'];
            #
            # Get the comment
                $comments_row    = IndexFunction::get_comment($this->conn, $comment_id);
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
        $uid_poster = IndexFunction::get_poster_uid($this->conn, $pid)['uid'];
        $get_user_result_array = ($visitor_state == true) ? IndexFunction::retrieve_details($this->conn, false) : IndexFunction::retrieve_details($this->conn, $uid_poster);

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
        $viewer_array = ($visitor_state == true) ? IndexFunction::get_note_poster($this->conn, false) : IndexFunction::get_note_poster($this->conn, $uid);

        $content = [
            'name'     => $viewer_array['name'],
            'username' => $viewer_array['username'],
            'display'  => $viewer_array['display'],
        ];
        return $content;
    }

    protected function notes_article($pid)
    {
        $get_note_result_array = IndexFunction::get_my_note($this->conn, $pid);

        $article_url = $this->generateUrl('note_posts', array('post_id'=>$pid));

        $content = [
            'pid'   => $pid,
            'title' => $get_note_result_array['title'],
            'url'   => $article_url,
        ];
        return $content;
    }
}