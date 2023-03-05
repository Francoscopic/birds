<?php

namespace App\Vunction;

use App\Database\DatabaseAccess;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

class IndexFunction
{
    public static function user_profile_state($uid): array
    {
        $user_file = self::retrieve_details($uid);

        $username       = strtolower($user_file['username']);
        $name           = stripslashes($user_file['name']);
        $state          = ($user_file['state'] === 1) ? 'darkmode' : 'lightmode';
        $display        = $user_file['display'];
        $display_small  = $user_file['display_small'];
        return array(
            'name'          => $name,
            'username'      => $username,
            'state'         => $state,
            'display'       => $display,
            'display_small' => $display_small,
        );
    }

    public static function get_user_state($user_id, $visit_state = false): array
    {
        $connection = new DatabaseAccess();
        $connection = $connection->connect('');

        if( $visit_state == true ) {
            return array(
                'state' => 0,
                'logo'  => self::get_path('images').'/logo/notes.png',
            );
        }
        
        $stmt = $connection->prepare('SELECT state FROM user_sapphire WHERE uid = ?');
        $stmt->bind_param('s', $user_id);
        $stmt->execute();

        # Get the array of data from database
            $get_state_array = $stmt->get_result();
            $state_array = $get_state_array->fetch_array(MYSQLI_ASSOC);
        # Instantiate the variables
        $state = $state_array['state']; // Dark or Light
        $theme_logo = ($state == true) ? self::get_path('images').'/logo/notes-white.png' : self::get_path('images').'/logo/notes.png';

        unset($stmt, $connection, $get_state_array, $state_array, $user_id);
        return array(
            'state'=>$state,
            'logo' => $theme_logo,
        );
    }

    public static function imgNomenclature($file): array
    {
        if(file_exists($file)) {
            list($width, $height, $type, $attr) = getimagesize($file);
            return array('width'=>$width, 'height'=>$height);
        }
        return array(
            'width' => '',
            'height' => ''
        );
    }

    public static function retrieve_details($user_id): array
    {
        if ($user_id === false) {
            return array(
                'name'          => 'John Doe',
                'username'      => 'john_doe',
                'state'         => false,
                'display'       => '',
                'display_small' => '',
            );
        }

        $connection = new DatabaseAccess();
        $connection = $connection->connect('');

        $stmt = $connection->prepare('SELECT uname, name, state, display FROM user_sapphire WHERE uid = ? OR uname = ? OR name = ? ');
        $stmt->bind_param('sss', $user_id, $user_id, $user_id);
        $stmt->execute();
        // Get the array of data from database
        $the_result = $stmt->get_result();
        $result_array = $the_result->fetch_array(MYSQLI_ASSOC);

        // Instantiate the variables
        $username       = $result_array['uname'];
        $name           = $result_array['name'];
        $state          = $result_array['state'];
        $display        = self::image_file_paths('profile')['content'] . $result_array['display'];
        $display_small  = self::image_file_paths('profile')['content'] . 'shk_' . $result_array['display'];

        unset($stmt, $connection, $user_id, $the_result, $result_array);
        // Send them to page
        return array(
            'username'      => $username,
            'name'          => $name,
            'state'         => $state,
            'display'       => $display,
            'display_small' => $display_small,
        );
    }

    public static function timeAgo($date): string
    {
        $timestamp = strtotime($date);

        $strTime = array("second", "minute", "hour", "day", "month", "year");
        $length = array("60","60","24","30","12","10");

        $currentTime = time();
        if($currentTime >= $timestamp) {
            $diff     = time()- $timestamp;
            for($i = 0; $diff >= $length[$i] && $i < count($length)-1; $i++) {
                $diff = $diff / $length[$i];
            }

            $diff = round($diff);
            if($diff === 1) {
                return "Now";
            } else {
                return $diff." ".$strTime[$i]."s";
            }
        }
    }

    public static function ShowMore($string, $limit = 30): string
    {
        $words = array();
        $words = explode(" ", $string, $limit);
        $New_String = "";
        $lastWord_no = ($limit - 1);
        if(count($words) === $limit) {
            $words[$lastWord_no] = "...";
        }
        $New_String = implode(" ", $words);
        return $New_String;
    }

    public static function cleanRead($note): string
    {
        # Make the Note have enough linebreak space for easy read.
        $order = array("\r\n", "\n", "\r", "\\r\\n", "\\n", "\\r");
        $opt = "\n\n";

        $replace = str_replace($order, $opt, $note);
        return (stripslashes($replace));
    }

    public static function clearBreak($text): string
    {
        $order = array('\r\n', '\n', '\r', 'rn');
        $replace = "<br>";
        $postedMsg = stripslashes(nl2br(str_replace($order, $replace, $text)));
        return $postedMsg;
    }

    public static function get_me($theUid): array
    {
        $connection = new DatabaseAccess();
        $connection = $connection->connect('');

        $stmt=$connection->prepare("SELECT name, uname FROM user_sapphire WHERE uid = ?");
        $stmt->bind_param('s', $theUid);
        $stmt->execute();
        $theResult = $stmt->get_result();
        $theResult_row = $theResult->fetch_array(MYSQLI_ASSOC);
        $myName = $theResult_row['name'];
        $myUname = $theResult_row['uname'];

        unset($stmt, $connection, $theResult, $theResult_row, $theUid);
        return array(
            'name'     => $myName,
            'username' => $myUname
        );
    }

