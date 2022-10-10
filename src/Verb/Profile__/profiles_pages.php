
<?php

$uid = $_SESSION['uid'];

$get_user_result_array = retrieve_details($uid);

$username = strtolower($get_user_result_array[0]);
$name = stripslashes($get_user_result_array[1]);
$state = $get_user_result_array[2] === 1 ? 'darkmode' : 'lightmode';
$display = $get_user_result_array[3];

$display_shrink = str_replace('profile', 'profile/shrink', $display);


?>