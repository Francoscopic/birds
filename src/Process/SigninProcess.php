<?php

namespace App\Process;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Session\Session;

use App\Database\DatabaseAccess;
use App\Function\IndexFunction;
use App\Validation\SigninValidation;

class SigninProcess extends AbstractController
{
    private $session;
    public function sign_in(Request $request): JsonResponse
    {
        // Procedure
            # 1. Validate good input (check)
            # 2. Validate details exist (check)
            # 3. Validate correct details. (check)
            # 4. Set environment ready
            # 5. Send in.

        $session = new Session();
        // $session->start();

        if( isset($request->request) ) {
        // if( isset($_POST['clt'], $_POST['psw']) ) {
    
            // $user = trim($_POST['clt']);
            // $pass = $_POST['psw'];

            $user = $request->request->get('clt');
            $pass = $request->request->get('psw');
            $mySeshKey = round(microtime(true)) . IndexFunction::randomKey(rand(3, 10));
    
            $one_ = $this->validate_user_input($user, $pass);
            if($one_['handle'] === true) {
    
                $two_ = $this->validate_details_exist($user);
                if($two_['handle'] === true) {
    
                    $three_ = $this->validate_correct_details($user, $pass);
                    if($three_['handle'] == true) {
    
                        $uid = $three_['uid'];
    
                        $four_ = $this->set_cookie_variables($uid, $mySeshKey);
                        if($this->update_session_key($uid, $mySeshKey) == true) {
    
                            # Create session
                            $session->set('sesh', $mySeshKey);
                            $session->set('uid', $uid);
                            $session->set('isin', true);
    
                            # Success
                            return $this->json([
                                'message' => 'Login success',
                                'status' => 40,
                            ]);
                        }
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
        $message = '';
        $status = '10';
        if( $email == '' && $pass == '' ) {

            // $this->echo_error('Enter details');
            $message = 'Enter details';
            $handle = false;
            $status = '11';
        } elseif( !$valid_email ) {
            
            // $this->echo_error('Email incorrect');
            $message = 'Email incorrect';
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

    protected function validate_details_exist($email): array 
    {
        $message = $status = '20';
        $handle = true;

        # Database Access
        $connection = new DatabaseAccess();
        $connection = $connection->connect('');

        $stmt = $connection->prepare("SELECT ud.confirmed, us.sid
                                        FROM user_sapphire us INNER JOIN user_diamond ud
                                        ON us.uid = ud.uid
                                        WHERE us.email = ? OR us.uname = ?");
        $stmt->bind_param("ss", $email, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->num_rows;
        
        if( $row != 1 || $row == 0 ) {
            // echo '<span class="ft-sect"><strong>You don\'t seem to have an account. <br><a class="note-er a" href="/o/signup/">Create one today.</a></strong></span>';
            $message = 'User Credentials Unavailable';
            $status = '21';
            $handle = false;
        }
        if($result->fetch_array(MYSQLI_ASSOC)['confirmed'] == 0) {
            // echo '<span class="ft-sect"><strong>Please check mail to confirm account</strong></span>';
            $message = 'Account Confirmation Required';
            $status = '22';
            $handle = false;
        }
        unset($stmt, $connection, $result, $row, $email);
        return array(
            'message' => $message,
            'handle'  => $handle,
            'status'  => $status,
        );
    }

    protected function validate_correct_details($email, $pass): array 
    {
        $message = $status = '30';
        $handle = true;

        # Database Access
        $connection = new DatabaseAccess();
        $connection = $connection->connect('');

        $stmt = $connection->prepare("SELECT ud.password, us.uid
                                        FROM user_sapphire us, user_diamond ud
                                        WHERE (us.email = ? AND ud.confirmed = 1) OR (us.uname=? AND ud.confirmed=1) LIMIT 1");
        $stmt->bind_param("ss", $email, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $hashed_pass = $row['password'];
        $uid = $row['uid'];

        if($this->validate_user_access($uid) != true) {
            # Exceeded login trial
            // echo 80;
            $status = '31';
            $handle = false;
            $message = 'Account locked out. Please contact #Support';
        } elseif ($this->validate_password($email, $pass) != true) {
            # password incorrect
            $this->update_login_passcount($uid);   # Do security.
            $status = '32';
            $handle = false;
            $message = 'User Credentials Incomplete';
        }

        unset($stmt, $connection, $result, $row, $email, $pass, $hashed_pass);

        return array(
            'message' => $message,
            'handle'  => $handle,
            'status'  => $status,
            'uid'     => $uid,
        );
    }

    protected function validate_user_access($uid): bool
    {
        $handle = true;
        # Database Access
        $connection = new DatabaseAccess();
        $connection = $connection->connect('');

        $stmt = $connection->prepare("SELECT uid, passcount FROM user_secure WHERE uid = ?");
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $secure = $stmt->get_result();
        $secure_row = $secure->fetch_array(MYSQLI_ASSOC);
        $passcount = $secure_row['passcount'];
        $handle = ($passcount >= 5) ? false : true;

        unset($stmt, $connection, $uid, $secure, $secure_row, $passcount);
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
            unset($_SESSION['vst']); // kill the visitor session
            $handle = true;
        } else {
            echo_error('Server error. Retry');
            $handle = false;
        }
        unset($cookieName, $cookieSesh, $uid, $mySeshKey, $uid_cookie, $sesh_cookie, $visitor_cookie);
        return $handle;
    }

    protected function update_session_key($uid, $mySeshKey): bool
    {
        $handle = true;
        # Database Access
        $connection = new DatabaseAccess();
        $connection = $connection->connect('');

        $stmt = $connection->prepare("INSERT INTO user_onyx (uid, seshkey) VALUES(?, ?)");
        $stmt->bind_param("ss", $uid, $mySeshKey);
        if( $stmt->execute() ) {
            # Update passcount to '0'
            $stmt = $connection->prepare("UPDATE user_secure SET passcount = 0 WHERE uid = ?");
            $stmt->bind_param("s", $uid);
            $stmt->execute();
        } else {
            # Weird error
            $handle = false;
        }
        unset($stmt, $connection, $uid, $mySeshKey);
        return $handle;
    }

    protected function update_login_passcount($uid)
    {
        # Database Access
        $connection = new DatabaseAccess();
        $connection = $connection->connect('');

        $stmt = $connection->prepare("UPDATE user_secure SET passcount = passcount + 1 WHERE uid = ?");
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        unset($stmt, $connection, $uid);
    }

    protected function validate_password($email, $pass): bool 
    {
        # Database Access
        $connection = new DatabaseAccess();
        $connection = $connection->connect('');

        $stmt = $connection->prepare("SELECT ud.password, ud.uid
                                        FROM user_diamond ud
                                        INNER JOIN user_sapphire us
                                        ON ud.uid=us.uid
                                        WHERE us.email = ? OR us.uname=?");
        $stmt->bind_param('ss', $email, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $password = $row['password'];

        unset($stmt, $connection, $result, $row);
        return password_verify($pass, $password) ? true : false;
    }
}