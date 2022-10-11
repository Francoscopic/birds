<?php

namespace App\Validation;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Database\DatabaseAccess;
use App\Function\IndexFunction;


class SigninValidation 
{
    public $page_state;

    public function __construct()
    {
        $this->page_state = $this->check_login();
    }

    protected function check_login(): bool
    {
        if( isset($_SESSION['uid'], $_SESSION['sesh'], $_SESSION['isin'], $_COOKIE['cookie_user']) ) {
    
            // First, check is the session is set and continue further
            $user_id = trim($_SESSION['uid']);
            $sesh_id = trim($_SESSION['sesh']);
            $isLoggedIn = $_SESSION['isin'];
    
            $validateSesh = validate_sesh_login($user_id, $isLoggedIn, $sesh_id);
    
            if($validateSesh === true) {
    
                // Destroy varaiables
                unset($user_id, $sesh_id, $isLoggedIn, $validateSesh, $_SESSION['vst']); //clear memory
                set_cookie_variables('vst', '', 40);
                return true;
            }
            unset($user_id, $sesh_id, $isLoggedIn, $validateSesh, $_SESSION['vst']); //clear memory
            return false;
        } else if( isset($_COOKIE['cookie_user'], $_COOKIE['cookie_sesh']) ) {
    
            // If session is not set, then cookie might be set(if not expired).
            // Validate cookie sesh is same as saved sesh, then send them in.
            $coo_sesh = trim($_COOKIE['cookie_sesh']);
            $coo_user_id = trim($_COOKIE['cookie_user']);
    
            $validateCoo = validate_sesh_login($coo_user_id, $isLoggedIn = false, $coo_sesh);
    
            if($validateCoo === true) {
    
                unset($coo_sesh, $coo_user_id, $validateCoo, $_SESSION['vst']); //clear memory
                set_cookie_variables('vst', '', 40);
                return true;
            }
            unset($coo_sesh, $coo_user_id, $validateCoo, $_SESSION['vst']); //clear memory
            return false;
        } else {
            // Just take the person to the signin page. No session is set.
            unset($_SESSION['vst']); //clear memory
            return false;
        }
    }

    protected function validate_sesh_login($userId, $isSignedIn, $sessionId): bool
    {
        $connection = new DatabaseAccess();
        $connection = $connection->connect('');
        $stmt=$connection->prepare("SELECT seshkey FROM user_onyx WHERE uid=? AND seshkey=? ORDER BY sid DESC LIMIT 1");
        $stmt->bind_param('ss', $userId, $sessionId);
        $stmt->execute();
        $checkSeshKey=$stmt->get_result();
        $seshrow = $checkSeshKey->fetch_array(MYSQLI_ASSOC);

        // if($seshrow == 0) {
        if($checkSeshKey->num_rows == 0 || $seshrow == 0) {
            return false;
        }

        $savedSesh = $seshrow['seshkey'];

        if ($isSignedIn == true && $sessionId == $savedSesh) {

            // Check if user has been logged in already from signin.php page -from Aquamarine folder
            // echo 'Give them access with their *uid*';
            unset($stmt, $connection, $checkSeshKey, $seshrow, $savedSesh, $userId, $sessionId, $isSignedIn); //clear memory
            return true;
        } else if($isSignedIn != true && $sessionId == $savedSesh) {

            // Create the lost session_id as saved session_id
            $_SESSION['uid'] = $userId;
            
            unset($stmt, $connection, $checkSeshKey, $seshrow, $savedSesh, $userId, $sessionId, $isSignedIn); //clear memory
            return true;
        } else {
            // Just take the person to the signin page. Login is not *true*
            unset($stmt, $connection, $checkSeshKey, $seshrow, $savedSesh, $userId, $sessionId, $isSignedIn); //clear memory
            return false;
        }
    }

    protected function set_cookie_variables($cookie_name, $cookie_value, $cookie_time)
    {
        $response = new Response();
        $response->headers->setCookie(new Cookie($cookie_name, $cookie_value, strtotime('+6 months')));
        // setcookie('vst', 'haha', strtotime('+1 month'));
        /*  // For special Cookies
            $response->headers->setCookie(
                Cookie::create($cookie_name)
                ->withValue($cookie_value)
                ->withExpires(strtotime('+6 months'))
                ->withSecure(true)
                ->withHttpOnly(true)
            );
        */
        $response->sendHeaders();
    }
    protected function visitor($visitor_id): void
    {
        $this->set_cookie_variables('vst', $visitor_id, 30);
        $this->add_visitor($visitor_id);
    }
    protected function add_visitor($visitor_id): void
    {
        $connection = new DatabaseAccess();
        $connection = $connection->connect('');
        $stmt = $connection->prepare('INSERT INTO visitor (v_id, visits) VALUES(?, visits + 1)');
        $stmt->bind_param('s', $visitor_id);
        $stmt->execute();
        unset($stmt, $connection, $visitor_id);
    }

    public function alright($page_state) 
    {
        $uid = $path = '';
        if( $page_state == true ) {
            $uid = $_SESSION['uid'];
            return array(
                'uid' => $uid,
                'visit' => false
            );
        } else {
            $request = Request::createFromGlobals();
            $path = $request->getPathInfo();
            $allowed_pages = array(
                '', '/', '/home', 
                'article', 'people', 
                'comments', 'help', 'username-policy', 
                'terms', 'rules', 'privacy-policy', 
                'help-articles', 'cookie-policy'
            );
            $reception = in_array($path, $allowed_pages); //check to see if page is allowed to be viewed
        
            if( $reception && isset($_COOKIE['vst']) ) {
        
                $uid = $_SESSION['vst'] = $_COOKIE['vst'];
            } elseif( $reception && !isset($_COOKIE['vst']) ) {
        
                $visitor_id = 'visitor-' .crypt(rand(5000, 9999), random_int(5000, 9999));
                $this->visitor($visitor_id); // Save the data, Create new visitor cookie
                $uid = $_SESSION['vst'] = $visitor_id;
            } else {
                
                $uid = 'vst-intruder';
                $this->add_visitor($uid);
                echo header('Location: /'); // take him outside
                // take the person outside
            }
            unset($allowed_pages, $reception, $_SESSION['uid']);
            return array(
                'uid' => $uid, 
                'visit' => true,
            );
        }
    }
}