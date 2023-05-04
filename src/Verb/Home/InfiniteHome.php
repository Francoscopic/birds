<?php

namespace App\Verb\Home;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Type;
use App\Verb\Cookie\RetrieveCookie;
use App\Vunction\IndexFunction;
use App\Validation\SigninValidation;


class InfiniteHome extends AbstractController
{
    private $conn;
    private $request;
    private $canvas;

    public function __construct(Connection $connection)
    {
        $this->conn = $connection;

        $this->request = Request::createFromGlobals();
    }

    public function more(): JsonResponse
    {
        # Profile data
        $login = new SigninValidation($this->conn);
        $login_state = $login->alright($login->page_state);
        $uid = $login_state['uid'];
        $visitor_state = $login_state['visit'];
        $intruder_state = $login_state['intruder'];

        $this->canvas = array(
            'notes' => array(),
            'profile' => array(
                'username' => '',
                'visitor_state' => $visitor_state,
            ),
            'misc' => array(
                'outside' => false,
            ),
        );

        if($this->request->request->get('grow_home') == 'home') {
            $this->grow_home($uid);
        }

        return $this->json([
            'message' => 'I see you',
            'content' => $this->canvas,
        ]);
    }

    protected function grow_home($uid)
    {
        $content = array();
        $current_position = intval($this->request->request->get('start'));

        foreach(
            $this->conn->iterateAssociativeIndexed('SELECT id, uid, pid FROM big_sur WHERE access = 1 ORDER BY id DESC LIMIT ?, 15', [$current_position], [Type::getType('integer')]) 
            as $id => $data
        )
        {
            # Get post and my details
                $the_pid    = $data['pid'];
                $poster_uid = $data['uid'];
            # Instantiate acting variables
                $my_note_row = IndexFunction::get_this_note($this->conn, $the_pid);
                $note_title  = stripslashes($my_note_row['title']);
                $note_parags = $my_note_row['paragraphs'];
                $note_cover  = IndexFunction::note_cover($my_note_row['cover'], 'notes');
                $note_state_is_image = ($my_note_row['state'] == 'art') ? false : true;
                $note_date   = IndexFunction::timeAgo($my_note_row['date']);
            $get_me            = IndexFunction::get_me($this->conn, $poster_uid);
            $note_poster_name  = $get_me['name'];
            $note_poster_uname = $get_me['username'];
            # Get me view details
                $if_view = IndexFunction::get_if_views($this->conn, $the_pid, $uid);
                $view_eye = ($if_view === true) ? '' : '*';
            # Get small_menu details
                $small_menu_state = IndexFunction::small_menu_validations($this->conn, $the_pid, $uid);
                $save_state = $small_menu_state['save'];
                $like_state = $small_menu_state['like'];
                $unlike_state = $small_menu_state['unlike'];
            $article_url = $this->generateUrl('note_posts', array('post_id'=>$the_pid));
            $profile_url = $this->generateUrl('note_profile', array('user_name'=>$note_poster_uname));

            $this->canvas['notes'][] = [
                'pid'          => $the_pid,
                'title'        => htmlspecialchars($note_title),
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
                'profile_url'  => $profile_url,
            ];
        }
        unset($uid, $current_position, $stmt, $get_result, $get_rows, $the_pid, $poster_uid, $my_note_row, $note_title, $note_parags, 
            $note_cover, $note_date, $note_poster_name, $note_poster_uname, $if_view, $view_eye
        );
    }
}