    public static function get_profile_uid($the_username=''): array 
    {
        $connection = new DatabaseAccess();
        $connection = $connection->connect('');

        $stmt=$connection->prepare("SELECT uid FROM user_sapphire WHERE uname = ?");
        $stmt->bind_param('s', $the_username);
        $stmt->execute();
        $theResult = $stmt->get_result();

        if($theResult->num_rows == 0) {
            return [
                'message' => 'not-found',
                'uid'    => null,
            ];
        }

        $theResult_row = $theResult->fetch_array(MYSQLI_ASSOC);
        $my_uid = $theResult_row['uid'];

        return [
            'message' => 'found',
            'uid'    => $my_uid,
        ];
    }

    public static function get_this_note($thePid): array
    {

        # Get the needed information of me.
        $connection_sur = new DatabaseAccess();
        $connection_sur = $connection_sur->connect('sur');

        $stmt = $connection_sur->prepare('SELECT title, parags, cover, state, date FROM big_sur_list WHERE pid = ?');
        $stmt->bind_param('s', $thePid);
        $stmt->execute();
        $get_result = $stmt->get_result();

        # array
        $result_array = $get_result->fetch_array(MYSQLI_ASSOC);

        # variables
        $title            = $result_array['title'];
        $paragraphs       = $result_array['parags'];
        $cover            = $result_array['cover'];
        $article_or_image = $result_array['state'];
        $date             = $result_array['date'];

        return array(
            'title'      => $title,
            'paragraphs' => $paragraphs,
            'cover'      => $cover,
            'state'      => $article_or_image,
            'date'       => $date
        );
    }

    public static function get_if_views($note_id, $viewer_id): bool
    {
        $connection_verb = new DatabaseAccess();
        $connection_verb = $connection_verb->connect('verb');

        // $stmt = $connection_verb->prepare('SELECT DISTINCT(sid) FROM views WHERE pid=? AND uid=? ORDER BY sid DESC');
        $stmt = $connection_verb->prepare('SELECT DISTINCT(visit_id) FROM visits WHERE post_id=? AND user_id=?');
        $stmt->bind_param('ss', $note_id, $viewer_id);
        $stmt->execute();
        $get_result = $stmt->get_result();

        if( $get_result->num_rows >= 1 ) {
            unset($stmt, $connection_verb, $get_result, $note_id, $viewer_id);
            return true;
        }
        unset($stmt, $connection_verb, $get_result, $note_id, $viewer_id);
        return false;
    }

    public static function get_note_views($note_id): string
    {
        # Call connection
        $connection_verb = new DatabaseAccess();
        $connection_verb = $connection_verb->connect('verb');

        $stmt = $connection_verb->prepare('SELECT sid FROM views WHERE pid = ?');
        $stmt->bind_param('s', $note_id);
        $stmt->execute();
        $get_result = $stmt->get_result();
        $rows = $get_result->num_rows;

        if( $rows >= 1 ) {
            unset($connection_verb, $note_id, $stmt, $get_result);
            return ($rows === 1) ? $rows.' view' : $rows.' views';
        }
        unset($connection_verb, $note_id, $stmt, $get_result);
        return 'no view';
    }

    public static function get_subscribe_state($publisher_id, $customer_id): string
    {
        $connection_sur = new DatabaseAccess();
        $connection_sur = $connection_sur->connect('sur');

        $stmt = $connection_sur->prepare('SELECT state FROM subscribes WHERE publisher = ? AND customer = ?');
        $stmt->bind_param('ss', $publisher_id, $customer_id);
        $stmt->execute();
        $get_state_array = $stmt->get_result();
        # Check if data exists
        if( $get_state_array->num_rows > 0 ) {
            $state = $get_state_array->fetch_array(MYSQLI_ASSOC)['state'];
            unset($stmt, $connection_sur, $publisher_id, $customer_id, $get_state_array);
            return $state;
        }
        unset($stmt, $connection_sur, $publisher_id, $customer_id, $get_state_array, $state);
        return 0;
    }

    public static function subscribe_state_variables($the_state): array
    {
        # Create and return values that show subscribtion state
        $text = 'SUBSCRIBE';
        $state = '';
        if( $the_state == 1 ) {
            $text = 'SUBSCRIBED';
            $state = 'checked';
        }
        return array(
            'title' => $text,
            'state' => $state
        );
    }

    public static function show_more($start, $end): string
    {
        $more = '
        <div class="note-footer-auxiliary desktop mobile">
            <div class="ptt-moreNotes _$hide_more">
                <a id="grow-home" class="ft-sect a" href="#">
                    more <i class="fa fa-arrow-right trn3"></i>
                </a>
                <span id="grow-notif" class="sm-i hd ft-sect"></span>
            </div>
        </div>';
        return ($start == $end) ? $more : '';
    }

