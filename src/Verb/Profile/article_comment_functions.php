
<?php

function get_my_note($thePid) {

    // Get the needed information of me.
    global $connection_sur;

    $stmt_o = $connection_sur->prepare('SELECT uid FROM big_sur WHERE pid = ?');
    $stmt_o->bind_param('s', $thePid);
    $stmt_o->execute();
    $get_uid_result = $stmt_o->get_result();
    $get_uid_row = $get_uid_result->fetch_array(MYSQLI_ASSOC);

    $note_poster_id = $get_uid_row['uid'];

    $stmt = $connection_sur->prepare('SELECT pid, title, note, cover, date FROM big_sur_list WHERE pid = ?');
    $stmt->bind_param('s', $thePid);
    $stmt->execute();
    $get_result = $stmt->get_result();

    // Get the array of data from database
    $result_array = $get_result->fetch_array(MYSQLI_ASSOC);

    // Instantiate the variables
    $post_id = $result_array['pid'];
    $title = $result_array['title'];
    $note = $result_array['note'];
    $cover = $result_array['cover'];
    $date = $result_array['date'];

    // Send them to page
    return array('poster_id'=>$note_poster_id, 'post_id'=>$post_id, 'title'=>$title, 'note'=>$note, 'cover'=>$cover, 'date'=>$date);
}

function get_note_poster($theUid) {

    // Call database
    global $connection;

    // Search database for the poster's details
    $stmt = $connection->prepare('SELECT name, uname, display FROM user_sapphire WHERE uid = ?');
    $stmt->bind_param('s', $theUid);
    $stmt->execute();

    // Get data
    $get_result = $stmt->get_result();
    $result_array = $get_result->fetch_array(MYSQLI_ASSOC);

    // Instantiate variables
    $name = $result_array['name'];
    $user_name = strtolower($result_array['uname']);
    $display = $result_array['display'];

    // Kill variables
    unset($stmt, $connection);

    // Return variables
    return array('name'=>$name, 'username'=>$user_name, 'display'=>$display);
}

function get_note_verbs() {
    
    function save_like_verb($table, $theUid, $thePosterUid, $thePid, $type, $state = 1) {

        global $connection_verb;
        $stmt = $connection_verb->prepare("SELECT sid FROM $table WHERE uid = ? AND puid = ? AND pid = ? AND state = ?");
        $stmt->bind_param('ssss', $theUid, $thePosterUid, $thePid, $state);
        $stmt->execute();
        $getResult = $stmt->get_result();
        # Instantiate the verbs
        $color = $disabled = $checked = '';
        if($type == 'like') {
            $icon = 'far fa-thumbs-up';
            $title = 'Like';
        } elseif($type == 'unlike') {
            $icon = 'far fa-thumbs-down';
            $title = ' ';
            // $title = '&Uopf;';
        } elseif($type == 'save') {
            $icon = 'far fa-bookmark';
            $title = 'Save';
        }
        if( $getResult->num_rows > 0 ) {
            # Return handler: true = NOTED
            $color = 'note-buttons-clicked';
            $disabled = 'disabled';
            $checked = 'checked';
            if($type == 'like') {
                $icon = 'fas fa-thumbs-up';
                $title = 'Liked';
            }
            if($type == 'unlike') {
                $icon = 'fas fa-thumbs-down';
                $title = '&Uopf;';
            }
            if($type == 'save') {
                $icon = 'fas fa-bookmark';
                $title = 'Saved';
            }
            return array('color'=>$color, 'icon'=>$icon, 'state'=>$disabled, 'check'=>$checked, 'title'=>$title);
        }
        # Return handler: false = NOT NOTED
        return array('color'=>$color, 'icon'=>$icon, 'state'=>$disabled, 'check'=>$checked, 'title'=>$title);
    }

    function renoted($theUid, $thePosterUid, $thePid, $state = 1) {

        global $connection_verb;
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
            return array($color, $disabled);
        }
        # Return handler: false = NOT RENOTED
        return array($color, $disabled);
    }

    function verb_number($thePid, $table, $state = 1) {

        global $connection_verb;
        $stmt = $connection_verb->prepare("SELECT pid, puid FROM $table WHERE pid = ? AND state = ?");
        $stmt->bind_param('ss', $thePid, $state);
        $stmt->execute();
        $getResult = $stmt->get_result();
        # Instantiate the verbs
        $number = $getResult->num_rows;
        # if rows is zero.
        if( $getResult->num_rows === 0 ) {
            # Return number of NOTES
            $number = '';
            return array('number'=>$number);
        }
        # Return handler: false = NOT NOTED
        return array('number'=>$number);
    }

    function note_views($id) {
        global $connection_verb;
        $stmt = $connection_verb->prepare('SELECT DISTINCT(sid) FROM views WHERE pid = ?');
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $ans = ($stmt->get_result())->num_rows;
        return $ans === 0 ? 'None' : $ans;
    }

}

function get_comment($comment_id) {

    global $connection_verb;

    $stmt = $connection_verb->prepare("SELECT comment, date FROM comments_list WHERE cid = ?");
    $stmt->bind_param("s", $comment_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $rows = $result->fetch_array(MYSQLI_ASSOC);

    $comment = $rows['comment'];
    $date = $rows['date'];

    // Kill the variables
    unset($comment_id);
    unset($result, $rows);

    return array($comment, $date);
}

function get_comment_poster($commenter_id) {

    global $connection;

    $stmt = $connection->prepare("SELECT name, uname FROM user_sapphire WHERE uid = ?");
    $stmt->bind_param("s", $commenter_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $rows = $result->fetch_array(MYSQLI_ASSOC);

    $name = $rows['name'];
    $uname = $rows['uname'];

    return array($name, $uname);

    // Kill the variables
    unset($commenter_id, $name, $uname);
    unset($result, $rows);

    // Close connection
    $stmt->reset();
    $connection->close();
}

function get_comments_number($note_id, $reason = 'number', $comment_no = 200) {

    global $connection_verb;
    $stmt = $connection_verb->prepare("SELECT sid FROM comments WHERE pid = ?");
    $stmt->bind_param("s", $note_id);
    $stmt->execute();
    $rows_number = ($stmt->get_result())->num_rows;

    // return array($rows_number);
    if( $rows_number >= $comment_no && $reason === 'more' ) {

        $show_more = '<a href="#" class="notes-more-comments ft-sect-lite a"><p>more <span class="trn3"><i class="sm-i fa fa-arrow-right"></i></span></p></a>';
        return array($show_more);
    } elseif( $rows_number <= $comment_no && $reason === 'more' ) {
        return array('');
    } elseif( $rows_number === 0 ) {
        return array('No');
    } else {
        return array($rows_number);
    }
}

function last_read_note($uid) {
    global $connection_verb;
    $stmt = $connection_verb->prepare('SELECT pid FROM views WHERE uid = ? ORDER BY sid DESC LIMIT 1');
    $stmt->bind_param('s', $uid);
    $stmt->execute();
    $result=$stmt->get_result();
    $row = $result->fetch_array(MYSQLI_ASSOC);
    return array($row['pid']);
}

?>