<?php

namespace App\Vunction;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

class IndexFunction
{
    public static function user_profile_state($conn, $uid): array
    {
        $user_file = self::retrieve_details($conn, $uid);

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

    public static function get_user_state($conn, $user_id, $visit_state = false): array
    {
        if( $visit_state == true ) {
            return array(
                'state' => 0,
                'logo'  => self::get_path('images').'/logo/notes.png',
            );
        }

        $state = $conn->fetchAssociative('SELECT state FROM user_sapphire WHERE uid = ?', [$user_id] );

        // 0 => dark, 1 => light
        $theme_logo = ($state == true && $state['state']==1) ? self::get_path('images').'/logo/notes.png' : self::get_path('images').'/logo/notes-white.png';

        unset($conn, $user_id, $visit_state);
        return array(
            'state' => $state,
            'logo'  => $theme_logo,
        );
    }

    public static function imgNomenclature($file): array
    {
        if(file_exists(__DIR__.'/../../public'.$file)) {
            list($width, $height, $type, $attr) = getimagesize(__DIR__.'/../../public'.$file);
            return array('width'=>$width, 'height'=>$height);
        }
        return array(
            'width' => '',
            'height' => ''
        );
    }

    public static function retrieve_details($conn, $user_id): array
    {
        if ($user_id == false) {
            return array(
                'name'          => 'John Doe',
                'username'      => 'john_doe',
                'state'         => false,
                'display'       => '',
                'display_small' => '',
            );
        }

        $username = $name = $state = $display = $display_small = null;

        $stmt = $conn->fetchAssociative('SELECT uname, name, state, display FROM user_sapphire WHERE uid=:uId OR uname=:uId OR name=:uId', ['uId'=>$user_id]);
        if($stmt == true) {
            $username       = $stmt['uname'];
            $name           = $stmt['name'];
            $state          = $stmt['state'];
            $display        = self::image_file_paths('profile')['content'] . $stmt['display'];
            $display_small  = self::image_file_paths('profile')['content'] . 'shk_' . $stmt['display'];
        }

        unset($stmt, $conn, $user_id);
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

    public static function get_me($conn, $theUid): array
    {
        $myName = $myUname = null;
        $stmt = $conn->fetchAssociative(
            'SELECT name, uname FROM user_sapphire WHERE uid=?', [$theUid]
        );
        if($stmt == true) {
            $myName = $stmt['name'];
            $myUname = $stmt['uname'];
        }

        unset($stmt, $conn, $theUid);
        return array(
            'name'     => $myName,
            'username' => $myUname
        );
    }

    public static function get_profile_uid($conn, $the_username=''): array 
    {
        $stmt = $conn->fetchOne('SELECT uid FROM user_sapphire WHERE uname=?', [$the_username]);
        if($stmt == true) {
            return [
                'message' => 'found',
                'uid'     => $stmt
            ];
        }

        return [
            'message' => 'not-found',
            'uid'     => null,
        ];
    }

    public static function get_this_note($connection, $thePid): array
    {
        $title = $paragraphs = $cover = $article_or_image = $date = null;
        $stmt = $connection->fetchAssociative(
            'SELECT title, parags, cover, state, date FROM big_sur_list WHERE pid = :pid', ['pid'=>$thePid], []
        );
        if($stmt == true) {
            $title            = $stmt['title'];
            $paragraphs       = $stmt['parags'];
            $cover            = $stmt['cover'];
            $article_or_image = $stmt['state'];
            $date             = $stmt['date'];
        }

        return array(
            'title'      => $title,
            'paragraphs' => $paragraphs,
            'cover'      => $cover,
            'state'      => $article_or_image,
            'date'       => $date
        );
    }

    public static function get_if_views($conn, $note_id, $viewer_id): bool
    {
        $stmt = $conn->fetchOne('SELECT COUNT(DISTINCT(visit_id)) AS total FROM verb_visits WHERE pid=? AND uid=?', [$note_id, $viewer_id]);
        if($stmt == true && $stmt >= 1) {
            unset($stmt, $note_id, $viewer_id);
            return true;
        }

        unset($stmt, $note_id, $viewer_id);
        return false;
    }

    public static function get_note_views($conn, $note_id): string
    {
        $stmt = $conn->fetchOne('SELECT COUNT(id) AS total verb_views WHERE pid=?', [$note_id]);

        if( $stmt == true && $stmt >= 1 ) {
            unset($conn, $note_id, $stmt);
            return ($stmt === 1) ? $stmt.' view' : $stmt.' views';
        }
        unset($conn, $note_id, $stmt);
        return 'no views';
    }

    public static function get_subscribe_state($conn, $following_id, $follower_id): string
    {
        $stmt = $conn->fetchOne('SELECT state FROM big_sur_subscribes WHERE following=? AND follower=?', [$following_id, $follower_id]);
        if( $stmt == true ) {
            unset($conn, $following_id, $follower_id);
            return $stmt;
        }
        unset($stmt, $conn, $following_id, $follower_id);
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
        public static function small_menu_validations($connection, $pid, $viewer_uid): array
        {
            $save_state = $like_state = $unlike_state = null;

            # SAVE
            $save_state = $connection->fetchOne('SELECT COUNT(id) FROM verb_saves WHERE uid = ? AND pid = ? AND state = 1', [$viewer_uid, $pid] );
            $save_state = ($save_state == true && $save_state == 1) ? 1 : 0;

            # LIKES
            $like_state = $connection->fetchOne('SELECT COUNT(id) FROM verb_likes WHERE uid = ? AND pid = ? AND state = 1', [$viewer_uid, $pid] );
            $like_state = ($like_state == true && $like_state == 1) ? 1 : 0;

            # UNLIKES
            $unlike_state = $connection->fetchOne('SELECT COUNT(id) FROM verb_unlikes WHERE uid = ? AND pid = ? AND state = 1', [$viewer_uid, $pid] );
            $unlike_state = ($like_state == true && $unlike_state == 1) ? 1 : 0;

            return array(
                'save'   => $save_state,
                'like'   => $like_state,
                'unlike' => $unlike_state
            );
        }
    #

    # Article
        public static function GET_validate($conn, $get): bool
        { # Is article found
            $stmt = $conn->fetchAssociative('SELECT COUNT(id) AS total FROM big_sur WHERE pid = ?', [$get]);
            
            return ( $stmt==true && $stmt['total']>0 ) ?true :false;
        }
        public static function GET_validate_people($conn, $get): array
        { # Is GET request name valid
            $stmt = $conn->fetchOne('SELECT uid FROM user_sapphire WHERE uname = ?', [$get]);
            if($stmt == true) {
                unset($conn, $stmt, $get);
                return [$stmt, 0];
            }

            unset($conn, $stmt, $get);
            return ['not-found', 1];
        }

        public static function get_my_note($conn, $thePid): array
        {
            $title = $note = $cover = $cover_lg = $extensions = $date = $note_poster_id = null;

            $stmt = $conn->fetchAssociative('SELECT bs.uid, bsl.title,
                bsl.note, bsl.cover, bsl.cover_extension, bsl.date
                FROM big_sur bs INNER JOIN big_sur_list bsl
                ON bs.pid = bsl.pid
                WHERE bs.pid = ?', [$thePid]);

            // Instantiate the variables
            if($stmt == true) {
                $note_poster_id = $stmt['uid'];
                $title      = $stmt['title'];
                $note       = $stmt['note'];
                $cover      = self::image_file_paths('note')['content'] . self::get_size('small') . $stmt['cover'];
                $cover_lg   = self::image_file_paths('note')['content'] . $stmt['cover'];
                $extensions = $stmt['cover_extension'];
                $date       = $stmt['date'];
            }

            unset($conn, $stmt);
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
        public static function get_note_poster($conn, $theUid): array
        {
            if ($theUid == false) {
                return array(
                    'name' => 'John Doe', 
                    'username' => 'john_doe', 
                    'state' => false, 
                    'display' => ''
                );
            }

            $name = $user_name = $display = null;

            $stmt = $conn->fetchAssociative('SELECT name, uname, display FROM user_sapphire WHERE uid = ?', [$theUid]);
            if($stmt == true) {
                $name      = $stmt['name'];
                $user_name = strtolower($stmt['uname']);
                $display   = self::image_file_paths('profile')['content'] . self::get_size('small') . $stmt['display'];
            }

            unset($stmt, $conn, $theUid);
            return array(
                'name'     => $name,
                'username' => $user_name,
                'display'  => $display
            );
        }
        public static function get_poster_uid($conn, $post_id): array
        {
            $poster_uid = null;

            $stmt = $conn->fetchOne('SELECT uid FROM big_sur WHERE pid=?', [$post_id]);
            if($stmt == true) {
                $poster_uid = $stmt;
            }

            return array(
                'uid'     => $poster_uid,
                'message' => 'Get UID with PID',
            );
        }
        public static function save_like_verb($conn, $table, $theUid, $thePosterUid, $thePid, $type, $state = 1): array
        {
            $table = 'verb_'.$table;
            $stmt = $conn->fetchOne("SELECT id FROM $table WHERE uid = ? AND puid = ? AND pid = ? AND state = ?", [$theUid, $thePosterUid, $thePid, $state]);
            # Instantiate the verbs
            $checked = '';
            if($type == 'like') {
                $icon = 'far fa-thumbs-up';
            } elseif($type == 'unlike') {
                $icon = 'far fa-thumbs-down';
            } elseif($type == 'save') {
                $icon = 'far fa-bookmark';
            }
            if( $stmt == true ) {
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
                unset($conn, $stmt, $table, $theUid, $thePosterUid, $thePid, $type, $state);
                return array('icon'=>$icon, 'check'=>$checked);
            }
            unset($conn, $stmt, $table, $theUid, $thePosterUid, $thePid, $type, $state);
            # Return handler: false = NOT NOTED
            return array(
                'icon'  => $icon,
                'check' => $checked
            );
        }
        public static function renoted($conn, $theUid, $thePosterUid, $thePid, $state = 1): array
        {
            $stmt = $conn->fetchOne('SELECT id FROM renotes WHERE uid = ? AND puid = ? AND pid = ? AND state = ?', [$theUid, $thePosterUid, $thePid, $state]);

            $color = $disabled = null;
            # if queryObject is true
            # Meaning: you RENOTED this Note (Does that make sense?)
            if( $stmt == true ) {
                $color = 'note-buttons-clicked';
                $disabled = 'disabled';
            }
            unset($conn, $stmt, $theUid, $thePosterUid, $thePid, $state);
            return array(
                'color' => $color,
                'state' => $disabled
            );
        }
        public static function verb_number($conn, $thePid, $table, $state = 1): array
        {
            $number = null;

            $table = 'verb_'.$table;

            $stmt = $conn->fetchOne("SELECT COUNT(id) as total FROM $table WHERE pid = ? AND state = ?", [$thePid, $state]);
            # if rows is zero.
            if($stmt == false) {
                # Return number of NOTES
                $number = 'Like';
                unset($conn, $stmt, $thePid, $table, $state);
                return array(
                    'number' => $number
                );
            }
            unset($conn, $stmt, $thePid, $table, $state);
            # Return handler: false = NOT NOTED
            return array(
                'number' => $number . ' likes',
            );
        }
        public static function note_views($conn, $id)
        {
            $stmt = $conn->fetchOne('SELECT COUNT(DISTINCT(uid)) AS total FROM verb_visits WHERE pid = ?', [$id]);
            $ans = null;
            if($stmt == true) {
                $ans = $stmt;
            }

            unset($conn, $id);
            return $ans;
        }
        public static function get_comment($conn, $comment_id): array
        {
            $comment = $date = null;
            $stmt = $conn->fetchAssociative('SELECT comment, date FROM verb_comments_list WHERE cid = ?', [$comment_id]);
            if($stmt == true) {
                $comment = $stmt['comment'];
                $date = $stmt['date'];
            }

            unset($conn, $stmt, $comment_id);
            return array(
                'comment' => $comment,
                'date'    => $date
            );
        }
        public static function get_comment_poster($conn, $commenter_id): array
        {
            $name = $uname = null;
            $stmt = $conn->fetchAssociative('SELECT name, uname FROM user_sapphire WHERE uid = ?', [$commenter_id]);
            if($stmt == true) {
                $name = $stmt['name'];
                $uname = $stmt['uname'];
            }

            unset($conn, $stmt, $commenter_id);
            return array(
                'name'     => $name,
                'username' => $uname
            );
        }
        public static function get_comments_number($conn, $note_id, $reason = 'number', $comment_no = 200): array
        {
            $stmt = $conn->fetchOne('SELECT COUNT(id) as total FROM verb_comments WHERE pid = ?', [$note_id]);

            if($stmt == true) {
                if( $stmt >= $comment_no && $reason === 'more' ) {

                    $show_more = '<a href="#" class="notes-more-comments calib a"><p>more <span class="trn3"><i class="sm-i fa fa-arrow-right"></i></span></p></a>';
                    unset($conn, $stmt);
                    return [$show_more];
                } elseif( $stmt <= $comment_no && $reason === 'more' ) {

                    unset($conn, $stmt);
                    return [''];
                } elseif( $stmt == 0 ) {

                    unset($conn, $stmt);
                    return ['Comment'];
                } else {

                    unset($conn, $stmt);
                    return [$rows_number];
                }
            }
            return ['something wrong'];
        }
        public static function last_read_note($conn, $uid): array
        {
            $stmt = $conn->fetchOne('SELECT pid FROM views WHERE uid = ? ORDER BY sid DESC LIMIT 1', [$uid]);
            if($stmt == true) {
                return [
                    $stmt
                ];
            }
            unset($conn, $stmt, $uid);
            return array(null);
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
        public static function article_validate_post_id($conn, $post_id)
        {
            return ( self::GET_validate($conn, $post_id) === true ) ? true : false;
        }
    #

    # Profile
        public static function profile_user_figures($conn, $user_id): array
        {
            # This function tends to get the user display image and cover image.
            # The key to finding everything is the session-user-id from *uid*

            $username = $name = $email = $state = $location = $website = $bio = $cover = $display = null;

            $stmt = $conn->fetchAssociative('SELECT uname, name, email, state, location, website, about, cover, display FROM user_sapphire WHERE uid=?', [$user_id]);
            if($stmt == true) {
                $username = $stmt['uname'];
                $name     = $stmt['name'];
                $email    = $stmt['email'];
                $state    = $stmt['state'];
                $location = $stmt['location'];
                $website  = $stmt['website'];
                $bio      = $stmt['about'];
                $cover    = self::image_file_paths('profile')['content'] . $stmt['cover'];
                $display  = self::image_file_paths('profile')['content'] . $stmt['display'];
            }

            unset($conn, $stmt, $user_id);
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
        public static function profile_check_username($conn, $user_name): array
        {
            $stmt = $conn->fetchOne('SELECT uid FROM user_sapphire WHERE uname = ?', [$user_name]);
            
            if($stmt == true) {
                $data = $result->fetch_array(MYSQLI_ASSOC);
                return array(
                    'state'   => true,
                    'message' => 'found',
                    'uid'     => $stmt,
                );
            }
            return array(
                'state'   => false,
                'message' => 'not-found',
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
        public static function get_drafted_data_for_edit($conn, $draft_pid, $user_uid): array
        {
            $stmt = $conn->fetchAssociative('SELECT title, body FROM big_sur_draft WHERE pid=? AND uid = ?', [$draft_pid, $user_uid]);

            if ($stmt == true) {
                $title = $stmt['title'];
                $body = $stmt['body'];

                unset($conn, $stmt, $draft_pid, $user_uid);
                return array(
                    'title' => $title,
                    'body'  => $body
                );
            }
            unset($conn, $stmt, $draft_pid, $user_uid);
            return array(
                'title' => null,
                'body'  => null
            );
        }
    #

    # People
        public static function get_user_figures($conn, $user_id): array
        {
            # This function tends to get the user display image and cover image.
            # The key to finding everything is the session-user-id from *uid*

            $username = $name = $location = $website = $bio = $cover = $display = $display_shrink = null;

            $stmt = $conn->fetchAssociative('SELECT uname, name, location, website, about, cover, display FROM user_sapphire WHERE uid=?', [$user_id]); 
            if($stmt == true) {
                $username = $stmt['uname'];
                $name     = $stmt['name'];
                $location = $stmt['location'];
                $website  = $stmt['website'];
                $bio      = $stmt['about'];
                $cover    = $stmt['cover'];
                $display  = $stmt['display'];
                $display_shrink = str_replace('profile', 'profile/shrink', $display);
            }

            # Send them to page
            unset($stmt, $conn, $user_id);
            return array(
                'username' => $username,
                'name'     => $name,
                'location' => $location,
                'website'  => $website,
                'bio'      => $bio,
                'cover'    => $cover,
                'display_small' => $display_shrink,
                'display'  => $display
            );
        }

        public static function get_number_of_notes($conn, $uid)
        {
            $notes_number = null;

            $stmt = $conn->fetchOne('SELECT COUNT(id) AS total FROM big_sur WHERE uid = ?', [$uid]);
            if($stmt == true && $stmt != 0) {
                $notes_number = $stmt;
            }

            unset($conn, $stmt, $uid);
            return $notes_number;
        }
    #

    # Follow / Following
        public static function subscribes($conn, $uid, $select = 'follower')
        {
            $subs_number = null;

            $stmt = $conn->fetchOne("SELECT COUNT(id) AS total FROM big_sur_subscribes WHERE $select = ? AND state = 1", [$uid]);
            if($stmt == true && $stmt != 0) {
                $subs_number = $stmt;
            }

            unset($stmt, $conn, $uid, $select);
            return $subs_number;
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
        public static function get_profile_data_for_edit($conn, $post_id, $user_id): array
        {
            $title = $body = null;

            $stmt = $conn->fetchAssociative('SELECT bsl.title, bsl.note FROM big_sur_list bsl INNER JOIN big_sur bs ON bsl.pid = bs.pid WHERE bs.uid=? AND bs.pid=?', [$user_id, $post_id]);
            if($stmt == true) {
                $title = $stmt['title'];
                $body  = $stmt['note'];
            }

            unset($conn, $stmt, $post_id, $user_id);
            return array(
                'title' => $title,
                'body'  => $body
            );
        }
    #

    # Help
        public static function help_GET_validate($conn, $get): array
        {
            $action = 1;

            $stmt = $conn->fetchOne('SELECT hid FROM help_articles WHERE hid = ?', [$get]);
            if($stmt == true) {
                $action = 0;
            }

            return array(
                'action' => $action
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
            // $connection = new DatabaseAccess();
            // $connection = $connection->connect('sur');
            // return $connection->real_escape_string($var);
            return $var;
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
                'dark'  => $for_dark, 
                'light' => $for_light
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