    # Home
        public static function note_cover_article($file): string
        {
            return $file;
        }
        public static function note_cover_extensions($cover, $exts): array
        {
            if(empty($exts)) {
                return array('images'=>'');
            }
            $images = '';
            $cover_name = explode('.', $cover)[0];
            $each_exts = explode(',', $exts);
            $num_each_exts = count($each_exts);
            for($i=1; $i<$num_each_exts; $i++) {

                $path = self::note_cover_article($cover_name.'-'.($i-1).'.'.$each_exts[$i]);
                $images .= '<img src="'. $path .'" alt="" />';
            }
            return array(
                'images' => $images
            );
        }
        public static function note_cover($file, $typ, $path='images', $size='small'): string
        {
            # Check other pages and fit directory, properly
            /* DOCUMENT:
                uid: user ID,
                file: link to file,
                type: "notes" or "profile",
            */
            $test_default = self::def_cover_display($file, $path);
            if($test_default == false) {
                $dir_a = self::image_file_paths('note')['content'] . $file;
                $dir_b = self::image_file_paths('profile')['content'] . self::get_size($size) . $file;

                $dir = ($typ == 'notes') ? $dir_a : $dir_b; # Find which folder: notes/profile

                $return = file_exists($dir) ? $dir : $dir; //to debug: replace "not-found" with $dir
                return $return;
            }
            return $test_default;
        }
        public static function get_display($file, $path='images'): string
        {
            $disp_name = explode('.', $file)[0];
            if($disp_name == 'display') return get_path($path).'/users/display.jpg';
            $dir = ($path=='images') ? ('../../community/profiles/shk_'.$file) : ('/community/profiles/shk_'.$file);
            return file_exists($dir) ? $dir : (get_path($path).'/users/display.jpg');
        }
        public static function def_cover_display($file, $path)
        {
            $disp_name = explode('.', $file)[0];
            if($disp_name == 'display') return self::image_file_paths('user')['content'] . 'display.jpg';
            if($disp_name == 'cover') return self::image_file_paths('user')['content'] . 'cover.jpg';
            return false;
        }
        public static function get_path($page): string
        {
            switch ($page) {
                case 'images':
                    return '/images';
                    break;
                case 'scripts':
                    return '/scripts';
                case 'stylesheets':
                    return '/stylesheets';
                default:
                    return '';
                    break;
            }
        }
        public static function get_size($size): string
        {
            $st = in_array($size, array('small','sm'));
            return ($st == true) ? 'shk_' : '';
        }
        public static function small_menu_validations($pid, $viewer_uid): array
        {
            $connection_verb = new DatabaseAccess();
            $connection_verb = $connection_verb->connect('verb');
            $save_state = $like_state = 0;

            # SAVES
            $stmt = $connection_verb->prepare('SELECT sid FROM saves WHERE uid = ? AND pid = ? AND state = 1 ');
            $stmt->bind_param('ss', $viewer_uid, $pid);
            $stmt->execute();
            $save_state = ($stmt->get_result()->num_rows == 1) ? 1 : 0;

            # LIKES
            $stmt = $connection_verb->prepare('SELECT sid FROM likes WHERE uid = ? AND pid = ? AND state = 1 ');
            $stmt->bind_param('ss', $viewer_uid, $pid);
            $stmt->execute();
            $like_state = ($stmt->get_result()->num_rows == 1) ? 1 : 0;

            # UN-LIKES
            $stmt = $connection_verb->prepare('SELECT sid FROM unlikes WHERE uid = ? AND pid = ? AND state = 1 ');
            $stmt->bind_param('ss', $viewer_uid, $pid);
            $stmt->execute();
            $unlike_state = ($stmt->get_result()->num_rows == 1) ? 1 : 0;

            return array(
                'save' => $save_state,
                'like' => $like_state,
                'unlike' => $unlike_state
            );
        }
    #

    # Article
        public static function GET_validate($get): bool
        { # Is article found
            $connection_sur = new DatabaseAccess();
            $connection_sur = $connection_sur->connect('sur');

            $stmt = $connection_sur->prepare('SELECT sid FROM big_sur WHERE pid = ?');
            $stmt->bind_param('s', $get);
            $stmt->execute();
            $result = $stmt->get_result();

            return ( $result->num_rows > 0 ) ? true : false;
        }
        public static function GET_validate_people($get): array
        { # Is GET request name valid
            $connection = new DatabaseAccess();
            $connection = $connection->connect('');
            $stmt = $connection->prepare('SELECT uid FROM user_sapphire WHERE uname = ?');
            $stmt->bind_param('s', $get);
            $stmt->execute();
            $result = $stmt->get_result();

            if( $result->num_rows > 0 ) {
                $row = $result->fetch_array(MYSQLI_ASSOC);
                $uid = $row['uid'];
                unset($connection, $stmt, $result, $row);
                return array($uid, 0);
            }
            unset($connection, $stmt, $result, $row, $uid);
            return array('1999', 1);
        }

