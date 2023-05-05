<?php

namespace App\Validation;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use App\Database\DatabaseAccess;
use App\Vunction\IndexFunction;


class SigninValidation
{
    public $page_state;
    private $session_cell;
    private $cookie_cell;
    private $request;
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;

        $this->session_cell = new Session();
        // $this->session_cell->start();

        $this->request = Request::createFromGlobals();
        $this->cookie_cell  = $this->request->cookies;

        $this->page_state = $this->check_login(); // start the process
    }

    protected function check_login(): bool
    {
        if(
                $this->cookie_cell->has('cookie_user') &&
                $this->session_cell->has('uid') &&
                $this->session_cell->has('sesh') &&
                $this->session_cell->has('isin')
            )
        {
            $user_id    = $this->session_cell->get('uid');
            $sesh_id    = $this->session_cell->get('sesh');
            $isLoggedIn = $this->session_cell->get('isin');
    
            $validateSesh = $this->validate_sesh_login($user_id, $isLoggedIn, $sesh_id);
    
            if($validateSesh === true) {
    
                // Destroy varaiables
                unset($user_id, $sesh_id, $isLoggedIn, $validateSesh);
                $this->session_cell->remove('vst');
                IndexFunction::set_cookie_variables('vst', '', '-7 months');

                return true;
            }
            //clear memory
            unset($user_id, $sesh_id, $isLoggedIn, $validateSesh);
            // $this->session_cell->remove('vst');

            return false;
        } else if( 
                $this->cookie_cell->has('cookie_user') &&
                $this->cookie_cell->has('cookie_sesh')
            )
        {
    
            // If session is not set, then cookie might be set(if not expired).
            // Validate cookie sesh is same as saved sesh, then send them in.

            $coo_sesh    = $this->cookie_cell->get('cookie_sesh');
            $coo_user_id = $this->cookie_cell->get('cookie_user');
    
            $validateCoo = $this->validate_sesh_login($coo_user_id, $isLoggedIn = false, $coo_sesh);
    
            if($validateCoo === true) {
                // clear memory
                unset($coo_sesh, $coo_user_id, $validateCoo);
                $this->session_cell->remove('vst'); // if it exists
                IndexFunction::set_cookie_variables('vst', '', '-7 months'); // delete visitor, if it exists

                return true;
            }
            //clear memory
            unset($coo_sesh, $coo_user_id, $validateCoo); 
            $this->session_cell->remove('vst');
            return false;
        } else {
            // Just take the person to the signin page. No session is set.
            $this->session_cell->remove('vst'); // if it exists
            // and I assume Cookie: VST would be available
            return false;
        }
    }

    protected function validate_sesh_login($userId, $isSignedIn, $sessionId): bool
    {
        $stmt = $this->conn->fetchOne('SELECT id FROM user_onyx WHERE uid=? AND seshkey=? ORDER BY id DESC LIMIT 1', [$userId, $sessionId]);

        if($stmt == false) {
            return false;
        }

        $savedSesh = $sessionId;

        if ($isSignedIn == true && $sessionId == $savedSesh) {

            // Check if user has been logged in already from signin.php page -from Aquamarine folder
            // echo 'Give them access with their *uid*';
            unset($stmt, $savedSesh, $userId, $sessionId, $isSignedIn); //clear memory
            return true;
        } else if($isSignedIn != true && $sessionId == $savedSesh) {

            // Create the lost session_id as saved session_id
            // $_SESSION['uid'] = $userId;
            $this->session_cell->set('uid', $userId);
            
            unset($stmt, $savedSesh, $userId, $sessionId, $isSignedIn); //clear memory
            return true;
        } else {
            // Just take the person to the signin page. Login is not *true*
            unset($stmt, $savedSesh, $userId, $sessionId, $isSignedIn); //clear memory
            return false;
        }
    }

    protected function visitor($visitor_id): void
    {
        IndexFunction::set_cookie_variables('vst', $visitor_id, '+6 months');
        $this->add_visitor($visitor_id);
    }
    protected function add_visitor($visitor_id): void
    {
        $this->conn->insert('user_visitor', ['v_id'=>$visitor_id, 'visits'=>'visits + 1']);
        unset($visitor_id);
    }

    public function alright($page_state)
    {
        $uid = $path = '';
        $intruder = false;

        if( $page_state == true ) {
            $uid = $this->cookie_cell->get('cookie_user');
            $intruder = false;
            return array(
                'message'  => '[User] Logged in',
                'uid'      => $uid,
                'visit'    => false,
                'intruder' => $intruder,
                'user'     => IndexFunction::user_profile_state($this->conn, $uid),
            );
        } else {

            $path = $this->request->getPathInfo();
            $allowed_pages = array(
                '', '/', '/home', 
                'article', 'people', 
                'comments', 'help', 'username-policy', 
                'terms', 'rules', 'privacy-policy', 
                'help-articles', 'cookie-policy'
            );
            $reception = in_array($path, $allowed_pages); //check to see if page is allowed to be viewed
        
            if( $reception && $this->cookie_cell->has('vst') ) {
        
                $this->session_cell->set('vst', $this->cookie_cell->get('vst'));
                $uid = $this->cookie_cell->get('vst');
                $intruder = false;
            } elseif( $reception && !$this->cookie_cell->has('vst') ) {
        
                $visitor_id = 'visitor-' .crypt(rand(5000, 9999), random_int(5000, 9999));

                $this->visitor($visitor_id); // Save the data, Create new visitor cookie
                $this->session_cell->set('vst', $visitor_id);
                $uid = $visitor_id;
                $intruder = false;
            } else {
                
                $uid = 'vst-north-' . IndexFunction::randomKey(5);
                $this->add_visitor($uid);
                $intruder = true;
                // take the person outside
            }
            unset($allowed_pages, $reception, $_SESSION['uid']);
            return array(
                'message'  => '[Visitor] Limited access',
                'uid'      => $uid, 
                'visit'    => true,
                'intruder' => $intruder,
                'user'     => IndexFunction::user_profile_state($this->conn, false),
            );
        }
        return array(
            'message'  => '[Visitor] Something horrible happened',
            'uid'      => $uid,
            'visit'    => true,
            'intruder' => $intruder,
            'user'     => IndexFunction::user_profile_state($this->conn, false),
        );
    }
}