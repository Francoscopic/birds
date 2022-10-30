<?php

namespace App\Verb\Home;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Database\DatabaseAccess;
use App\Function\IndexFunction;
use App\Verb\Cookie\RetrieveCookie;


class ArticleViewsHome extends AbstractController
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

        return $this->json([
            'message' => '[500] Something bad happened',
        ]);
    }

    protected function save_outshares($share_id, $post_id, $user_id, $media)
    {
    
        // Database Access
        $connection_verb = new DatabaseAccess();
        $connection_verb = $connection_verb->connect('verb');

        $stmt = $connection_verb->prepare('INSERT INTO shares (share_id, post_id, user_id, media) VALUES(?, ?, ?, ?)');
        $stmt->bind_param('ssss', $share_id, $post_id, $user_id, $media);
        $stmt->execute();
        unset($connection_verb, $stmt, $share_id, $post_id, $user_id, $media);
    }
    
    protected function save_inshares($share_id, $post_id, $user_id, $media)
    {

        // Database Access
        $connection_verb = new DatabaseAccess();
        $connection_verb = $connection_verb->connect('verb');

        $stmt = $connection_verb->prepare('INSERT INTO visits (visit_id, post_id, user_id, media) VALUES(?, ?, ?, ?)');
        $stmt->bind_param('ssss', $share_id, $post_id, $user_id, $media);
        $stmt->execute();
        unset($connection_verb, $stmt, $share_id, $post_id, $user_id, $media);
    }
}