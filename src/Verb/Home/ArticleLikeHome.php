<?php

namespace App\Verb\Home;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Validation\SigninValidation;
use App\Verb\Cookie\RetrieveCookie;
use App\Vunction\IndexFunction;


class ArticleLikeHome extends AbstractController
{

    private $request;
    private $get_cookie;
    private $user_id;
    private $conn;

    public function __construct(Connection $connection)
    {
        $this->request = Request::createFromGlobals();

        $this->conn = $connection;

        $this->get_cookie = new RetrieveCookie();
        $this->user_id = $this->get_cookie->get_netintui_user_id()['user_id'];
    }

    public function verbs(): JsonResponse
    {
        $get_cookie = new RetrieveCookie();
        $viewer_id = $get_cookie->get_netintui_user_id()['user_id'];

        if( $this->request->request->has('thePid') &&
            $this->request->request->has('theReason')
        )
        {
            $pid    = $this->request->request->get('thePid');
            $puid   = IndexFunction::get_poster_uid($this->conn, $pid)['uid']; // poster-user_id
            $uid    = $viewer_id;
            $reason = $this->request->request->get('theReason');
    
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
            'message' => '[500] Something bad happened',
        ]);
    }

    protected function note($thePid, $thePUid, $theUid, $state = 1): void
    {
        $thisID = IndexFunction::randomKey(9);

        $stmt = $this->conn->fetchAssociative('SELECT COUNT(id) AS total, state FROM verb_saves WHERE uid = ? AND puid = ? AND pid = ?', [$theUid, $thePUid, $thePid]);

        // if result is not greater than zero.
        // Meaning you haven't saved this before/ INSERT into database
        if( !( $stmt['total'] > 0 ) ) {
            // Nothing found. Save new
            $this->conn->insert('verb_saves', ['pid'=>$thePid, 'puid'=>$thePUid, 'uid'=>$theUid, 'bid'=>$thisID, 'state'=>$state]);
        } else {
            # Found something. UPDATE
            $current_state = $stmt['state'];
            if($current_state == 0) {
                // If state is 0 (unsaved), make it 1 (save)
                $this->conn->update('verb_saves', ['state'=>1], ['pid'=>$thePid, 'puid'=>$thePUid, 'uid'=>$theUid]);
            } else {
                // If state is 1 (saved), make it 0 (unsave)
                $this->conn->update('verb_saves', ['state'=>0], ['pid'=>$thePid, 'puid'=>$thePUid, 'uid'=>$theUid]);
            }
        }
        unset($thePid, $thePUid, $theUid, $state, $thisID);
    }

    protected function like($thePid, $thePUid, $theUid, $state = 1) 
    {
        $thisID = IndexFunction::randomKey(9);

        $stmt = $this->conn->fetchAssociative('SELECT COUNT(id) AS total, state FROM verb_likes WHERE uid = ? AND puid = ? AND pid = ?', [$theUid, $thePUid, $thePid]);

        // if result is not greater than zero.
        // Meaning you haven't saved this before/ INSERT into database
        if( !( $stmt['total'] > 0 ) ) {
            # Nothing found. INSERT
            $this->conn->insert('verb_likes', ['pid'=>$thePid, 'puid'=>$thePUid, 'uid'=>$theUid, 'lid'=>$thisID, 'state'=>$state]);
        } else {
            # Found something. UPDATE
            $current_state = $stmt['state'];
            if($current_state == 0) {
                // If state is 0 (unliked), make it 1 (like)
                $this->conn->update('verb_likes', ['state'=>1], ['pid'=>$thePid, 'puid'=>$thePUid, 'uid'=>$theUid]);
                echo 0;
            } else {
                // If state is 1 (liked), make it 0 (unlike)
                $this->conn->update('verb_likes', ['state'=>0], ['pid'=>$thePid, 'puid'=>$thePUid, 'uid'=>$theUid]);
                echo 1;
            }
        }
        unset($stmt, $thePid, $thePUid, $theUid, $state, $thisID);
    }

    protected function unlike($thePid, $thePUid, $theUid, $state = 0) 
    {
        $thisID = IndexFunction::randomKey(9);

        $stmt = $this->conn->fetchAssociative('SELECT COUNT(id) AS total, state FROM verb_unlikes WHERE uid = ? AND puid = ? AND pid = ?', [$theUid, $thePUid, $thePid]);

        // if getResult is not greater than zero.
        // Meaning you haven't saved this before/ INSERT into database
        if( !( $stmt['total'] > 0 ) ) {
            # Nothing found. INSERT
            $this->conn->insert('verb_unlikes', ['pid'=>$thePid, 'puid'=>$thePUid, 'uid'=>$theUid, 'lid'=>$thisID, 'state'=>$state]);
        } else {
            # Found something. UPDATE
            $current_state = $stmt['state'];
            if($current_state == 0) {
                // If state is 0 (unliked), make it 1 (like)
                $this->conn->update('verb_unlikes', ['state'=>1], ['pid'=>$thePid, 'puid'=>$thePUid, 'uid'=>$theUid]);
                echo 0;
            } else {
                // If state is 1 (liked), make it 0 (unlike)
                $this->conn->update('verb_unlikes', ['state'=>0], ['pid'=>$thePid, 'puid'=>$thePUid, 'uid'=>$theUid]);
                echo 1;
            }
        }
        unset($stmt, $thePid, $thePUid, $theUid, $state, $thisID);
    }

    protected function report($thePid, $theUid, $theReportData_asPUid) 
    {
        $thisID = IndexFunction::randomKey(9);

        $this->conn->insert('verb_report', ['pid'=>$thePid, 'uid'=>$theUid, 'report_id'=>$thisID, 'sitch'=>$theReportData_asPUid]);
        unset($thisID, $thePid, $theReportData_asPUid, $theUid);
    }
}