<?php

namespace App\Verb\Home;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Database\DatabaseAccess;
use App\Validation\SigninValidation;
use App\Verb\Cookie\RetrieveCookie;
use App\Function\IndexFunction;


class ArticleCommentHome extends AbstractController
{

    private $request;
    private $get_cookie;
    private $user_id;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();

        $this->get_cookie = new RetrieveCookie();
        $this->user_id = $this->get_cookie->get_netintui_user_id()['user_id'];
    }

    public function verbs(): JsonResponse
    {
        if( $this->request->request->has('com_pid') &&
            $this->request->request->has('com')
        )
        {
            $pid    = $this->request->request->get('com_pid');
            $puid   = IndexFunction::get_poster_uid($pid)['data']; // poster-user_id
            $uid    = $this->user_id;

            // Code here
            
            return $this->json([
                'message' => 'Comment saved',
            ]);
        }
        return $this->json([
            'message' => '[500]Something bad happened',
        ]);
    }

    protected function post_comment()
    {
        $connection_sur = new DatabaseAccess();
        $connection_sur = $connection_sur->connect('sur');
        // code here
    }
}