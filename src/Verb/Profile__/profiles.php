<?php

$get_user_result_array = ($visitor_state == true) ? retrieve_details(false) : retrieve_details($uid);

$username = strtolower($get_user_result_array['username']);
$name = stripslashes($get_user_result_array['name']);
$state = ($get_user_result_array['state'] === 1) ? 'darkmode' : 'lightmode';
$display = $get_user_result_array['display'];

?>