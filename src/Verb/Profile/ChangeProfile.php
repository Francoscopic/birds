<?php

namespace App\Verb\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Doctrine\DBAL\Connection;
use App\Validation\SigninValidation;
use App\Verb\Cookie\RetrieveCookie;
use App\Vunction\IndexFunction;


class ChangeProfile extends AbstractController
{
    private $request;
    private $uid;
    private $file_path;
    private $conn;

    public function __construct(Connection $connection)
    {
        // get user_id
        $get_cookie = new RetrieveCookie();
        $this->uid = $get_cookie->get_netintui_user_id()['user_id'];

        // get request
        $this->request = Request::createFromGlobals();

        // set constants
        $this->file_path = dirname(__DIR__).'/../../public/images/community/profiles/';

        // access database
        $this->conn = $connection;
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
            $name_saved = $bio_saved = $loc_saved = null;

            $stmt = $this->conn->fetchAssociative('SELECT id, name, about, location FROM user_sapphire WHERE uid = ?', [$uid]);

            if($stmt == true) {
                $name_saved = stripslashes($stmt['name']);
                $bio_saved  = stripslashes($stmt['about']);
                $loc_saved  = stripslashes($stmt['location']);
            }

            // Validate if same
            $same = ( $name==$name_saved && $bio==$bio_saved && $loc==$loc_saved );
            if( $same ) {

                return [
                    'message' => 'No changes observed',
                    'status'  => 500,
                ];
            } else {

                $name_cleaned = IndexFunction::validateInput($name);
                $loc_cleaned  = IndexFunction::validateInput($loc);
                $bio_cleaned  = IndexFunction::test_input($bio);
                // Save the changes
                $this->conn->update('user_sapphire', ['name'=>$name_cleaned, 'about'=>$bio_cleaned, 'location'=>$loc_cleaned], ['uid'=>$uid]);

                # Success
                // Unset varaiables to free memory
                unset($name, $bio, $loc, $name_saved, $bio_saved, $loc_saved, $name_cleaned, $loc_cleaned, $goodSaveBio, $same, $uid, $goodName, $goodLoc);
                return [
                    'message' => 'Success',
                    'status'  => 200,
                ];
            }
        }
    }

    protected function change_cover($uid)
    {
        $file_path  = $this->file_path;

        // Get the file components
        $file_error = $_FILES['cover']['error'];
        $file_type  = $_FILES['cover']['type'];
        $file_size  = $_FILES['cover']['size'];
        $file_tmp   = $_FILES['cover']['tmp_name'];
        $file_name  = IndexFunction::validateInput($_FILES['cover']['name']);

        // Set-up the necessary image changes
        $format    = explode('.', $file_name);
        $new_name  = IndexFunction::randomKey(11).'.'.end($format);

        // Initiate the image changes
        $saveImage = ['other'=>$file_path.$new_name];
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

                    // update database
                    $this->conn->update('user_sapphire', ['cover'=>$new_name], ['uid'=>$uid]);

                    // free-up memory
                    unset($uid, $file_path, $saveImage, $valid_types, $format, $new_name, $file_error, $file_type, $file_size, $file_tmp, $file_name);
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
        $file_path  = $this->file_path;

        // Get the file components
        $file_error = $_FILES['display']['error'];
        $file_type  = $_FILES['display']['type'];
        $file_size  = $_FILES['display']['size'];
        $file_tmp   = $_FILES['display']['tmp_name'];
        $file_name  = IndexFunction::validateInput(IndexFunction::test_input($_FILES['display']['name']));

        // Set-up the necessary image changes
        $format    = explode('.', $file_name);
        $new_name  = IndexFunction::randomKey(11).'.'.end($format);

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

                    // update database
                    $this->conn->update('user_sapphire', ['display'=>$new_name], ['uid'=>$uid]);

                    // Unset variables to free-up memory
                    unset($uid, $file_path, $file_error, $file_type, $file_size, $file_tmp, $file_name, $format, $new_name, $saveImage, $saveShrink, $valid_types);

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
