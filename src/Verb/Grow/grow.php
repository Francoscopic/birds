<?php

require_once __DIR__.'/../../../out/aquamarine/processors/dbconn.php';
require_once __DIR__.'/../big_sur/functions.php';

grow();

function grow() {

    if( isset($_POST['grow_home']) ) {

        $which_home = $_POST['grow_home'];

        if( $which_home === 'home' ) {
            grow_home();    // Index page
            // echo 'Working';
        }
        if( $which_home === 'community' ) {
            // grow_community();   // Community page
        }

        unset($which_home);
    }

    if( isset($_POST['grow_people']) ) {
        grow_people();  // People page
    }

    if( isset($_POST['grow_page']) ) {

        $which = $_POST['grow_page'];

        if( $which === 'profile' ) {
            grow_profile(); // Profile page
        }
        if( $which === 'saved' ) {
            grow_saved();   // Saved page
        }

        unset($which);
    }
}

function grow_home() {

    global $connection_sur;

        $uid = $_POST['uid'];
        $current_position = $_POST['start'];

        $stmt = $connection_sur->prepare("SELECT sid, uid, pid FROM big_sur WHERE uid NOT IN 
                                            (SELECT publisher FROM mute WHERE customer = '$uid' AND state = 1) 
                                            AND access = 1 
                                            ORDER BY sid DESC LIMIT $current_position, 15");
        $stmt->execute();
        $get_result = $stmt->get_result();

        while( $get_rows = $get_result->fetch_array(MYSQLI_ASSOC) )
        {

            # Get post and my details
                $the_pid = $get_rows['pid'];
                $poster_uid = $get_rows['uid'];
            #

            # Instantiate acting variables
                $my_note_row = get_this_note($the_pid);
                $note_title  = stripslashes($my_note_row['title']);
                $note_parags = $my_note_row['paragraphs'];
                $note_cover  = note_cover($my_note_row['cover'], 'notes', 'home');
                $note_state_article_or_image = ($my_note_row['state'] == 'art') ? 'hd' : '';
                $note_date   = timeAgo($my_note_row['date']);
            #

            $get_me            = get_me($poster_uid);
            $note_poster_name  = $get_me['name'];
            $note_poster_uname = $get_me['username'];

            # Get me view details
                $if_view = get_if_views($the_pid, $uid);
                $view_eye = ($if_view === true) ? '' : '*';
            #

            # Get small_menu details
                $small_menu_state = small_menu_validations($the_pid, $uid);
                $save_state = $small_menu_state['save'];
                $like_state = $small_menu_state['like'];
                $unlike_state = $small_menu_state['unlike'];
            #
    ?>
                <div class="nts-host relative">
                    <span id="page-assistant" class="hd" pid="<?php echo $the_pid ?>" puid="<?php echo $poster_uid ?>" uid="<?php echo $uid ?>" read="<?php echo PageRoutes('base', '?wp='. $the_pid)['article'] ?>" title="<?php echo $note_title ?>" poster="<?php echo $note_poster_name ?>" save_state="<?php echo $save_state ?>" like_state="<?php echo $like_state ?>" unlike_state="<?php echo $unlike_state ?>"></span>
                    <a href="<?php echo PageRoutes('base', '?wp='. $the_pid)['article'] ?>" class="vw-anchor nts-host-anchor a">
                        <div class="nts-host-display lozad bck relative" data-background-image="<?php echo $note_cover ?>">
                            <div class="nts-host-display-type nt-ui-rad4 ft-sect <?php echo $note_state_article_or_image ?>"><span>photo</span></div>
                            <div class="nts-host-display-filter"></div>
                        </div>
                        <div class="nts-host-verb ft-sect">
                            <p>
                                <strong title="Paragraphs"><?php echo $note_parags ?></strong><span class=""> paragraphs</span>
                            </p>
                        </div>
                        <div id="nts-host-title" class="nts-host-title">
                            <p class="trn3-color"><?php echo $view_eye ?><?php echo ShowMore($note_title, 14) ?></p>
                        </div>
                    </a>
                    <div class="nts-host-verb-author ft-sect">
                        <a href="<?php echo PageRoutes('base', '?up='. $note_poster_uname)['people'] ?>" class="a">
                            <p><?php echo $note_poster_name ?></p>
                        </a>
                        <a href="#" class="a">
                            <button class="nts-show-menu no-bod" visit="<?php echo $visitor_state ?>"><i class="lg-i fa fa-ellipsis-v"></i></button>
                        </a>
                    </div>
                </div>
    <?php
            }
            unset($connection_sur, $uid, $current_position, $stmt, $get_result, $get_rows);
            unset($the_pid, $poster_uid, $my_note_row, $note_title, $note_parags, $note_cover, $note_date, $note_poster_name, $note_poster_uname, $if_view, $view_eye);
    #
}

function grow_people() {
    global $connection_sur;

    grow_people_profile('big_sur', $connection_sur, 'people');

    unset($connection_sur);
}

function grow_profile() {
    global $connection_sur;

    grow_people_profile('big_sur', $connection_sur, 'profile');

    unset($connection_sur);
}

function grow_saved() {
    global $connection_verb;

    grow_people_profile('saves', $connection_verb, 'saved');

    unset($connection_verb);
}

function settle_disputes($db) {

    // First dispute is the difference in the poster's user_id
    // 'puid', for saves db and 'uid' for big_sur db
    if($db === 'saves') {
        unset($db);
        return array('puid');
    } else {
        unset($db);
        return array('uid');
    }
}

function grow_people_profile($db, $conn, $page) {

        $uid = $_POST['uid'];
        $muid = $_POST['muid'];
        $current_position = $_POST['start'];

        # Settle disputes
            $disputes = settle_disputes($db);
            $dispute_one = $disputes[0];
        #

        $stmt = $conn->prepare("SELECT * FROM $db WHERE uid = '$uid' AND access = 1 ORDER BY sid DESC LIMIT $current_position, 9");
        $stmt->execute();
        $get_result = $stmt->get_result();

        while( $get_rows = $get_result->fetch_array(MYSQLI_ASSOC) )
        {
            # Get post and my details
                $the_pid = $get_rows['pid'];
                $poster_uid = $get_rows["$dispute_one"]; // Make puid, for saves db and uid for big_sur
            #
            # Instantiate acting variables
                $my_note_row = get_this_note($the_pid);
                $note_title = stripslashes($my_note_row[0]);
                $note_parags = $my_note_row[1];
                $note_cover = note_cover($poster_uid, $my_note_row[2], '../');
                $note_date = timeAgo($my_note_row[3]);
            #
            $note_poster_name = get_me($poster_uid)[0];
            $note_poster_uname = get_me($poster_uid)[1];
            # Get me view details
                $view_num = note_views($the_pid);
                $if_view = get_if_views($the_pid, $uid);
                $view_eye = $if_view === true ? '' : 'hd';
            #
    ?>
            <div class="nts-host">
                <span id="page-assistant" class="hd" pid="<?php echo $the_pid ?>" uid="<?php echo $uid ?>" muid="<?php echo $muid ?>"></span>
                <a href="article.php?wp=<?php echo $the_pid ?>" class="vw-anchor-pages nts-host-anchor a">
                    <div class="nts-host-banner relative">
                        <div class="nts-host-display lozad rad2 bck" data-background-image="<?php echo $note_cover ?>">
                        </div>
                    </div>
                    <div id="nts-host-title" class="nts-host-title">
                        <p class="trn3-color"><?php echo ($view_eye == 'hd') ? '' : '*' ?><?php echo ShowMore($note_title, 14) ?></p>
                    </div>
                </a>
                <div class="nts-host-verb">
                    <p>
                        <strong title="Views"><?php echo $view_num ?></strong> <span class=""> views</span>
                        <strong title="Paragraphs"><i class="sm-i fa fa-paragraph"></i></strong> <span><?php echo $note_parags ?></span>
                    </p>
                </div>
                <div class="nts-host-verb-author">
                    <a href="people.php?up=<?php echo $note_poster_uname ?>" class="a">
                        <p><?php echo $note_poster_name ?></p>
                    </a>
                </div>
            </div>
    <?php
        }
        unset($uid, $current_position, $stmt, $get_result, $get_rows);
        unset($the_pid, $poster_uid, $my_note_row, $note_title, $note_parags, $note_cover, $note_date, $note_poster_name, $note_poster_uname, $if_view, $view_eye);
        unset($disputes, $dispute_one);
}


?>