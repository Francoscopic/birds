<?php

namespace App\Verb\Home;

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
    
    public function __construct()
    {
        $this->request = Request::createFromGlobals();

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
        // Database Access
        $connection_sur = new DatabaseAccess();
        $connection_sur = $connection_sur->connect('sur');

        $stmt = $connection_sur->prepare('UPDATE big_sur SET access=0 WHERE pid=? AND uid=?');
        $stmt->bind_param('ss', $pid, $uid);
        $stmt->execute();
        unset($connection_sur, $stmt, $pid, $uid);
    }

    protected function draft_delete($pid, $uid)
    {
        // Database Access
        $connection_sur = new DatabaseAccess();
        $connection_sur = $connection_sur->connect('sur');

        $stmt = $connection_sur->prepare('UPDATE big_sur_draft SET access=0 WHERE pid=? AND uid=?');
        $stmt->bind_param('ss', $pid, $uid);
        $stmt->execute();
        unset($connection_sur, $stmt, $pid, $uid);
    }

    protected function saved_remove($pid, $uid)
    {
        // Database access
        $connection_verb = new DatabaseAccess();
        $connection_verb = $connection_verb->connect('verb');

        $puid = indexFunction::get_poster_uid($pid)['uid'];

        $stmt = $connection_verb->prepare('UPDATE saves SET state=0 WHERE pid=? AND puid=? AND uid=?');
        $stmt->bind_param('sss', $pid, $puid, $uid);
        $stmt->execute();
        unset($connection_verb, $stmt, $pid, $puid, $uid);
    }
}