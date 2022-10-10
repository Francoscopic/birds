
<?php
# DISCLAIMER: User = the log-in account
#   People = the account being viewed

$people_name = $_GET['up'];

# valiate if user-name is correct and show account
# otherwise, go to home page
    $val_res = GET_validate_people($people_name);
    $people_uid = $val_res[0];
    $uid_action = $val_res[1];
    if($uid_action === 1) {
        echo header('Location: ../../');
        exit;
    }
#

# Get the people's details
    $get_people_figures_array = get_user_figures($people_uid); // Initiator
    $username = strtolower($get_people_figures_array['username']);
    $name = $get_people_figures_array['name'];
    $location = $get_people_figures_array['location'];
    $website = $get_people_figures_array['website'];
    $bio = nl2br($get_people_figures_array['bio']);
    $bio_forChange = trim($get_people_figures_array['bio']);
    $cover = $get_people_figures_array['cover'];
    $display = $get_people_figures_array['display']; # 6 is for shrink
#

# Get my details
    // $get_user_figures_array = get_user_figures($uid); // Initiator
    // $my_username = strtolower($get_user_figures_array['username']);
    // $my_name = $get_user_figures_array['name'];
    // $my_display = $get_user_figures_array['display_small'];
#

# Get the subscribe state between the user and people
    $subscribe_state = ($visitor_state == true) ? false : get_subscribe_state($people_uid, $uid);
    $state_variables = subscribe_state_variables($subscribe_state);
    $sub_state_text  = $state_variables['title'];
    $sub_state_state = $state_variables['state'];
#

# Get the user mode state
    $get_user_state = ($visitor_state == true) ? array('state'=>1) : get_user_state($uid);
    $state = ($get_user_state['state'] == 1) ? 'darkmode' : 'lightmode';
#

# Get the number of subscribers
    $my_subscribers_number = subscribes($people_uid, 'followers');
#


?>