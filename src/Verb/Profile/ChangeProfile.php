<?php

namespace App\Verb\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Database\DatabaseAccess;
use App\Validation\SigninValidation;
use App\Verb\Cookie\RetrieveCookie;
use App\Function\IndexFunction;


class ChangeProfile extends AbstractController
{
    private $request;
    private $uid;
    private $file_path;
    private $connection;

    public function __construct()
    {
        // get user_id
        $get_cookie = new RetrieveCookie();
        $this->uid = $get_cookie->get_netintui_user_id()['user_id'];

        // get request
        $this->request = Request::createFromGlobals();

        // set constants
        $this->file_path = dirname(__DIR__).'/../../public/images/community/profiles/';

        // access database
        $this->connection = new DatabaseAccess();
        $this->connection = $this->connection->connect('');
    }

    public function index(): JsonResponse
    {
        // on bio
        if($this->request->request->has('change_on_bio')) {
            $res = $this->change_bio($this->uid);
            return $this->json([
                'message' => $res['message'],
                'status'  => $res['status'],
            ]);
        }
        // on cover
        if($this->request->request->has('on_cover')) {
            $res = $this->change_cover($this->uid);
            return $this->json([
                'message' => $res['message'],
                'status'  => $res['status'],
            ]);
        }
        // on display
        if($this->request->request->has('on_display')) {
            $res = $this->change_display($this->uid);
            return $this->json([
                'message' => $res['message'],
                'status'  => $res['status'],
            ]);
        }
        return $this->json([
            'message' => 'Something bad happened',
            'status'  => '500',
        ]);
    }

    protected function change_bio($uid)
    {
        $connection = $this->connection;
        // User office first
        $name = trim($this->request->request->get('change_name'));
        $bio  = trim($this->request->request->get('change_bio'));
        $loc  = trim($this->request->request->get('change_loc'));

        // Do PHP validations
        $goodName = IndexFunction::goodName($name, 'A-Za-z0-9 ');
        $goodBio  = IndexFunction::goodName($bio, 'A-Za-z0-9\,\.\:\;\!\@\#\&\+\-\_\\n\'\" ');
        $goodLoc  = IndexFunction::goodName($loc, 'A-Za-z\, ');

        $goodSaveBio = IndexFunction::validateInput($bio);


        if( !empty($name) && !$goodName) {

            return [
                'message' => 'Name: Special characters',
                'status'  => 500,
            ];
        } elseif( !empty($loc) && !$goodLoc) {

            return [
                'message' => 'Location: Special characters',
                'status'  => 500,
            ];
        } else {
            // Check if you're giving me the same thing.
            $stmt = $connection->prepare("SELECT name, about, location FROM user_sapphire WHERE uid = ?");
            $stmt->bind_param("s", $uid);
            $stmt->execute();
            $check = $stmt->get_result();
            $check_row = $check->fetch_array(MYSQLI_ASSOC);

            // Get values
            $name_saved = stripslashes($check_row['name']);
            $bio_saved  = stripslashes($check_row['about']);
            $loc_saved  = stripslashes($check_row['location']);

            // Validate if same
            $same = ( $name==$name_saved && $bio==$bio_saved && $loc==$loc_saved );
            if( $same ) {

                return [
                    'message' => 'You made no changes to bio',
                    'status'  => 500,
                ];
            } else {

                $name_cleaned = IndexFunction::validateInput($name);
                $loc_cleaned  = IndexFunction::validateInput($loc);
                $bio_cleaned  = IndexFunction::test_input($bio);
                // Save the changes
                $stmt = $connection->prepare("UPDATE user_sapphire SET name=?, about=?, location=?, date=? WHERE uid=?");
                $stmt->bind_param("sssss", $name_cleaned, $bio_cleaned, $loc_cleaned, $date, $uid);
                $stmt->execute();

                # Success
                // Unset varaiables to free memory
                unset($connection, $stmt, $name, $bio, $loc, $name_saved, $bio_saved, $loc_saved, $name_cleaned, $loc_cleaned, $goodSaveBio, $same, $uid, $goodName, $goodLoc);
                return [
                    'message' => 'Success',
                    'status'  => 200,
                ];
            }
        }
    }

