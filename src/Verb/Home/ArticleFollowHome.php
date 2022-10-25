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


class ArticleFollowHome extends AbstractController
{
    public function verbs(): JsonResponse
    {
        $get_cookie = new RetrieveCookie();
        $viewer_id = $get_cookie->get_netintui_user_id()['user_id'];

        if( isset( $_POST['thePid'], $_POST['theReason'] ) )
        {
            $pid    = $_POST['thePid'];
            $puid   = IndexFunction::get_poster_uid($pid)['data']; // poster-user_id
            $uid    = $viewer_id;
            $reason = $_POST['theReason'];

            $get_state = $this->validate_subscribe($puid, $uid);
    
            if( trim($reason) == 'follow' && $get_state === false )
            {
                $this->subscribe_me($puid, $uid);
            }
            return $this->json([
                'message' => 'Data saved',
            ]);
        }
        return $this->json([
            'message' => '[500]Something bad happened',
        ]);
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
        return false;
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