
<?php

require_once('../../../out/aquamarine/processors/dbconn.php');

change_dark_light();
function change_dark_light() {

    if( isset( $_POST['state'], $_POST['uid'] ) ) {

        // introduce connection
        global $connection;

        $the_state = strtolower($_POST['state']);
        $the_uid = trim($_POST['uid']);
        $state = null;

        $stmt = $connection->prepare("UPDATE user_sapphire SET state = ? WHERE uid = ?");
        $stmt->bind_param('ss', $state, $the_uid);

        // validate first
        // echo ($the_state == 'dark') ? 'Light OFF' : 'Light ON';
        ($the_state == 'dark') ? $state = 1 : $state = 0;
        $stmt->execute();
    }
    unset($the_state, $the_uid, $state);
    $stmt->reset();
    $connection->close();
}

?>