<?php

// Introduce connection
require_once('../../../out/aquamarine/processors/dbconn.php');

# ARTICLES

    for_articles();
    function for_articles() {

        if (isset($_POST['selection'], $_POST['message'], $_POST['uid'], $_POST['hid'])) {

            global $connection_help;
            $hid = $_POST['hid'];
            $uid = $_POST['uid'];
            $selection = $_POST['selection'];
            $message = $_POST['message'];

            $stmt = $connection_help->prepare('INSERT INTO help_response (hid, uid, response, suggestion) VALUES(?, ?, ?, ?)');
            $stmt->bind_param('ssss', $hid, $uid, $selection, $message);
            $stmt->execute();
        }
    }
#


?>