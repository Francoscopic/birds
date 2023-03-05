<?php

namespace App\Verb\Home;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Database\DatabaseAccess;
use App\Validation\SigninValidation;
use App\Verb\Cookie\RetrieveCookie;
use App\Vunction\IndexFunction;


class ArticleFollowHome extends AbstractController
{
    private $request;
    private $get_cookie;
    public function __construct()
    {
        $this->request = Request::createFromGlobals();

        $this->get_cookie = new RetrieveCookie();
    }
    public function verbs(): JsonResponse
    {
        $viewer_id = $this->get_cookie->get_netintui_user_id()['user_id'];

        if( isset( $_POST['thePid'], $_POST['theReason'] ) )
        {
            $pid    = $_POST['thePid'];
            $puid   = IndexFunction::get_poster_uid($pid)['uid']; // poster-user_id
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
            $puid = IndexFunction::get_profile_uid($this->request->request->get('publisher_uname'))['uid'];
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

    protected function work($puid, $uid, $reason)
    {
        $has_follow_history = $this->validate_subscribe($puid, $uid);
    
        if( trim($reason) == 'follow' && !$has_follow_history )
        {
            $this->subscribe_me($puid, $uid);
        }
        return true;
    }

    protected function subscribe_me($pub_uid, $cusm_uid)
    {
        // Database Access
        $connection_sur = new DatabaseAccess();
        $connection_sur = $connection_sur->connect('sur');

        $stmt = $connection_sur->prepare('INSERT INTO subscribes (publisher, customer, state) VALUES(?, ?, 1)');
        $stmt->bind_param('ss', $pub_uid, $cusm_uid);
        $stmt->execute();
        # Close variables, free memory
        unset($stmt, $connection_sur, $pub_uid, $cusm_uid);
    }

    protected function validate_subscribe($pub_uid, $cusm_uid): bool
    {
        $connection_sur = new DatabaseAccess();
        $connection_sur = $connection_sur->connect('sur');

        $stmt = $connection_sur->prepare('SELECT state FROM subscribes WHERE publisher = ? AND customer = ?');
        $stmt->bind_param('ss', $pub_uid, $cusm_uid);
        $stmt->execute();
        $get_result = $stmt->get_result();

        if( $get_result->num_rows >= 1 ) {
            # Is a subscriber or unsubscriber
            $state = $get_result->fetch_array(MYSQLI_ASSOC)['state'];
            if($state == 1) {
                # Then, unsubscribe
                $this->unsubscribe_me($pub_uid, $cusm_uid);
            } else {
                # re-SUBSCRIBE
                $this->unsubscribe_me($pub_uid, $cusm_uid, 1);
            }
            unset($connection_sur, $stmt, $pub_uid, $cusm_uid, $get_result, $state);
            return true;
        }
        return false; // no follow history
    }

    protected function unsubscribe_me($pub_uid, $cusm_uid, $state = 0)
    {
        $connection_sur = new DatabaseAccess();
        $connection_sur = $connection_sur->connect('sur');

        $stmt = $connection_sur->prepare('UPDATE subscribes SET state = ? WHERE publisher = ? AND customer = ?');
        $stmt->bind_param('sss', $state, $pub_uid, $cusm_uid);
        $stmt->execute();
        # Close variables, free memory
        unset($stmt, $connection_sur, $pub_uid, $cusm_uid);
    }
}