        public static function get_my_note($thePid): array
        {
            // Get the needed information of me.
            $connection_sur = new DatabaseAccess();
            $connection_sur = $connection_sur->connect('sur');

            $stmt = $connection_sur->prepare('SELECT bs.uid, bsl.title,
                                                    bsl.note, bsl.cover, bsl.cover_extension, bsl.date
                                                    FROM big_sur bs INNER JOIN big_sur_list bsl
                                                    ON bs.pid = bsl.pid
                                                    WHERE bs.pid = ?');
            $stmt->bind_param('s', $thePid);
            $stmt->execute();
            $get_result = $stmt->get_result();

            // Get the array of data from database
            $result_array = $get_result->fetch_array(MYSQLI_ASSOC);

            // Instantiate the variables
            $title      = $result_array['title'];
            $note       = $result_array['note'];
            $cover      = self::image_file_paths('note')['content'] . self::get_size('small') . $result_array['cover'];
            $cover_lg   = self::image_file_paths('note')['content'] . $result_array['cover'];
            $extensions = $result_array['cover_extension'];
            $date       = $result_array['date'];

            $note_poster_id = $result_array['uid'];

            unset($connection_sur, $stmt, $get_result, $result_array);
            // Send them to page
            return array(
                'poster_id'  => $note_poster_id,
                'post_id'    => $thePid,
                'title'      => $title,
                'note'       => $note,
                'cover'      => $cover,
                'cover_full' => $cover_lg,
                'extensions' => $extensions,
                'date'       => $date,
            );
        }
        public static function get_note_poster($theUid): array
        {
            if ($theUid == false) {
                return array('name'=>'John Doe', 'username'=>'john_doe', 'state'=>false, 'display'=>'');
            }

            // Call database
            $connection = new DatabaseAccess();
            $connection = $connection->connect('');

            // Search database for the poster's details
            $stmt = $connection->prepare('SELECT name, uname, display FROM user_sapphire WHERE uid = ?');
            $stmt->bind_param('s', $theUid);
            $stmt->execute();

            // Get data
            $get_result   = $stmt->get_result();
            $result_array = $get_result->fetch_array(MYSQLI_ASSOC);

            // Instantiate variables
            $name      = $result_array['name'];
            $user_name = strtolower($result_array['uname']);
            $display   = self::image_file_paths('profile')['content'] . self::get_size('small') . $result_array['display'];

            // Kill variables
            unset($stmt, $connection, $result_array, $get_result, $theUid);

            // Return variables
            return array(
                'name'     => $name,
                'username' => $user_name,
                'display'  => $display
            );
        }
        public static function get_poster_uid($post_id): array
        {
            // Database Access
            $connection_sur = new DatabaseAccess();
            $connection_sur = $connection_sur->connect('sur');

            $stmt = $connection_sur->prepare('SELECT uid FROM big_sur WHERE pid = ?');
            $stmt->bind_param('s', $post_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $num_rows = $result->num_rows;
            $row = $result->fetch_array(MYSQLI_ASSOC);
            $poster_uid = $row['uid'];

            return array(
                'uid'     => $poster_uid,
                'message' => 'Get UID with PID',
            );
        }
        public static function save_like_verb($table, $theUid, $thePosterUid, $thePid, $type, $state = 1): array
        {
            $connection_verb = new DatabaseAccess();
            $connection_verb = $connection_verb->connect('verb');
            $stmt = $connection_verb->prepare("SELECT sid FROM $table WHERE uid = ? AND puid = ? AND pid = ? AND state = ?");
            $stmt->bind_param('ssss', $theUid, $thePosterUid, $thePid, $state);
            $stmt->execute();
            $getResult = $stmt->get_result();
            # Instantiate the verbs
            $checked = '';
            if($type == 'like') {
                $icon = 'far fa-thumbs-up';
            } elseif($type == 'unlike') {
                $icon = 'far fa-thumbs-down';
            } elseif($type == 'save') {
                $icon = 'far fa-bookmark';
            }
            if( $getResult->num_rows > 0 ) {
                # Return handler: true = NOTED
                $checked = 'checked';
                if($type == 'like') {
                    $icon = 'fas fa-thumbs-up';
                }
                if($type == 'unlike') {
                    $icon = 'fas fa-thumbs-down';
                }
                if($type == 'save') {
                    $icon = 'fas fa-bookmark';
                }
                unset($connection_verb, $stmt, $getResult, $table, $theUid, $thePosterUid, $thePid, $type, $state);
                return array('icon'=>$icon, 'check'=>$checked);
            }
            unset($connection_verb, $stmt, $getResult, $table, $theUid, $thePosterUid, $thePid, $type, $state);
            # Return handler: false = NOT NOTED
            return array(
                'icon'=>$icon,
                'check'=>$checked
            );
        }
        public static function renoted($theUid, $thePosterUid, $thePid, $state = 1): array
        {
            $connection_verb = new DatabaseAccess();
            $connection_verb = $connection_verb->connect('verb');
            $stmt = $connection_verb->prepare('SELECT sid FROM renotes WHERE uid = ? AND puid = ? AND pid = ? AND state = ?');
            $stmt->bind_param('ssss', $theUid, $thePosterUid, $thePid, $state);
            $stmt->execute();
            $getResult = $stmt->get_result();

            # Instantiate the verbs
            $color = '';
            $disabled = '';

            # if row is greater than zero.
            # Meaning: you RENOTED this Note (Does that make sense?)
            if( $getResult->num_rows > 0 ) {
                # Return handler: true = RENOTED
                $color = 'note-buttons-clicked';
                $disabled = 'disabled';
                unset($connection_verb, $stmt, $getResult, $theUid, $thePosterUid, $thePid, $state);
                return array('color'=>$color, 'state'=>$disabled);
            }
            unset($connection_verb, $stmt, $getResult, $theUid, $thePosterUid, $thePid, $state);
            # Return handler: false = NOT RENOTED
            return array(
                $color,
                $disabled
            );
        }
        public static function verb_number($thePid, $table, $state = 1): array
        {
            $connection_verb = new DatabaseAccess();
            $connection_verb = $connection_verb->connect('verb');
            $stmt = $connection_verb->prepare("SELECT pid, puid FROM $table WHERE pid = ? AND state = ?");
            $stmt->bind_param('ss', $thePid, $state);
            $stmt->execute();
            $getResult = $stmt->get_result();
            # Instantiate the verbs
            $number = $getResult->num_rows;
            # if rows is zero.
            if( $getResult->num_rows === 0 ) {
                # Return number of NOTES
                $number = 'Like';
                unset($connection_verb, $stmt, $getResult, $thePid, $table, $state);
                return array(
                    'number' => $number
                );
            }
            unset($connection_verb, $stmt, $getResult, $thePid, $table, $state);
            # Return handler: false = NOT NOTED
            return array(
                'number' => $number . ' likes',
            );
        }
        public static function note_views($id)
        {
            $connection_verb = new DatabaseAccess();
            $connection_verb = $connection_verb->connect('verb');
            $stmt = $connection_verb->prepare('SELECT DISTINCT(user_id) FROM visits WHERE post_id = ?');
            $stmt->bind_param('s', $id);
            $stmt->execute();
            $ans = ($stmt->get_result())->num_rows;
            unset($connection_verb, $stmt, $id);
            return $ans === 0 ? null : $ans;
        }
        public static function get_comment($comment_id): array
        {
            $connection_verb = new DatabaseAccess();
            $connection_verb = $connection_verb->connect('verb');

            $stmt = $connection_verb->prepare("SELECT comment, date FROM comments_list WHERE cid = ?");
            $stmt->bind_param("s", $comment_id);
            $stmt->execute();

            $result = $stmt->get_result();
            $rows = $result->fetch_array(MYSQLI_ASSOC);

            $comment = $rows['comment'];
            $date = $rows['date'];

            // Kill the variables
            unset($connection_verb, $stmt, $result, $rows, $comment_id);
            return array(
                'comment'=>$comment,
                'date'=>$date
            );
        }
        public static function get_comment_poster($commenter_id): array
        {
            $connection = new DatabaseAccess();
            $connection = $connection->connect('');

            $stmt = $connection->prepare("SELECT name, uname FROM user_sapphire WHERE uid = ?");
            $stmt->bind_param("s", $commenter_id);
            $stmt->execute();

            $result = $stmt->get_result();
            $rows = $result->fetch_array(MYSQLI_ASSOC);

            $name = $rows['name'];
            $uname = $rows['uname'];

            // Kill the variables
            unset($connection, $stmt, $result, $rows, $commenter_id);
            return array(
                'name'=>$name,
                'username'=>$uname
            );
        }
        public static function get_comments_number($note_id, $reason = 'number', $comment_no = 200): array
        {
            $connection_verb = new DatabaseAccess();
            $connection_verb = $connection_verb->connect('verb');
            
            $stmt = $connection_verb->prepare("SELECT sid FROM comments WHERE pid = ?");
            $stmt->bind_param("s", $note_id);
            $stmt->execute();
            $rows_number = ($stmt->get_result())->num_rows;

            if( $rows_number >= $comment_no && $reason === 'more' ) {

                $show_more = '<a href="#" class="notes-more-comments calib a"><p>more <span class="trn3"><i class="sm-i fa fa-arrow-right"></i></span></p></a>';
                unset($connection_verb, $stmt);
                return array($show_more);
            } elseif( $rows_number <= $comment_no && $reason === 'more' ) {
                unset($connection_verb, $stmt);
                return array('');
            } elseif( $rows_number === 0 ) {
                unset($connection_verb, $stmt);
                return array('Comment');
            } else {
                unset($connection_verb, $stmt);
                return array($rows_number);
            }
        }
        public static function last_read_note($uid): array
        {
            $connection_verb = new DatabaseAccess();
            $connection_verb = $connection_verb->connect('verb');
            $stmt = $connection_verb->prepare('SELECT pid FROM views WHERE uid = ? ORDER BY sid DESC LIMIT 1');
            $stmt->bind_param('s', $uid);
            $stmt->execute();
            $result=$stmt->get_result();
            $row = $result->fetch_array(MYSQLI_ASSOC);

            unset($connection_verb, $stmt, $result, $uid);
            return array(
                $row['pid']
            );
        }
        public static function note_font_family($theFont): string
        {
            $font_families = [
                'lato'=> "'Lato', calibri, sans-serif",
                'playfair'=> "'Playfair Display', serif",
                'roboto'=> "'Roboto', sans-serif",
                'lora'=> "'Lora', serif",
                'calibri'=> "'Calibri light', calibri, sans-serif",
                ''=>''
            ];
            return $font_families[$theFont];
        }
        public static function test_input($data)
        {
            // $transformed = trim($data);
            $transformed = filter_var($data, FILTER_UNSAFE_RAW);
            return $transformed;
        }
        public static function article_validate_post_id($post_id)
        {
            return ( self::GET_validate($post_id) === true ) ? true : false;
        }
    #

    # Profile
        public static function profile_user_figures($user_id): array
        {
            # This function tends to get the user display image and cover image.
            # The key to finding everything is the session-user-id from *uid*

            $connection_sur = new DatabaseAccess();
            $connection_sur = $connection_sur->connect('');

            $stmt = $connection_sur->prepare('SELECT uname, name, email, state, location, website, about, cover, display FROM user_sapphire WHERE uid=?');
            $stmt->bind_param('s', $user_id);
            $stmt->execute();

            # Get the array of data from database
            $get_figures_array = $stmt->get_result();
            $figures_array = $get_figures_array->fetch_array(MYSQLI_ASSOC);

            # Instantiate the variables
            $username = $figures_array['uname'];
            $name     = $figures_array['name'];
            $email    = $figures_array['email'];
            $state    = $figures_array['state'];
            $location = $figures_array['location'];
            $website  = $figures_array['website'];
            $bio      = $figures_array['about'];
            $cover    = self::image_file_paths('profile')['content'] . $figures_array['cover'];
            $display  = self::image_file_paths('profile')['content'] . $figures_array['display'];

            unset($connection_sur, $stmt, $get_figures_array, $figures_array, $user_id);
            # Send them to page
            return array(
                'username' => $username,
                'name'     => $name,
                'email'    => $email,
                'state'    => $state,
                'location' => $location,
                'website'  => $website,
                'about'    => $bio,
                'cover'    => $cover,
                'display'  => $display
            );
        }
        public static function profile_navigation($page='profile'): array
        {
            $content = array();
            $highlight = 'profile-tab-active';
            $profile=$history=$draft=$saved='';
            switch ($page) {
                case 'profile':
                    $profile = $highlight;
                    break;
                case 'history':
                    $history = $highlight;
                    break;
                case 'draft':
                    $draft = $highlight;
                    break;
                case 'saved':
                    $saved = $highlight;
                    break;
                default:
                    break;
            }
            $content = [
                'profile' => $profile,
                'history' => $history,
                'draft'   => $draft,
                'saved'   => $saved,
            ];
            return $content;
        }
        public static function profile_check_username($user_name): array
        {
            $connection = new DatabaseAccess();
            $connection = $connection->connect('');

            $stmt = $connection->prepare('SELECT uid FROM user_sapphire WHERE uname = ?');
            $stmt->bind_param('s', $user_name);
            $stmt->execute();
            $result = $stmt->get_result();
            $rows = $result->num_rows;
            
            if($rows == 1 ) {
                $data = $result->fetch_array(MYSQLI_ASSOC);
                return array(
                    'state'   => true,
                    'message' => 'found',
                    'uid'     => $data['uid'],
                );
            }
            return array(
                'state'   => false,
                'message' => 'Not found',
                'uid'     => null,
            );
        }
    #

    # Draft
        public static function count_paragraphs($text)
        {
            $text = trim($text);
            # Check bombs
            $bomb1 = explode("\n", $text);
            $bomb2 = explode("\r\n", $text);
            unset($text);
            return ($bomb1 > $bomb2) ? count($bomb1) : count($bomb2);
        }
        public static function get_drafted_data_for_edit($draft_pid, $user_uid): array
        {
            $connection_sur = new DatabaseAccess();
            $connection_sur = $connection_sur->connect('sur');
            $stmt = $connection_sur->prepare('SELECT title, body FROM big_sur_draft WHERE pid=? AND uid = ?');
            $stmt->bind_param('ss', $draft_pid, $user_uid);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $data = $result->fetch_array(MYSQLI_ASSOC);
                $title = $data['title'];
                $body = $data['body'];
                unset($connection_sur, $stmt, $result, $data, $draft_pid, $user_uid);
                return array('title'=>$title, 'body'=>$body);
            }
            unset($connection_sur, $stmt, $result, $draft_pid, $user_uid);
            return array(
                'title'=>'',
                'body'=>''
            );
        }
    #

    # People
        public static function get_user_figures($user_id): array
        {
            # This function tends to get the user display image and cover image.
            # The key to finding everything is the session-user-id from *uid*

            $connection = new DatabaseAccess();
            $connection = $connection->connect('');
            $stmt = $connection->prepare('SELECT uname,name,location,website,about,cover,display FROM user_sapphire WHERE uid=?');
            $stmt->bind_param('s', $user_id);
            $stmt->execute();

            # Get the array of data from database
            $get_figures_array = $stmt->get_result();
            $figures_array = $get_figures_array->fetch_array(MYSQLI_ASSOC);

            # Instantiate the variables
            $username = $figures_array['uname'];
            $name     = $figures_array['name'];
            $location = $figures_array['location'];
            $website  = $figures_array['website'];
            $bio      = $figures_array['about'];
            $cover    = $figures_array['cover'];
            $display  = $figures_array['display'];
            $display_shrink = str_replace('profile', 'profile/shrink', $display);

            # Send them to page
            unset($stmt, $connection, $get_figures_array, $figures_array, $user_id);
            return array(
                'username'=>$username,
                'name'=>$name,
                'location'=>$location,
                'website'=>$website,
                'bio'=>$bio,
                'cover'=>$cover,
                'display_small'=>$display_shrink,
                'display'=>$display
            );
        }

        public static function get_number_of_notes($uid)
        {
            $connection_sur = new DatabaseAccess();
            $connection_sur = $connection_sur->connect('sur');
            $stmt = $connection_sur->prepare('SELECT COUNT(sid) FROM big_sur WHERE uid = ?');
            $stmt->bind_param('s', $uid);
            $stmt->execute();
            $get_no_of_notes_array = $stmt->get_result();
            $notes_number = $get_no_of_notes_array->fetch_array(MYSQLI_ASSOC)['COUNT(sid)'];

            unset($connection, $stmt, $get_no_of_notes_array);
            return ($notes_number != 0) ? $notes_number : null;
        }
    #

    # Follow / Following
        public static function subscribes($uid, $select = 'followers')
        {
            $select_array = ['followers'=>'publisher', 'following'=>'customer'];
            $attribute = $select_array[$select];
            $connection_sur = new DatabaseAccess();
            $connection_sur = $connection_sur->connect('sur');
            $stmt = $connection_sur->prepare("SELECT COUNT(sid) FROM subscribes WHERE $attribute = ? AND state = 1");
            $stmt->bind_param('s', $uid);
            $stmt->execute();
            $get_subs_no_array = $stmt->get_result();
            $subs_number = $get_subs_no_array->fetch_array(MYSQLI_ASSOC)['COUNT(sid)'];
            if($subs_number != 0) {
                unset($stmt, $connection_sur, $get_subs_no_array, $uid, $attribute);
                return $subs_number;
            }
            unset($stmt, $connection_sur, $get_subs_no_array, $uid, $attribute);
            return null;
        }
        public static function subscribe_but($uid_poster, $uid): array
        {   # Get the subscribe state between the user and people
            $subscribe_state = self::get_subscribe_state($uid_poster, $uid);
            $state_variables = self::subscribe_state_variables($subscribe_state);
            $subs_text       = $state_variables['title'];
            $subs_state      = $state_variables['state'];

            unset($uid_poster, $uid, $subscribe_state, $state_variables);
            return array(
                'text'  => $subs_text,
                'state' => $subs_state
            );
        }
    #

    # Write
        public static function get_profile_data_for_edit($post_pid, $user_uid): array
        {
            $connection_sur = new DatabaseAccess();
            $connection_sur = $connection_sur->connect('sur');
            $stmt = $connection_sur->prepare('SELECT bsl.title, bsl.note FROM big_sur_list bsl INNER JOIN big_sur bs ON bsl.pid = bs.pid WHERE bs.uid=? AND bs.pid=?');
            $stmt->bind_param('ss', $user_uid, $post_pid);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $data = $result->fetch_array(MYSQLI_ASSOC);
                $title = $data['title'];
                $body = $data['note'];
                unset($connection_sur, $stmt, $result, $data, $draft_pid, $user_uid);
                return array(
                    'title'=>$title,
                    'body'=>$body
                );
            }
            unset($connection_sur, $stmt, $result, $draft_pid, $user_uid);
            return array(
                'title'=>'',
                'body'=>''
            );
        }
    #

