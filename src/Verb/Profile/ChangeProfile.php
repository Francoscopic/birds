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

    public function __construct()
    {
        // get user_id
        $get_cookie = new RetrieveCookie();
        $this->uid = $get_cookie->get_netintui_user_id()['user_id'];

        // get request
        $this->request = Request::createFromGlobals();
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
        if($this->request->request->has('change_on_cover')) {
            $res = $this->change_cover($this->uid);
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
                'status'  => 500,
                'message' => 'Name: Special characters',
            ];
        } elseif( !empty($loc) && !$goodLoc) {

            return [
                'status'  => 500,
                'message' => 'Location: Special characters',
            ];
        } else {
            // Check if you're giving me the same thing.
            $connection = new DatabaseAccess();
            $connection = $connection->connect('');
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
                    'status'  => 500,
                    'message' => 'You made no changes',
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
                return [
                    'status'  => 200,
                    'message' => 'Update success',
                ];
            }

            // Unset varaiables to free memory
            unset($connection_sur, $stmt, $name, $bio, $loc, $name_saved, $bio_saved, $loc_saved, $name_cleaned, $loc_cleaned, $goodSaveBio, $same, $uid, $goodName, $goodLoc);
        }
    }

    protected function change_cover($uid)
    {
        // Initiate the paths
        $file_path = dirname(__DIR__).'/public/images/community/profiles/';

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
            echo 'Select image';
            return $this->json([
                'message' => 'Select image',
                'status'  => 500,
            ]);
        } else if(in_array($file_type, $valid_types)) {

            if($file_size > 10247680 || file_exists($file_path.$new_name)) {

                // size exceeded limit 10MB or Name already exist -not likely to happen (the name part)
                return $this->json([
                    'message' => 'File exceeds 10MB',
                    'status'  => 500,
                ]);
            } else {

                if(!IndexFunction::nPhoto_resize($file_tmp, $file_size, $file_type, $saveImage)) {

                    $stmt=$connection->prepare('UPDATE user_sapphire SET cover = ?, date = ? WHERE uid = ?');
                    $stmt->bind_param('sss', $new_name, $date, $uid);
                    $stmt->execute();

                    // free-up memory
                    unset($connection, $stmt, $uid, $file_path, $saveImage, $valid_types, $format, $new_name, $file_error, $file_type, $file_size, $file_tmp, $file_name);
                    return $this->json([
                        'message' => 'Upload success',
                        'status'  => 200,
                    ]);
                } else {
                    return $this->json([
                        'message' => 'Error encountered. Please retry',
                        'status'  => 500,
                    ]);
                }
            }
        } else {

            return $this->json([
                'message' => 'File type not supported',
                'status'  => 500,
            ]);
        }
    }
}