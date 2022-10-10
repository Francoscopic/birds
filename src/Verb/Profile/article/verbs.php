<?php

# Introduce connection
require_once __DIR__.'../../../../../out/aquamarine/processors/dbconn.php';

# Articles
save_verbs();
save_human_emotions();

function save_verbs() {

    if( isset( $_POST['thePid'], $_POST['thePUid'], $_POST['theUid'], $_POST['theReason'] ) ) {

        $pid = $_POST['thePid'];
        $puid = $_POST['thePUid'];
        $uid = $_POST['theUid'];
        $reason = $_POST['theReason'];

        verb_droppers(); // This function needs to be instantiated before it's nested functions(note, like, renote) can be called

        // Small validation and direction trailing
        if( trim($reason) == 'note' || trim($reason) == 'save' ) {
            # Send request
            note($pid, $puid, $uid);
        }
        if( trim($reason) == 'like' ) {
            # Send request
            like($pid, $puid, $uid);
        }
        if( trim($reason == 'unlike') ) {
            # Send request
            unlike($pid, $puid, $uid);
        }
        if( trim($reason == 'share') ) {
            # Send request
            echo 'Coming soon';
            // renote($pid, $puid, $uid);
        }
        if( trim($reason) == 'report' ) {
            // Send request
            // Here PUID is => report data.
            report($pid, $puid, $uid);
        }
    }
}

