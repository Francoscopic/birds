<?php

namespace App\Verb\Profile;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

use Doctrine\DBAL\Connection;
use App\Verb\Cookie\RetrieveCookie;
use App\Vunction\IndexFunction;


class DeskProfile extends AbstractController
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
        $this->file_path = dirname(__DIR__).'/../../public/images/community/notes/';

        // access database
        $this->conn = $connection;
    }

    public function index(): JsonResponse
    {
        // save article for later
        if($this->request->request->has('desk_save_draft')) {
            $res = $this->desk_saveForLater($this->uid);
            return $this->json([
                'message' => $res['message'],
                'status'  => $res['status'],
            ]);
        }
        if($this->request->request->has('mydesk')) {
            $res = $this->desk_save_selector($this->uid);
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

    protected function desk_saveForLater($uid)
    {
        $title = $this->request->request->get('desk_save_title');
        $body  = $this->request->request->get('desk_save_body');
        $pid   = $this->request->request->get('desk_save_pid');

        $stmt = $this->conn->fetchOne('SELECT COUNT(id) AS rows FROM big_sur_draft WHERE uid = ? AND pid = ? AND access=1 ORDER BY sid DESC LIMIT 1', [$uid, $pid]);
        if($stmt == true) {
            if( $stmt['rows'] > 0  ) {
                $this->conn->update('big_sur_draft', ['title'=>$title, 'body'=>$body], ['uid'=>$uid, 'pid'=>$pid]);
                
                unset($stmt, $title, $body, $pid, $uid);
                return [
                    'message' => 'Updated',
                    'status'  => 200,
                ];
            }
        }

        $this->conn->insert('big_sur_draft', ['access'=>1, 'uid'=>$uid, 'pid'=>$pid, 'title'=>$title, 'body'=>$body]);
        unset($title, $body, $pid, $uid);
        return [
            'message' => 'Saved',
            'status'  => 200,
        ];
    }

    protected function desk_save_selector($uid)
    {
        $notes_save_handle = 0;   # 0 for articles, 1 for images.
        $image_extensions  = '';

        $title = IndexFunction::validateInput($this->request->request->get('ttl'));
        $body  = $this->request->request->get('nt');
        $pid   = IndexFunction::randomKey(9);

        #
            # Get the file components
            $cover_type         = $_FILES['cover']['type'];
            $cover_tmp          = $_FILES['cover']['tmp_name'];
            $cover_size         = $_FILES['cover']['size'];
            $cover_name         = IndexFunction::validateInput($_FILES['cover']['name']);  # Clean.
            $cover_format       = explode('.', $cover_name);
            $cover_new_name     = IndexFunction::randomKey(5).round(microtime(true));
            $cover_new_name_ext = $cover_new_name.'.'.end($cover_format);

            # Initiate the image changes
            $save_cover_image   = $this->file_path.$cover_new_name_ext;

            $image_extensions .= end($cover_format);
        #

        if(
            $this->request->request->has('editor')
            &&
            $this->request->request->get('editor') == 1
        )
        { // images
            $number_of_images = count($_FILES['images']['name']);
            for( $i=0; $i<$number_of_images; $i++ ) {
                $image_tmp      = $_FILES['images']['tmp_name'][$i];
                $image_name     = IndexFunction::validateInput($_FILES['images']['name'][$i]);
                $image_format   = explode('.', $image_name);
                $image_new_name = $cover_new_name .'-'. $i .'.'.end($image_format);

                move_uploaded_file($image_tmp, $this->file_path.$image_new_name);
                $image_extensions .= ','.end($image_format);
            }
            $notes_save_handle = 1;
        } else { //article
            $body              = IndexFunction::validateInput(trim($body));
            $paragraphs        = IndexFunction::count_paragraphs($body);
            $notes_save_handle = 0;
        }

        if($notes_save_handle == 0) { # Save article

            $cover_upload = !IndexFunction::nPhoto_resize( $cover_tmp, $cover_size, $cover_type, $save_cover_image );
            if($cover_upload) {
                $res = $this->desk_save($uid, $pid, $title, $body, $paragraphs, $cover_new_name_ext, 'art', '');
                return [
                    'message' => $res['message'],
                    'status'  => $res['status'],
                ];
            }
        } else { # Save images

            $cover_upload = !IndexFunction::nPhoto_resize( $cover_tmp, $cover_size, $cover_type, $save_cover_image );
            if($cover_upload) {
                $res = $this->desk_save($uid, $pid, $title, '', $number_of_images, $cover_new_name_ext, 'img', $image_extensions);
                return [
                    'message' => $res['message'],
                    'status'  => $res['status'],
                ];
            }
        }

        unset($title, $notes, $uid);
        unset($paragraph_array, $first_paragraph, $paragraphs, $pid);
        unset($file_type, $file_size, $file_tmp, $file_name, $format, $new_name, $saveImage, $valid_types);
    }

    protected function desk_save($uid, $pid, $title, $notes, $paragraphs, $img, $which_editor, $extensions)
    {
        # Note scoring
            # Score = Flesch-Kincaid
            $score = 0;
            (string) $note_score = $this->desk_score_algo($score);
        #

        $stmt = $this->conn->insert('big_sur', ['access'=>1, 'uid'=>$uid, 'pid'=>$pid]);

        if($stmt == true)
        {
            $this->conn->insert('big_sur_list', ['access'=>1, 'state'=>$which_editor, 'pid'=>$pid, 'title'=>$title, 'note'=>$notes, 'parags'=>$paragraphs, 'cover'=>$img, 'cover_extension'=>$extensions]);

            // $this->conn->insert('big_sur_fkscore', []);

            unset($uid, $pid, $title, $notes, $paragraphs, $img);
            return [
                'message' => 'Success',
                'status'  => 200,
            ];
        }
        unset($uid, $pid, $title, $body, $paragraphs, $img);
        return [
            'message' => 'Error encountered',
            'status'  => 500,
        ];
    }

    protected function desk_score_algo(...$a)
    {
        return 200;
    }
}