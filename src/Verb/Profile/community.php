<?php

# Get the Note last read.
$pid = last_read_note($uid)[0];

# Details for the Note, itself: FUNCTION = GET_MY_NOTE()
    $get_note_result_array = get_my_note($pid);
    $uid_poster = $get_note_result_array[0]; # uid
    $pid_note = $get_note_result_array[1]; # pid
    $note_title = stripslashes($get_note_result_array[2]); # title
    $note_note = $get_note_result_array[3]; # note
    $note_cover = note_cover_article($uid_poster, $get_note_result_array[4], '../');
    $note_date = $get_note_result_array[5]; # date posted
    $path = '../';
#

# Details of the Noter, themselves: FUNCTION = GET_NOTE_POSTER()
    $get_note_poster = get_note_poster($uid_poster);

    $note_poster_name = $get_note_poster[0];
    $note_poster_username = $get_note_poster[1];
    $note_poster_display = $get_note_poster[2];
#

# Get the subscribe state between the user and people
    $subscribe_state = get_subscribe_state($uid_poster, $uid);
    $state_variables = subscribe_state_variables($subscribe_state);
    $sub_state_handler = $state_variables[0];
    $sub_state_text = $state_variables[1];
    $sub_state_color = $state_variables[2];
#


?>