<?php

require_once('../out/aquamarine/processors/dbconn.php');


$get_user_figures_array = profile_user_figures($connection, $uid);

$username = strtolower($get_user_figures_array['username']);
$name = $get_user_figures_array['name'];
$email = $get_user_figures_array['email'];
$state = ($get_user_figures_array['state'] === 1) ? 'darkmode' : 'lightmode';
$location = $get_user_figures_array['location'];
$website = $get_user_figures_array['website'];
$bio = nl2br($get_user_figures_array['about']);
$bio_forChange = trim($bio);
$cover = $get_user_figures_array['cover'];
$display = $get_user_figures_array['display'];

$subs_number = subscribes($uid, 'followers');   // the people who follow me
$my_subs = subscribes($uid, 'following');    // the people I follow

# WRITE
    $draft_title = $draft_body = '';
    if ( isset($_GET['drf']) ) {
        $draft_edit_pid = $_GET['did'];
        $draft_edit_data = get_drafted_data_for_edit($draft_edit_pid, $uid);
        $draft_title = stripslashes($draft_edit_data['title']);
        $draft_body = stripslashes($draft_edit_data['body']);
    }
    $profile_title = $profile_body = '';
    if ( isset($_GET['prf']) ) {
        $profile_edit_pid = $_GET['did'];
        $profile_edit_data = get_profile_data_for_edit($profile_edit_pid, $uid);
        $profile_title = stripslashes($profile_edit_data['title']);
        $profile_body = stripslashes(str_replace("\\r","\r",str_replace("\\n","\n", $profile_edit_data['body'])));
    }
#

?>