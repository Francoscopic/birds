<?php

namespace App\Verb\Home;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Database\DatabaseAccess;
use App\Validation\SigninValidation;
use App\Verb\Cookie\RetrieveCookie;
use App\Vunction\IndexFunction;


class ArticleCommentHome extends AbstractController
{
    private $request;
    private $get_cookie;
    private $user_id;
    private $connection;

    public function __construct(Connection $conn)
    {
        $this->request = Request::createFromGlobals();

        $this->connection = $conn;

        $this->get_cookie = new RetrieveCookie();
        $this->user_id = $this->get_cookie->get_netintui_user_id()['user_id'];
    }

    public function verbs(): JsonResponse
    {
        if( $this->request->request->has('com_pid') &&
            $this->request->request->has('com')
        )
        {
            $pid     = $this->request->request->get('com_pid');
            $puid    = IndexFunction::get_poster_uid($this->connection, $pid)['uid']; // poster-user_id
            $uid     = $this->user_id;
            $comment = IndexFunction::test_input($this->request->request->get('com'));

            // Code here
            $this->submit_comment($pid, $puid, $uid, $comment);
            
            return $this->json([
                'message' => 'Comment saved',
            ]);
        }
        return $this->json([
            'message' => '[500] Something bad happened',
        ]);
    }

    protected function submit_comment($pid, $puid, $uid, $comment)
    {
        $conn = $this->connection;

        $thisID = IndexFunction::randomKey(11);

        $conn->insert('verb_comments', ['pid'=>$pid, 'puid'=>$puid, 'uid'=>$uid, 'cid'=>$thisID]);
        $conn->insert('verb_comments_list', ['cid'=>$thisID, 'comment'=>$comment]);

        unset($pid, $puid, $uid, $comment, $thisID);
    }
}