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


class ArticleLikeHome extends AbstractController
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
    
            if( trim($reason) == 'save' )
            {
                $this->note($pid, $puid, $uid);
            }
            if( trim($reason) == 'like' )
            {
                $this->like($pid, $puid, $uid);
            }
            if( trim($reason == 'unlike') )
            {
                $this->unlike($pid, $puid, $uid);
            }
            if( trim($reason == 'share') )
            {
                // Coming soon
            }
            if( trim($reason) == 'report' )
            {
                // Here PUID is => report data.
                $this->report($pid, $uid, $_POST['other']);
            }
            return $this->json([
                'message' => 'Data saved',
            ]);
        }
        return $this->json([
            'message' => '[500]Something bad happened',
        ]);
    }

    protected function note($thePid, $thePUid, $theUid, $state = 1) 
    {
        // Database Access
        $connection_verb = new DatabaseAccess();
        $connection_verb = $connection_verb->connect('verb');

        $thisID = rand(99, 9999).round(microtime(true)) . IndexFunction::randomKey(7);

        $stmt = $connection_verb->prepare('SELECT state FROM saves WHERE uid = ? AND puid = ? AND pid = ?');
        $stmt->bind_param('sss', $theUid, $thePUid, $thePid);
        $stmt->execute();
        $getResult = $stmt->get_result();

        // if getResult is not greater than zero.
        // Meaning you haven't saved this before/ INSERT into database
        if( !( $getResult->num_rows > 0 ) ) {
            // Nothing found. Save new
            $stmt = $connection_verb->prepare('INSERT INTO saves (pid, puid, uid, bid, state) VALUES(?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $thePid, $thePUid, $theUid, $thisID, $state);
            $stmt->execute();
        } else {
            # Found something. UPDATE
            $current_state = $getResult->fetch_array(MYSQLI_ASSOC)['state'];
            if($current_state == 0) {
                // If state is 0 (unsaved), make it 1 (save)
                $stmt = $connection_verb->prepare('UPDATE saves SET state = 1 WHERE pid = ? AND puid = ? AND uid = ?');
                $stmt->bind_param('sss', $thePid, $thePUid, $theUid);
                $stmt->execute();
            } else {
                // If state is 1 (saved), make it 0 (unsave)
                $stmt = $connection_verb->prepare('UPDATE saves SET state = 0 WHERE pid = ? AND puid = ? AND uid = ?');
                $stmt->bind_param('sss', $thePid, $thePUid, $theUid);
                $stmt->execute();
            }
        }
        unset($connection_verb, $stmt, $thePid, $thePUid, $theUid, $state, $thisID, $getResult);
    }

    protected function like($thePid, $thePUid, $theUid, $state = 1) 
    {
        // Database Access
        $connection_verb = new DatabaseAccess();
        $connection_verb = $connection_verb->connect('verb');

        $thisID = rand(99, 9999).round(microtime(true)) . IndexFunction::randomKey(7);

        $stmt = $connection_verb->prepare('SELECT state FROM likes WHERE uid = ? AND puid = ? AND pid = ?');
        $stmt->bind_param('sss', $theUid, $thePUid, $thePid);
        $stmt->execute();
        $getResult = $stmt->get_result();

        // if getResult is not greater than zero.
        // Meaning you haven't saved this before/ INSERT into database
        if( !( $getResult->num_rows > 0 ) ) {
            # Nothing found. INSERT
            $stmt = $connection_verb->prepare('INSERT INTO likes (pid, puid, uid, lid, state) VALUES(?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $thePid, $thePUid, $theUid, $thisID, $state);
            $stmt->execute();
        } else {
            # Found something. UPDATE
            $current_state = $getResult->fetch_array(MYSQLI_ASSOC)['state'];
            if($current_state == 0) {
                // If state is 0 (unliked), make it 1 (like)
                $stmt = $connection_verb->prepare('UPDATE likes SET state = 1 WHERE pid = ? AND puid = ? AND uid = ?');
                $stmt->bind_param('sss', $thePid, $thePUid, $theUid);
                $stmt->execute();
                echo 0;
            } else {
                // If state is 1 (liked), make it 0 (unlike)
                $stmt = $connection_verb->prepare('UPDATE likes SET state = 0 WHERE pid = ? AND puid = ? AND uid = ?');
                $stmt->bind_param('sss', $thePid, $thePUid, $theUid);
                $stmt->execute();
                echo 1;
            }
        }
        unset($connection_verb, $stmt, $thePid, $thePUid, $theUid, $state, $thisID, $getResult);
    }

    protected function unlike($thePid, $thePUid, $theUid, $state = 0) 
    {
        // Database Access
        $connection_verb = new DatabaseAccess();
        $connection_verb = $connection_verb->connect('verb');

        $thisID = rand(99, 9999).round(microtime(true)) . IndexFunction::randomKey(7);

        $stmt = $connection_verb->prepare('SELECT state FROM unlikes WHERE uid = ? AND puid = ? AND pid = ?');
        $stmt->bind_param('sss', $theUid, $thePUid, $thePid);
        $stmt->execute();
        $getResult = $stmt->get_result();

        // if getResult is not greater than zero.
        // Meaning you haven't saved this before/ INSERT into database
        if( !( $getResult->num_rows > 0 ) ) {
            # Nothing found. INSERT
            $stmt = $connection_verb->prepare('INSERT INTO unlikes (pid, puid, uid, lid, state) VALUES(?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $thePid, $thePUid, $theUid, $thisID, $state);
            $stmt->execute();
        } else {
            # Found something. UPDATE
            $current_state = $getResult->fetch_array(MYSQLI_ASSOC)['state'];
            if($current_state == 0) {
                // If state is 0 (unliked), make it 1 (like)
                $stmt = $connection_verb->prepare('UPDATE unlikes SET state = 1 WHERE pid = ? AND puid = ? AND uid = ?');
                $stmt->bind_param('sss', $thePid, $thePUid, $theUid);
                $stmt->execute();
                echo 0;
            } else {
                // If state is 1 (liked), make it 0 (unlike)
                $stmt = $connection_verb->prepare('UPDATE unlikes SET state = 0 WHERE pid = ? AND puid = ? AND uid = ?');
                $stmt->bind_param('sss', $thePid, $thePUid, $theUid);
                $stmt->execute();
                echo 1;
            }
        }
        unset($connection_verb, $stmt, $thePid, $thePUid, $theUid, $state, $thisID, $getResult);
    }

    protected function renote($thePid, $thePUid, $theUid, $state = 1) 
    {
        // Database Access
        $connection_verb = new DatabaseAccess();
        $connection_verb = $connection_verb->connect('verb');

        $thisID = rand(99, 9999).round(microtime(true)) . IndexFunction::randomKey(7);

        $stmt = $connection_verb->prepare('SELECT sid FROM renotes WHERE uid = ? AND puid = ? AND pid = ? AND state = ?');
        $stmt->bind_param('ssss', $theUid, $thePUid, $thePid, $state);
        $stmt->execute();
        $getResult = $stmt->get_result();

        // if getResult is not greater than zero.
        // Meaning you haven't note this before/ INSERT into database
        if( !( $getResult->num_rows > 0 ) ) {
            // Nothing found. Save new
            $stmt = $connection_verb->prepare('INSERT INTO renotes (pid, puid, uid, rid, state) VALUES(?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $thePid, $thePUid, $theUid, $thisID, $state);
            $stmt->execute();
            // echo Saved to database;
        }
            // ELSE: The result is greater than zero
            // Meaning you have note this before.

        unset($connection_verb, $stmt, $thePid, $thePUid, $theUid, $state, $thisID, $getResult);
    }

    protected function report($thePid, $theUid, $theReportData_asPUid) 
    {
        // Database Access
        $connection_verb = new DatabaseAccess();
        $connection_verb = $connection_verb->connect('verb');
        $thisID = rand(99, 9999).round(microtime(true)) . IndexFunction::randomKey(7);

        $stmt = $connection_verb->prepare('INSERT INTO report (pid, uid, rid, sitch) VALUES(?, ?, ?, ?)');
        $stmt->bind_param('ssss', $thePid, $theUid, $thisID, $theReportData_asPUid);
        $stmt->execute();
        unset($connection_verb, $stmt, $thisID, $thePid, $theReportData_asPUid, $theUid);
    }
}