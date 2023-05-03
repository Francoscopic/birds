<?php

namespace App\Verb\Home;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Database\DatabaseAccess;
use App\Verb\Cookie\RetrieveCookie;
use App\Vunction\IndexFunction;


class ProfileVerbsHome extends AbstractController
{
    private $request;
    private $get_cookie;
    private $user_visitor_id;
    private $conn;
    
    public function __construct(Connection $connection)
    {
        $this->request = Request::createFromGlobals();

        $this->conn = $connection;

        $this->get_cookie = new RetrieveCookie();
        $this->user_visitor_id = $this->get_cookie->get_netintui_user_id()['user_id'];
    }

    public function index(): JsonResponse
    {
        if ( $this->request->request->has('hide_article') ) {

            $post_id         = $this->request->request->get('profile_pid');
            $user_visitor_id = $this->user_visitor_id;
    
            $this->make_note_hidden($post_id, $user_visitor_id);

            return $this->json([
                'message' => 'success',
            ]);
        }
        if ( $this->request->request->has('draft_delete') ) {

            $post_id         = $this->request->request->get('draft_pid');
            $user_visitor_id = $this->user_visitor_id;
    
            $this->draft_delete($post_id, $user_visitor_id);

            return $this->json([
                'message' => 'success',
                'data'    => 13,
            ]);
        }
        if ( $this->request->request->has('saved_remove') ) {
            
            $post_id = $this->request->request->get('saved_remove_pid');
            $user_visitor_id = $this->user_visitor_id;

            $this->saved_remove($post_id, $user_visitor_id);

            return $this->json([
                'message' => 'success',
                'data'    => 13,
            ]);
        }
        return $this->json([
            'message' => '[500] Something bad happened',
        ]);
    }

    protected function make_note_hidden($pid, $uid)
    {
        $this->conn->update('big_sur', ['access'=>0], ['pid'=>$pid, 'uid'=>$uid]);
        unset($pid, $uid);
    }

    protected function draft_delete($pid, $uid)
    {
        $this->conn->update('big_sur_draft', ['access'=>0], ['pid'=>$pid, 'uid'=>$uid]);
        unset($pid, $uid);
    }

    protected function saved_remove($pid, $uid)
    {
        $puid = IndexFunction::get_poster_uid($this->conn, $pid)['uid'];

        $this->conn->update('verb_saves', ['state'=>0], ['pid'=>$pid, 'puid'=>$puid, 'uid'=>$uid]);
        unset($pid, $puid, $uid);
    }
}