<?php

namespace App\Verb\Home;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Database\DatabaseAccess;
use App\Verb\Cookie\RetrieveCookie;
use App\Function\IndexFunction;


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
}