    protected function change_cover($uid)
    {
        $connection = $this->connection;
        $file_path  = $this->file_path;

        // Get the file components
        $file_error = $_FILES['cover']['error'];
        $file_type  = $_FILES['cover']['type'];
        $file_size  = $_FILES['cover']['size'];
        $file_tmp   = $_FILES['cover']['tmp_name'];
        $file_name  = IndexFunction::validateInput($_FILES['cover']['name']);

        // Set-up the necessary image changes
        $format    = explode('.', $file_name);
        $new_name  = round(microtime(true)).rand(13,53478).IndexFunction::randomKey(4).'.'.end($format);

        // Initiate the image changes
        $saveImage = $file_path.$new_name;
        $valid_types = array("image/jpeg", "image/jpg", "image/png");

        if( empty($file_name) ) {

            // field empty
            return [
                'message' => 'Select cover',
                'status'  => 500,
            ];
        } else if(in_array($file_type, $valid_types)) {

            if($file_size > 10247680 || file_exists($file_path.$new_name)) {

                // size exceeded limit 10MB or Name already exist -not likely to happen (the name part)
                return [
                    'message' => 'Cover image exceeds 10MB',
                    'status'  => 500,
                ];
            } else {

                if(!IndexFunction::nPhoto_resize($file_tmp, $file_size, $file_type, $saveImage)) {

                    $stmt=$connection->prepare('UPDATE user_sapphire SET cover = ?, date = ? WHERE uid = ?');
                    $stmt->bind_param('sss', $new_name, $date, $uid);
                    $stmt->execute();

                    // free-up memory
                    unset($connection, $stmt, $uid, $file_path, $saveImage, $valid_types, $format, $new_name, $file_error, $file_type, $file_size, $file_tmp, $file_name);
                    return [
                        'message' => 'Success',
                        'status'  => 200,
                    ];
                } else {
                    return [
                        'message' => 'Error encountered. Please retry',
                        'status'  => 500,
                    ];
                }
            }
        } else {

            return [
                'message' => 'Cover image type not supported',
                'status'  => 500,
            ];
        }
    }

    protected function change_display($uid)
    {
        $connection = $this->connection;
        $file_path  = $this->file_path;

        // Get the file components
        $file_error = $_FILES['display']['error'];
        $file_type  = $_FILES['display']['type'];
        $file_size  = $_FILES['display']['size'];
        $file_tmp   = $_FILES['display']['tmp_name'];
        $file_name  = IndexFunction::validateInput(IndexFunction::test_input($_FILES['display']['name']));

        // Set-up the necessary image changes
        $format    = explode('.', $file_name);
        $new_name  = round(microtime(true)).rand(13,53478).IndexFunction::randomKey(4).'.'.end($format);

        // Initiate the image changes
        $saveImage   = $file_path.$new_name;
        $saveShrink  = $file_path.'shk_'.$new_name;
        $valid_types = array("image/jpeg", "image/jpg", "image/png");

        if( empty($file_name) ) {
            // field empty
            return [
                'message' => 'Select display',
                'status'  => 500,
            ];
        } else if(in_array($file_type, $valid_types)) {

            if($file_size > 10247680 || file_exists($file_path.$new_name)) {

                // size exceeded limit 10MB or Name already exist -not likely to happen (the name part)
                return [
                    'message' => 'Display exceeds 10MB',
                    'status'  => 500,
                ];
            } else {

                if(!IndexFunction::nPhoto_square( $file_tmp, $file_size, $file_type, $saveImage, 200 ) && !IndexFunction::nPhoto_square( $file_tmp, $file_size, $file_type, $saveShrink, 50 )) {

                    $stmt=$connection->prepare('UPDATE user_sapphire SET display = ? WHERE uid = ?');
                    $stmt->bind_param('ss', $new_name, $uid);
                    $stmt->execute();

                    // Unset variables to free-up memory
                    unset($connection, $stmt, $uid, $file_path);
                    unset($file_error, $file_type, $file_size, $file_tmp, $file_name);
                    unset($format, $new_name);
                    unset($saveImage, $saveShrink, $valid_types);

                    // Upload success.
                    return [
                        'message' => 'Success',
                        'status'  => 200,
                    ];
                } else {
                    // error encountered
                    return [
                        'message' => 'Error encountered. Please retry',
                        'status'  => 500,
                    ];
                }
            }
        } else {
            // file type not supported
            return [
                'message' => 'Display file type not supported',
                'status'  => 500,
            ];
        }
    }
}