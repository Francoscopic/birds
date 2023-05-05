<?php

namespace App\Verb\Home;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

// use App\Database\DatabaseAccess;
use App\Validation\SigninValidation;
use App\Verb\Cookie\RetrieveCookie;
use App\Vunction\IndexFunction;


class ArticleFollowHome extends AbstractController
{
    private $request;
    private $get_cookie;
    private $conn;
    public function __construct(Connection $connection)
    {
        $this->conn = $connection;
        $this->request = Request::createFromGlobals();

        $this->get_cookie = new RetrieveCookie();
    }
    public function verbs(): JsonResponse
    {
        $viewer_id = $this->get_cookie->get_netintui_user_id()['user_id'];

        if( isset( $_POST['thePid'], $_POST['theReason'] ) )
        {
            $pid    = $_POST['thePid'];
            $puid   = IndexFunction::get_poster_uid($this->conn, $pid)['uid']; // poster-user_id
            $uid    = $viewer_id;
            $reason = $_POST['theReason'];
            $this->work($puid, $uid, $reason);

            return $this->json([
                'message' => 'Data saved',
            ]);
        }
        if( $this->request->request->has('publisher_uname') &&
            $this->request->request->has('reason')
        )
        {
            $puid = IndexFunction::get_profile_uid($this->conn, $this->request->request->get('publisher_uname'))['uid'];
            $reason = $this->request->request->get('reason');
            $this->work($puid, $viewer_id, $reason);

            return $this->json([
                'message' => 'Data saved',
            ]);
        }
        return $this->json([
            'message' => '[500]Something bad happened',
        ]);
    }

    protected function work($puid, $uid, $reason): bool
    {
        $has_follow_history = $this->validate_subscribe($puid, $uid);
    
        if( trim($reason) == 'follow' && !$has_follow_history )
        {
            $this->subscribe_me($puid, $uid);
        }
        return true;
    }

    protected function subscribe_me($pub_uid, $cusm_uid): void
    {
        # Close variables, free memory
        $this->conn->insert('user_subscribes', ['following'=>$pub_uid, $customer=>$cusm_uid, 'state'=>1]);
        unset($pub_uid, $cusm_uid);
    }

    protected function validate_subscribe($pub_uid, $cusm_uid): bool
    {
        $handle = false;
        $stmt = $this->conn->fetchAssociative('SELECT COUNT(id) AS total, state FROM user_subscribes WHERE following = ? AND follower = ?', [$pub_uid, $cusm_]);

        if($stmt == true && $stmt['total'] >= 1) {
            # Is a subscriber or unsubscriber
            $state = $stmt['state'];
            if($state == 1) {
                # Then, unsubscribe
                $this->unsubscribe_me($pub_uid, $cusm_uid);
            } else {
                # re-SUBSCRIBE
                $this->unsubscribe_me($pub_uid, $cusm_uid, 1);
            }
            $handle = true;
        }
        unset($stmt, $pub_uid, $cusm_uid, $state);
        return $handle; // no follow history
    }

    protected function unsubscribe_me($pub_uid, $cusm_uid, $state = 0): void
    {
        $this->conn->update('user_subscribes', ['state'=>$state], ['following'=>$pub_uid, 'follower'=>$cusm_uid]);
        # Close variables, free memory
        unset($pub_uid, $cusm_uid, $state);
    }
}