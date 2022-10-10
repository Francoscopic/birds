
<?php

// Introduce connection
require_once('../../../../out/aquamarine/processors/dbconn.php');

save_subscribes();
function save_subscribes() {

    function subscribe_me($pub_uid, $cusm_uid) {
    
        global $connection_sur;

        $stmt = $connection_sur->prepare('INSERT INTO subscribes (publisher, customer, state) VALUES(?, ?, 1)');
        $stmt->bind_param('ss', $pub_uid, $cusm_uid);
        $stmt->execute();
        # Close variables, free memory
        unset($pub_uid, $cusm_uid);
        $stmt->reset();
        $connection_sur->close();
    }

    function validate_subscribe($pub_uid, $cusm_uid) {

        function unsubscribe_me($pub_uid, $cusm_uid, $state = 0) {

            global $connection_sur;
    
            $stmt = $connection_sur->prepare('UPDATE subscribes SET state = ? WHERE publisher = ? AND customer = ?');
            $stmt->bind_param('sss', $state, $pub_uid, $cusm_uid);
            $stmt->execute();
            # Close variables, free memory
            unset($pub_uid, $cusm_uid);
            $stmt->reset();
            $connection_sur->close();
        }

        global $connection_sur;
        $stmt = $connection_sur->prepare('SELECT state FROM subscribes WHERE publisher = ? AND customer = ?');
        $stmt->bind_param('ss', $pub_uid, $cusm_uid);
        $stmt->execute();
        $get_result = $stmt->get_result();

        if( $get_result->num_rows >= 1 ) {
            # Is a subscriber or unsubscriber
            $state = $get_result->fetch_array(MYSQLI_ASSOC)['state'];
            if($state == 1) {
                # Then, unsubscribe
                unsubscribe_me($pub_uid, $cusm_uid);
            } else {
                # re-SUBSCRIBE
                unsubscribe_me($pub_uid, $cusm_uid, 1);
            }
            return true;
        }
        return false;
        # Close variables
        unset($get_result);
        $stmt->reset();
        $connection_sur->close();
    }

    if( isset($_POST['publisher_uid'], $_POST['customer_uid']) ) {

        $publisher_uid = $_POST['publisher_uid'];
        $customer_uid = $_POST['customer_uid'];

        $get_state = validate_subscribe($publisher_uid, $customer_uid);
        if( $get_state == false ) {
            # is not a subscriber. (No data in base)
            # Subscribe. (Add data in base)
            subscribe_me($publisher_uid, $customer_uid);
        }
    }

}


?>