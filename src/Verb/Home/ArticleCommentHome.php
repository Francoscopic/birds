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
            $pid     = $this->request->request->get('com_pid');
            $puid    = IndexFunction::get_poster_uid($pid)['uid']; // poster-user_id
            $uid     = $this->user_id;
            $comment = IndexFunction::test_input($this->request->request->get('com'));

            // Code here
            $this->submit_comment($pid, $puid, $uid, $comment);
            
            return $this->json([
                'message' => 'Comment saved',
            ]);
        }
        return $this->json([
            'message' => '[500]Something bad happened',
        ]);
    }

    protected function submit_comment($pid, $puid, $uid, $comment)
    {
        $connection_verb = new DatabaseAccess();
        $connection_verb = $connection_verb->connect('verb');

        $thisID = rand(600, 999) . IndexFunction::randomKey(5);

        # INSERT into comments
        $stmt = $connection_verb->prepare('INSERT INTO comments (pid, puid, uid, cid) VALUES(?, ?, ?, ?)');
        $stmt->bind_param('ssss', $pid, $puid, $uid, $thisID);

        # INSERT into comments_list
        $stmt2 = $connection_verb->prepare('INSERT INTO comments_list (cid, comment) VALUES(?, ?)');
        $stmt2->bind_param('ss', $thisID, $comment);

        # Execute INSERT
        $stmt->execute();
        $stmt2->execute();

        unset($connection_verb, $stmt, $stmt2, $pid, $puid, $uid, $thisID, $comment);
    }
}