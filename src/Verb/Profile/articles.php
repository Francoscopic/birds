
<?php

$get = $_GET['wp'];
$pid;

# valiate if PID is correct and show Note
# otherwise, go to home page
$pid = GET_validate($get)[0];
$pid_action = GET_validate($get)[1];
if($pid_action == 1) {
    echo header('Location: ../../'); // out they go
}


# Details for the Note, itself: FUNCTION = GET_MY_NOTE()
    $get_note_result_array = get_my_note($pid);
    $uid_poster = $get_note_result_array['poster_id']; # uid
    $pid_note = $get_note_result_array['post_id']; # pid
    $note_title = stripslashes($get_note_result_array['title']); # title
    $note_note = $get_note_result_array['note']; # note
    $note_cover = note_cover_article($get_note_result_array['cover'], '../../');
    $note_extensions = note_cover_extensions($get_note_result_array['cover'], $get_note_result_array['extensions'])['images'];
    $note_date = $get_note_result_array['date']; # date posted of article

    $note_font = note_font_family($get_note_result_array['font']);
    $note_theme = $get_note_result_array['theme'];

    $cover_width = imgNomenclature($note_cover)['width'];
    $cover_height = imgNomenclature($note_cover)['height'];
#

# Details of the Noter, themselves: FUNCTION = GET_NOTE_POSTER()
    $get_note_poster = get_note_poster($uid_poster);

    $note_poster_name = $get_note_poster['name'];
    $note_poster_username = $get_note_poster['username'];
    $note_poster_display = $get_note_poster['display'];
#

# Details of Viewer
    $viewer_array = ($visitor_state == true) ? get_note_poster(false) : get_note_poster($uid);
    $name = $viewer_array['name'];
    $username = $viewer_array['username'];
    $display = note_cover($viewer_array['display'], 'profile', 'pages', 'small');
#

# Variables that make VISTORS balanced
    $comment_form = <<<COMMENT
    <form method="post" name="comment">
        <div class="cmt-container clear-fix">
            <div class="cmt-user-img bck lozad" data-background-image="$display">
            </div>
            <div id="cmt-area" class="cmt-area">
                <input type="hidden" id="comment-assistant" pid="$pid_note" puid="$uid_poster" uid="$uid" name="$name" uname="$username" />
                <textarea onkeyup="comment_grow(this)" name="comment" id="cmt-area-textarea" class="cmt-area-textarea noresize ft-sect" placeholder="Share your thoughts.."></textarea><!--
            --><button id="cmt-area-post" class="atc-area-but opas5" disabled>Post</button>
            </div>
        </div>
    </form>
    COMMENT;
    $comment_show = ($visitor_state == true) ? '' : $comment_form;
#

# NEW views from web
    function save_views($note_id, $viewer_id) {
        global $connection_verb;
        $stmt = $connection_verb->prepare('INSERT INTO views (access, pid, uid) VALUES(1, ?, ?)');
        $stmt->bind_param('ss', $note_id, $viewer_id);
        $stmt->execute();
    }
    save_views($pid, $uid);
#



?>