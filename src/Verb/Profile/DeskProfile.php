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
use App\Vunction\IndexFunction;


class DeskProfile extends AbstractController
{
    private $request;
    private $uid;
    private $file_path;
    private $connection_sur;

    public function __construct()
    {
        // get user_id
        $get_cookie = new RetrieveCookie();
        $this->uid = $get_cookie->get_netintui_user_id()['user_id'];

        // get request
        $this->request = Request::createFromGlobals();

        // set constants
        $this->file_path = dirname(__DIR__).'/../../public/images/community/notes/';

        // access database
        $this->connection_sur = new DatabaseAccess();
        $this->connection_sur = $this->connection_sur->connect('sur');
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

        $stmt = $this->connection_sur->prepare('SELECT sid FROM big_sur_draft WHERE uid = ? AND pid = ? AND access=1 ORDER BY sid DESC LIMIT 1');
        $stmt->bind_param('ss', $uid, $pid);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->num_rows;
        if( $rows > 0  ) {
            $stmt = $this->connection_sur->prepare('UPDATE big_sur_draft SET title=?, body=? WHERE uid=? AND pid=?');
            $stmt->bind_param('ssss', $title, $body, $uid, $pid);
            $stmt->execute();
            
            unset($stmt, $rows, $result, $title, $body, $pid, $uid);
            return [
                'message' => 'Updated',
                'status'  => 200,
            ];
        }

        $stmt = $this->connection_sur->prepare('INSERT INTO big_sur_draft (access, uid, pid, title, body) VALUES(1, ?, ?, ?, ?)');
        $stmt->bind_param('ssss', $uid, $pid, $title, $body);
        $stmt->execute();
        unset($stmt, $result, $rows, $title, $body, $pid, $uid);
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
            # Score = Paragraphs * First_Paragraphs_Words * Title Red Flags            
            $First_Paragraphs_Words = [0,1];
            $red = 0;
            $red_flags = 0;
            $flags = 0;
            (string) $note_score = $this->desk_score_algo($paragraphs, $First_Paragraphs_Words[0], $red_flags);
        #

        $stmt = $this->connection_sur->prepare('INSERT INTO big_sur (access, uid, pid, note_score) VALUES(1, ?, ?, ?)');
        $stmt->bind_param('sss', $uid, $pid, $note_score);
        $executeOne = $stmt->execute();

        if($executeOne)
        {
            $stmt = $this->connection_sur->prepare('INSERT INTO big_sur_list (access, state, pid, title, note, parags, cover, cover_extension) VALUES(1, ?, ?, ?, ?, ?, ?, ?)');
            $stmt->bind_param('sssssss', $which_editor, $pid, $title, $notes, $paragraphs, $img, $extensions);
            $executeTwo = $stmt->execute();

            $stmt = $this->connection_sur->prepare('INSERT INTO note_score_details (pid, paragraphs, red_flags, flags, score) VALUES(?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $pid, $paragraphs, $red_flags, $flags, $note_score);
            $executeScore = $stmt->execute();

            unset($uid, $pid, $title, $notes, $paragraphs, $img);
            unset($stmt, $executeOne, $executeTwo, $executeScore, $First_Paragraphs_Words, $red_flags, $flags, $note_score);
            return [
                'message' => 'Success',
                'status'  => 200,
            ];
        }
        unset($uid, $pid, $title, $body, $paragraphs, $img);
        unset($stmt, $executeOne, $executeTwo, $executeScore, $First_Paragraphs_Words, $red_flags, $flags, $note_score);
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