    # Help
        public static function help_GET_validate($get): array
        {
            $connection_help = new DatabaseAccess();
            $connection_help = $connection_help->connect('help');
            $stmt = $connection_help->prepare('SELECT hid FROM help_articles WHERE hid = ?');
            $stmt->bind_param('s', $get);
            $stmt->execute();
            $result = $stmt->get_result();
            if( $result->num_rows > 0 ) {
                return array(
                    'action'=>0
                );
            }
            return array(
                'action'=>1
            );
        }
    #

    # Change
        public static function goodName($name, $reGex)
        {
            $properName = preg_match("/^[$reGex]*$/", $name);
            return $properName;
        }
        
        public static function validateInput($var)
        {
            $connection = new DatabaseAccess();
            $connection = $connection->connect('sur');
            return $connection->real_escape_string($var);
        }

        public static function nPhoto_square($photoTmp, $file_size, $file_type, $savePhoto, $square_size)
        {
            switch($file_type)
            {
                case 'image/gif': $original_image=imagecreatefromgif($photoTmp); break;
                case 'image/pjpeg':
                case 'image/jpeg': $original_image=imagecreatefromjpeg($photoTmp); break;
                case 'image/png': $original_image=imagecreatefrompng($photoTmp); break;
                default: $original_image = ''; break;
            }

            // get width and height of original image
                list($original_width, $original_height) = getimagesize($photoTmp);

                if($original_width > $original_height){
                    $new_height = $square_size;
                    $new_width = $new_height*($original_width / $original_height);
                }
                if($original_height > $original_width){
                    $new_width = $square_size;
                    $new_height = $new_width*($original_height / $original_width);
                }
                if($original_height == $original_width){
                    $new_width = $square_size;
                    $new_height = $square_size;
                }
                $new_width = round($new_width);
                $new_height = round($new_height);

                $smaller_image = imagecreatetruecolor($new_width, $new_height);
                $square_image  = imagecreatetruecolor($square_size, $square_size);

                imagecopyresampled($smaller_image, $original_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);

                if($new_width > $new_height){
                    $difference = $new_width-$new_height;
                    $half_difference =  round($difference/2);
                    imagecopyresampled($square_image, $smaller_image, 0-$half_difference+1, 0, 0, 0, $square_size+$difference, $square_size, $new_width, $new_height);
                }
                if($new_height > $new_width){
                    $difference = $new_height-$new_width;
                    $half_difference =  round($difference/2);
                    imagecopyresampled($square_image, $smaller_image, 0, 0-$half_difference+1, 0, 0, $square_size, $square_size+$difference, $new_width, $new_height);
                }
                if($new_height == $new_width){
                    imagecopyresampled($square_image, $smaller_image, 0, 0, 0, 0, $square_size, $square_size, $new_width, $new_height);
                }

                // save the smaller image FILE if destination file given
                if(substr_count(strtolower($savePhoto), ".jpg")){
                    imagejpeg($square_image, $savePhoto, 100);
                }
                if(substr_count(strtolower($savePhoto), ".jpeg")){
                    imagejpeg($square_image, $savePhoto, 100);
                }
                if(substr_count(strtolower($savePhoto), ".gif")){
                    imagegif($square_image, $savePhoto);
                }
                if(substr_count(strtolower($savePhoto), ".png")){
                    imagepng($square_image, $savePhoto, 9);
                }

                imagedestroy($original_image);
                imagedestroy($smaller_image);
                imagedestroy($square_image);
        }