function verb_droppers() {

    function note($thePid, $thePUid, $theUid, $state = 1) {
        
        // Get me the connection variable
        global $connection_verb;

        $thisID = rand(99, 9999).round(microtime(true)).randomKey( rand(2, 12) );

        $stmt = $connection_verb->prepare('SELECT state FROM saves WHERE uid = ? AND puid = ? AND pid = ?');
        $stmt->bind_param('sss', $theUid, $thePUid, $thePid);
        $stmt->execute();
        $getResult = $stmt->get_result();

        // if getResult is not greater than zero.
        // Meaning you haven't saved this before/ INSERT into database
        if( !( $getResult->num_rows > 0 ) ) {
            // Nothing found. Save new
            $stmt = $connection_verb->prepare('INSERT INTO saves (pid, puid, uid, bid, state) VALUES(?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $thePid, $thePUid, $theUid, $thisID, $state);
            $stmt->execute();
        } else {
            # Found something. UPDATE
            $current_state = $getResult->fetch_array(MYSQLI_ASSOC)['state'];
            if($current_state == 0) {
                // If state is 0 (unsaved), make it 1 (save)
                $stmt = $connection_verb->prepare('UPDATE saves SET state = 1 WHERE pid = ? AND puid = ? AND uid = ?');
                $stmt->bind_param('sss', $thePid, $thePUid, $theUid);
                $stmt->execute();
            } else {
                // If state is 1 (saved), make it 0 (unsave)
                $stmt = $connection_verb->prepare('UPDATE saves SET state = 0 WHERE pid = ? AND puid = ? AND uid = ?');
                $stmt->bind_param('sss', $thePid, $thePUid, $theUid);
                $stmt->execute();
            }
        }
        unset($connection_verb, $stmt, $thePid, $thePUid, $theUid, $state, $thisID, $getResult);
    }

    function like($thePid, $thePUid, $theUid, $state = 1) {
        
        // Get me the connection variable
        global $connection_verb;

        $thisID = rand(99, 9999).round(microtime(true)).randomKey( rand(2, 12) );

        $stmt = $connection_verb->prepare('SELECT state FROM likes WHERE uid = ? AND puid = ? AND pid = ?');
        $stmt->bind_param('sss', $theUid, $thePUid, $thePid);
        $stmt->execute();
        $getResult = $stmt->get_result();

        // if getResult is not greater than zero.
        // Meaning you haven't saved this before/ INSERT into database
        if( !( $getResult->num_rows > 0 ) ) {
            # Nothing found. INSERT
            $stmt = $connection_verb->prepare('INSERT INTO likes (pid, puid, uid, lid, state) VALUES(?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $thePid, $thePUid, $theUid, $thisID, $state);
            $stmt->execute();
        } else {
            # Found something. UPDATE
            $current_state = $getResult->fetch_array(MYSQLI_ASSOC)['state'];
            if($current_state == 0) {
                // If state is 0 (unliked), make it 1 (like)
                $stmt = $connection_verb->prepare('UPDATE likes SET state = 1 WHERE pid = ? AND puid = ? AND uid = ?');
                $stmt->bind_param('sss', $thePid, $thePUid, $theUid);
                $stmt->execute();
                echo 0;
            } else {
                // If state is 1 (liked), make it 0 (unlike)
                $stmt = $connection_verb->prepare('UPDATE likes SET state = 0 WHERE pid = ? AND puid = ? AND uid = ?');
                $stmt->bind_param('sss', $thePid, $thePUid, $theUid);
                $stmt->execute();
                echo 1;
            }
        }
        unset($connection_verb, $stmt, $thePid, $thePUid, $theUid, $state, $thisID, $getResult);
    }

    function unlike($thePid, $thePUid, $theUid, $state = 0) {
        
        // Get me the connection variable
        global $connection_verb;

        $thisID = rand(99, 9999).round(microtime(true)).randomKey( rand(2, 12) );

        $stmt = $connection_verb->prepare('SELECT state FROM unlikes WHERE uid = ? AND puid = ? AND pid = ?');
        $stmt->bind_param('sss', $theUid, $thePUid, $thePid);
        $stmt->execute();
        $getResult = $stmt->get_result();

        // if getResult is not greater than zero.
        // Meaning you haven't saved this before/ INSERT into database
        if( !( $getResult->num_rows > 0 ) ) {
            # Nothing found. INSERT
            $stmt = $connection_verb->prepare('INSERT INTO unlikes (pid, puid, uid, lid, state) VALUES(?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $thePid, $thePUid, $theUid, $thisID, $state);
            $stmt->execute();
        } else {
            # Found something. UPDATE
            $current_state = $getResult->fetch_array(MYSQLI_ASSOC)['state'];
            if($current_state == 0) {
                // If state is 0 (unliked), make it 1 (like)
                $stmt = $connection_verb->prepare('UPDATE unlikes SET state = 1 WHERE pid = ? AND puid = ? AND uid = ?');
                $stmt->bind_param('sss', $thePid, $thePUid, $theUid);
                $stmt->execute();
                echo 0;
            } else {
                // If state is 1 (liked), make it 0 (unlike)
                $stmt = $connection_verb->prepare('UPDATE unlikes SET state = 0 WHERE pid = ? AND puid = ? AND uid = ?');
                $stmt->bind_param('sss', $thePid, $thePUid, $theUid);
                $stmt->execute();
                echo 1;
            }
        }
        unset($connection_verb, $stmt, $thePid, $thePUid, $theUid, $state, $thisID, $getResult);
    }

    function renote($thePid, $thePUid, $theUid, $state = 1) {
        
        // Get me the connection variable
        global $connection_verb;

        $thisID = rand(99, 9999).round(microtime(true)).randomKey( rand(2, 12) );

        $stmt = $connection_verb->prepare('SELECT sid FROM renotes WHERE uid = ? AND puid = ? AND pid = ? AND state = ?');
        $stmt->bind_param('ssss', $theUid, $thePUid, $thePid, $state);
        $stmt->execute();
        $getResult = $stmt->get_result();

        // if getResult is not greater than zero.
        // Meaning you haven't note this before/ INSERT into database
        if( !( $getResult->num_rows > 0 ) ) {
            // Nothing found. Save new
            $stmt = $connection_verb->prepare('INSERT INTO renotes (pid, puid, uid, rid, state) VALUES(?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $thePid, $thePUid, $theUid, $thisID, $state);
            $stmt->execute();
            // echo Saved to database;
        }
            // ELSE: The result is greater than zero
            // Meaning you have note this before.

        unset($connection_verb, $stmt, $thePid, $thePUid, $theUid, $state, $thisID, $getResult);
    }

    function report($thePid, $theReportData_asPUid, $theUid) {

        // Get me the connection variable
        global $connection_verb;
        $thisID = rand(99, 9999).round(microtime(true)).randomKey( rand(2, 12) );

        $stmt = $connection_verb->prepare('INSERT INTO report (pid, uid, rid, sitch) VALUES(?, ?, ?, ?)');
        $stmt->bind_param('ssss', $thePid, $theUid, $thisID, $theReportData_asPUid);
        $stmt->execute();
        unset($connection_verb, $stmt, $thisID, $thePid, $theReportData_asPUid, $theUid);
    }
}

function save_human_emotions() {

    if( isset( $_POST['the_pid'], $_POST['the_puid'], $_POST['the_uid'], $_POST['the_reason'] ) ) {

        $pid = $_POST['the_pid'];
        $puid = $_POST['the_puid'];
        $uid = $_POST['the_uid'];
        $reason = $_POST['the_reason'];

        emotion_droppers(); // This function needs to be instantiated before it's nested functions(note, like, renote) can be called

        // Small validation and direction trailing
        if( trim($reason) === 'disinterest' ) {
            // Call the respective function
            disinterest_report($pid, $puid, $uid, 'disinterests', 'did');
        }
        if( trim($reason) === 'mute' ) {
            // Call the respective function
            mute_publisher($puid, $uid);
        }
        if( trim($reason) === 'inappropriate' ) {
            // Call the respective function
            disinterest_report($pid, $puid, $uid, 'inappropriates', 'inappro_id');
        }

    }
}

function emotion_droppers() {

    function disinterest_report($thePid, $thePUid, $theUid, $table, $special_column) {
        // Get me the connection variable
        global $connection_human_emotions;

        $thisID = rand(2000, 9000).round(microtime(true)).randomKey( rand(5, 10) );

        // Save uninterest ID
        $stmt = $connection_human_emotions->prepare("INSERT INTO $table (pid, puid, uid, $special_column) VALUES(?, ?, ?, ?)");
        $stmt->bind_param('ssss', $thePid, $thePUid, $theUid, $thisID);
        $stmt->execute();

        // Saved to database;

        unset($thePid, $thePUid, $theUid, $thisID, $table, $special_column);
        $stmt->reset();
        $connection_human_emotions->close();
    }

    function mute_publisher($publisher_id, $reader_id) {
        // Get me the connection variable
        global $connection_sur;

        $state = 1;

        // Save the mute relationship
        $stmt = $connection_sur->prepare("INSERT INTO mute (publisher, customer, state) VALUES(?, ?, ?)");
        $stmt->bind_param('sss', $publisher_id, $reader_id, $state);
        $stmt->execute();
        // Saved to database;

        unset($publisher_id, $reader_id, $state);
        $stmt->reset();
        $connection_sur->close();
    }
}


# Comments
save_comments();
function save_comments() {

    function submit_comment($pid, $puid, $uid, $comment) {

        global $connection_verb;

        $thisID = rand(600, 999).randomKey( rand(10, 20) );

        # INSERT into comments
        $stmt = $connection_verb->prepare('INSERT INTO comments (pid, puid, uid, cid) VALUES(?, ?, ?, ?)');
        $stmt->bind_param('ssss', $pid, $puid, $uid, $thisID);
        # INSERT into comments_list
        $stmt_ = $connection_verb->prepare('INSERT INTO comments_list (cid, comment) VALUES(?, ?)');
        $stmt_->bind_param('ss', $thisID, $comment);

        # Execute INSERT
        $stmt->execute();
        $stmt_->execute();

        echo '10';    # success

        unset($pid, $puid, $uid, $thisID, $comment);
        $stmt->reset();
        $connection_verb->close();
    }

    if( isset( $_POST['com_pid'], $_POST['com_puid'], $_POST['com_uid'], $_POST['com'] ) ) {

        $pid = $_POST['com_pid'];
        $puid = $_POST['com_puid'];
        $uid = $_POST['com_uid'];
        $comment = test_input($_POST['com']);

        submit_comment($pid, $puid, $uid, $comment);
    }
}

# Record views
views();
function views() {

    function save_views($note_id, $viewer_id) {

        global $connection_verb;
        $stmt = $connection_verb->prepare('INSERT INTO views (access, pid, uid) VALUES(1, ?, ?)');
        $stmt->bind_param('ss', $note_id, $viewer_id);
        $stmt->execute();
    }

    if( isset( $_POST['views'], $_POST['note_id'], $_POST['viewer_id'] ) ) {

        $note_id = $_POST['note_id'];
        $viewer_id = $_POST['viewer_id'];
        save_views($note_id, $viewer_id);
    }
}

# Record shares and visits
shares();
function shares() {

    function save_outshares($share_id, $post_id, $user_id, $media) {

        global $connection_verb;
        $stmt = $connection_verb->prepare('INSERT INTO shares (share_id, post_id, user_id, media) VALUES(?, ?, ?, ?)');
        $stmt->bind_param('ssss', $share_id, $post_id, $user_id, $media);
        $stmt->execute();
    }
    function save_inshares($share_id, $post_id, $user_id, $media) {

        global $connection_verb;
        $stmt = $connection_verb->prepare('INSERT INTO visits (visit_id, post_id, user_id, media) VALUES(?, ?, ?, ?)');
        $stmt->bind_param('ssss', $share_id, $post_id, $user_id, $media);
        $stmt->execute();
    }

    if ( isset($_POST['outShare']) ) {
        
        $post_id = $_POST['outshare_pid'];
        $user_visitor_id = $_POST['outshare_uid'];
        $media = $_POST['outshare_media'];
        $share_id = randomKey(9);

        save_outshares($share_id, $post_id, $user_visitor_id, $media);
    }

    if ( isset($_POST['inShare']) ) {
        
        $post_id = $_POST['inshare_pid'];
        $user_visitor_id = $_POST['inshare_uid'];
        $media = $_POST['inshare_media'];
        $visit_id = randomKey(9);

        save_inshares($visit_id, $post_id, $user_visitor_id, $media);
    }
}



# USEFUL Functions
    function randomKey($length) {
        
        $pool = array_merge(range(0,9),range('a','z'),range('A','Z'));
        $key = "";
        for($i = 0; $i<$length; $i++)
        {
            $key .= $pool[mt_rand(0,count($pool) - 1)];
        }
        return $key;
    }

    function test_input($data) {

        $transformed = trim($data);
        $transformed = filter_var($data, FILTER_UNSAFE_RAW);
        return $transformed;
    }
#

?>