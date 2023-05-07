<?php

namespace App\Verb\Home;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Vunction\IndexFunction;
use App\Verb\Cookie\RetrieveCookie;


class ArticleViewsHome extends AbstractController
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

    public function views(): JsonResponse
    {
        if ( $this->request->request->has('outShare') ) {

            $post_id         = $this->request->request->get('outshare_pid');
            $user_visitor_id = $this->user_visitor_id;
            $media           = $this->request->request->get('inshare_media');
            $media           = ($media == '') ? 'inhouse' : $media;
            $share_id        = IndexFunction::randomKey(9);
    
            $this->save_outshares($share_id, $post_id, $user_visitor_id, $media);

            return $this->json([
                'message' => 'I see you',
            ]);
        }
    
        if ( $this->request->request->has('inShare') ) {

            $post_id         = $this->request->request->get('inshare_pid');
            $user_visitor_id = $this->user_visitor_id;
            $media           = $this->request->request->get('inshare_media', 'inhouse');
            $visit_id        = IndexFunction::randomKey(9);
    
            $this->save_inshares($visit_id, $post_id, $user_visitor_id, $media);

            return $this->json([
                'message' => 'I see you',
                'content' => $media,
            ]);
        }

        if ( $this->request->request->has('removeVisit') ) {
            $post_id = $this->request->request->get('remove_pid');
            $user_id = $this->user_visitor_id;

            $this->remove_views($post_id, $user_id);

            return $this->json([
                'message' => 'Request received',
                'data'    => 13,
            ]);
        }

        return $this->json([
            'message' => '[500] Something bad happened',
        ]);
    }

    protected function save_outshares($share_id, $post_id, $user_id, $media)
    {
        $this->conn->insert('verb_shares', ['share_id'=>$share_id, 'pid'=>$post_id, 'uid'=>$user_id, 'media'=>$media]);
        unset($share_id, $post_id, $user_id, $media);
    }
    
    protected function save_inshares($share_id, $post_id, $user_id, $media)
    {
        $this->conn->insert('verb_visits', ['visit_id'=>$share_id, 'pid'=>$post_id, 'uid'=>$user_id, 'media'=>$media, 'state'=>1]);
        unset($share_id, $post_id, $user_id, $media);
    }

    protected function remove_views($pid, $uid)
    {
        $this->conn->update('verb_visits', ['state'=>0], ['pid'=>$pid, 'uid'=>$uid]);
        unset($pid, $uid);
    }
}