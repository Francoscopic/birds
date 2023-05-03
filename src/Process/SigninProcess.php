<?php

namespace App\Process;

use Doctrine\DBAL\Connection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;

use App\Database\DatabaseAccess;
use App\Vunction\IndexFunction;

class SigninProcess extends AbstractController
{
    private $session;
    private $connection;

    public function sign_in(Request $request, Connection $conn): JsonResponse
    {
        // Procedure
            # 1. Validate good input (check)
            # 2. Validate details exist (check)
            # 3. Validate correct details. (check)
            # 4. Set environment ready
            # 5. Send in.

        $this->session = new Session();
        // $session->start();

        # Database Access
        $this->connection = $conn;

        if( isset($request->request) ) {

            $user = $request->request->get('clt');
            $pass = $request->request->get('psw');
            $mySeshKey = round(microtime(true)) . IndexFunction::randomKey(7);
    
            $one_ = $this->validate_user_input($user, $pass);
            if($one_['handle'] === true) {
    
                $two_ = $this->validate_details_exist($conn, $user);
                if($two_['handle'] === true) {
    
                    $three_ = $this->validate_correct_details($conn, $user, $pass);
                    if($three_['handle'] == true) {
    
                        $uid = $three_['uid'];
    
                        $four_ = $this->set_cookie_variables($uid, $mySeshKey);
                        if($this->update_session_key($conn, $uid, $mySeshKey) == true) {
    
                            # Create session
                            $this->session->set('sesh', $mySeshKey);
                            $this->session->set('uid', $uid);
                            $this->session->set('isin', true);
    
                            # Success
                            return $this->json([
                                'message' => 'Login success',
                                'status' => 40,
                            ]);
                        }
                        return $this->json([
                            'message' => '[4500] Bite refused you access. Seek support',
                            'status' => $four_,
                        ]);
                    }
                    return $this->json([
                        'message' => $three_['message'],
                        'status' => $three_['status'],
                    ]);
                }
                return $this->json([
                    'message' => $two_['message'],
                    'status' => $two_['status'],
                ]);
            }
            return $this->json([
                'message' => $one_['message'],
                'status' => $one_['status'],
            ]);
        }
        return $this->json([
            'message' => '[500] Something bad happened',
            'status' => '500',
        ]);
    }

    protected function validate_user_input($email, $pass): array 
    {
        $valid_email = filter_var($email, FILTER_VALIDATE_EMAIL);
        $handle = true;
        $message = '[10] Error occured';
        $status = '10';
        if( $email == '' && $pass == '' ) {

            $message = '[11] Enter details';
            $handle = false;
            $status = '11';
        } elseif( !$valid_email ) {
            
            $message = '[12] Email incorrect';
            $handle = false;
            $status = '12';
        }
        unset($valid_email, $email, $pass);
        return array(
            'message' => $message,
            'handle'  => $handle,
            'status'  => $status,
        );
    }

    protected function validate_details_exist($conn, $email): array 
    {
        $message = '[20] Indentity/password incorrect';
        $status = '20';
        $handle = true;

        $stmt = $conn->fetchAssociative('SELECT ud.confirmed, us.id
            FROM user_sapphire us INNER JOIN user_diamond ud
            ON us.uid = ud.uid
            WHERE us.email = :userInput OR us.uname = :userInput', ['userInput'=>$email]);

        if($stmt == false) {
            // no account found
            $message = '[21] Identity/password incorrect';
            $status = '21';
            $handle = false;
        }

        if($stmt == true && $stmt['confirmed'] == 0) {
            // account confirmation required
            $message = '[22] Account confirmation required';
            $status = '22';
            $handle = false;
        }

        unset($stmt, $email);
        return array(
            'message' => $message,
            'handle'  => $handle,
            'status'  => $status,
        );
    }

    protected function validate_correct_details($conn, $email, $pass): array 
    {
        $message = '[30] Indentity/password incorrect';
        $status = '30';
        $handle = true;

        $stmt = $conn->fetchAssociative('SELECT ud.password, us.uid
            FROM user_sapphire us, user_diamond ud
            WHERE (us.email=:userInput AND ud.confirmed=1) OR (us.uname=:userInput AND ud.confirmed=1) LIMIT 1', ['userInput'=>$email]);

        $hashed_pass = $stmt['password'];
        $uid = $stmt['uid'];
        if($stmt == true) {
            if($this->validate_user_access($conn, $stmt['uid']) != true) {
                # Exceeded login trial
                $status = '31';
                $handle = false;
                $message = '[31] Account held temporarily. #Support can help';
            } elseif ($this->validate_password($conn, $email, $pass) != true) {
                # password incorrect
                $this->update_login_passcount($conn, $uid);   # Do security.
                $status = '32';
                $handle = false;
                $message = '[32] Indentity/password incorrect';
            }
        }

        unset($stmt, $email, $pass, $hashed_pass);
        return array(
            'message' => $message,
            'handle'  => $handle,
            'status'  => $status,
            'uid'     => $uid,
        );
    }

    protected function validate_user_access($conn, $uid): bool
    {
        $passcount = 0;
        $handle = true;

        $stmt = $conn->fetchAssociative('SELECT id, passcount FROM user_secure WHERE uid = ?', [$uid]);
        if($stmt == true) {
            $passcount = $stmt['passcount'];
        }
        $handle = ($passcount >= 5) ? false : true;

        unset($stmt, $conn, $uid, $passcount);
        return $handle;
    }

    protected function set_cookie_variables($uid, $mySeshKey): bool
    {
        $handle = true;
        $cookieName = 'cookie_user';
        $cookieSesh = 'cookie_sesh';

        $uid_cookie     = IndexFunction::set_cookie_variables($cookieName, $uid, '+6 months');
        $sesh_cookie    = IndexFunction::set_cookie_variables($cookieSesh, $mySeshKey, '+6 months');
        $visitor_cookie = IndexFunction::set_cookie_variables('vst', '', '-7 months');

        if( $uid_cookie==true && $sesh_cookie==true && $visitor_cookie==true ) {
            // kill the visitor session
            $this->session->remove('vst');
            $handle = true;
        } else {
            // Unknown error
            $handle = false;
        }
        unset($cookieName, $cookieSesh, $uid, $mySeshKey, $uid_cookie, $sesh_cookie, $visitor_cookie);
        return $handle;
    }

    protected function update_session_key($conn, $uid, $mySeshKey): bool
    {
        $handle = true;

        $handle = $conn->insert('user_onyx', ['uid' => $uid, 'seshkey' => $mySeshKey]);
        $handle = $conn->update('user_secure', ['passcount' => 0], ['uid' => $uid]);

        unset($conn, $uid, $mySeshKey);
        return $handle;
    }

    protected function update_login_passcount($conn, $uid)
    {
        $conn->update('user_secure', ['passcount' => 'passcount + 1'], ['uid' => $uid]);
        unset($conn, $uid);
    }

    protected function validate_password($conn, $email, $pass): bool 
    {
        $stmt = $conn->fetchAssociative('SELECT ud.password
            FROM user_diamond ud
            INNER JOIN user_sapphire us
            ON ud.uid=us.uid
            WHERE us.email = :userInput OR us.uname = :userInput', ['userInput'=>$email]);
        if($stmt == false) {
            return false;
        }
        $password = $stmt['password'];

        unset($stmt, $conn);
        return password_verify($pass, $password);
    }
}