<?php

// Introduce connection
require_once('../../../out/aquamarine/processors/dbconn.php');

# PROFILE
    delete_note();
    function delete_note() {

        if (isset($_POST['profile_pid'], $_POST['profile_uid'])) {

            global $connection_sur;
            $access = 0;

            $pid = $_POST['profile_pid'];
            $uid = $_POST['profile_uid'];

            $stmt = $connection_sur->prepare('UPDATE big_sur_list SET access = ? WHERE pid = ?');
            $stmt->bind_param('ss', $access, $pid);
            $stmt->execute();
            $stmt = $connection_sur->prepare('UPDATE big_sur SET access = ? WHERE pid = ? AND uid = ?');
            $stmt->bind_param('sss', $access, $pid, $uid);
            $stmt->execute();

            # Free memory
            unset($stmt, $connection_sur, $access, $pid);
        }
    }
#


# DRAFT
    delete_draft();
    function delete_draft() {

        if (isset($_POST['draft_pid'], $_POST['draft_uid'])) {

            global $connection_sur;
            $access = 0;

            $pid = $_POST['draft_pid'];
            $uid = $_POST['draft_uid'];

            $stmt = $connection_sur->prepare('UPDATE big_sur_draft SET access = ? WHERE pid = ? AND uid = ?');
            $stmt->bind_param('iss', $access, $pid, $uid);
            $stmt->execute();

            echo 13;
            unset($stmt, $connection_sur, $access, $uid, $pid);
        }
    }
#


# SAVED
    remove_saved();
    function remove_saved() {

        if (isset($_POST['saved_del_pid'], $_POST['saved_del_uid'])) {
            global $connection_verb;

            $pid = $_POST['saved_del_pid'];
            $uid = $_POST['saved_del_uid'];

            $stmt = $connection_verb->prepare('UPDATE saves SET state = 0 WHERE pid=? AND uid=?');
            $stmt->bind_param('ss', $pid, $uid);
            $stmt->execute();
            unset($connection_verb, $stmt, $pid, $uid);
            echo 13;
        }
    }
#


# HISTORY
    remove_history();
    function remove_history() {

        if (isset($_POST['history_del_pid'], $_POST['history_del_uid'])) {
            global $connection_verb;

            $pid = $_POST['history_del_pid'];
            $uid = $_POST['history_del_uid'];

            $stmt = $connection_verb->prepare('UPDATE views SET access = 0 WHERE pid=? AND uid=?');
            $stmt->bind_param('ss', $pid, $uid);
            $stmt->execute();
            unset($connection_verb, $stmt, $pid, $uid);
            echo 13;
        }
    }
#

?>