        public static function nPhoto_resize($photoTmp, $file_size, $file_type, $savePhoto)
        {
            switch($file_type)
            {
                case 'image/gif': $ext=imagecreatefromgif($photoTmp); break;
                case 'image/pjpeg':
                case 'image/jpeg': $ext=imagecreatefromjpeg($photoTmp); break;
                case 'image/png': $ext=imagecreatefrompng($photoTmp); break;
                default: $ext = ''; break;
            }

            if( $file_size > 1024768 ) {
                list( $width, $height ) = getimagesize( $photoTmp );
                $newWidth  = $width/2;
                $newHeight = $height/2;
                $tmp = imagecreatetruecolor( $newWidth, $newHeight );
                imagecopyresampled( $tmp, $ext, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height );
                imagejpeg( $tmp, $savePhoto, 80 );
                # save image
                    imagedestroy( $ext );
                    imagedestroy( $tmp );
                # murder image
            } else {
                move_uploaded_file( $photoTmp, $savePhoto );
            }
            unset($photoTmp, $file_size, $file_type, $savePhoto);
        }
    #

    # Misc
        public static function image_file_paths($which): array
        {
            $file_path = '';
            switch ($which) {
                case 'note':
                    $file_path = '/images/community/notes/';
                    break;
                case 'profile':
                    $file_path = '/images/community/profiles/';
                    break;
                case 'logo':
                    $file_path = '/images/logo/';
                    break;
                case 'support':
                    $file_path = '/images/support/';
                    break;
                case 'user':
                    $file_path = '/images/users/';
                default:
                    $file_path = '/images/misc/';
                    break;
            }
            return array(
                'message' => '',
                'content' => $file_path,
            );
        }
        public static function filter($str)
        {
            // convert case to lower
            $str = strtolower($str);
            // remove special chars
            $str = preg_replace('/[^a-zA-Z0-9]/i', '', $str);
            // remove white space from both ends
            $str = trim($str);
            return $str;
        }
        public static function randomKey($length)
        {
            $pool = array_merge(range(0,9), range('a','z'), range('A','Z'));
            $key = "";
            for($i = 0; $i<$length; $i++)
            {
                $key .= $pool[mt_rand(0, count($pool) - 1)];
            }
            unset($pool, $length);
            return $key;
        }
        public static function set_cookie_variables($cookie_name, $cookie_value, $cookie_time = '+6 months', $withSecure = true, $httpOnly = true)
        {
            $response = new Response();
            $response->headers->setCookie(
                Cookie::create($cookie_name)
                ->withValue($cookie_value)
                ->withExpires(strtotime($cookie_time))
                ->withSecure($withSecure)
                ->withHttpOnly($httpOnly)
            );
            $response->sendHeaders();
            return true;
            /* 
                $response->headers->setCookie(new Cookie($cookie_name, $cookie_value, strtotime($cookie_time)));
                setcookie('vst', 'haha', strtotime('+1 month'));
            */
        }
    #

    # Layout
        public static function know_mode($state): array
        {
            $for_dark = (trim($state) === 'darkmode') ? 'bcg-e' : '';
            $for_light = (trim($state) === 'lightmode') ? 'bcg-e' : '';
            return array(
                'dark'=>$for_dark, 
                'light'=>$for_light
            );
        }

        public static function light_mode_response($cur_state): array 
        {
            $state_icon = ($cur_state == 1) ? 'fa-solid fa-sun' : 'fa-solid fa-moon'; // 1 is currently Dark, 0 is currently Light
            $state_text = ($cur_state == 1) ? 'Light' : 'Dark';
            return array(
                'icon' => $state_icon, 
                'text' => $state_text
            );
        }
    #
}