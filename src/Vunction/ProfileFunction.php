<?php

namespace App\Vunction;

use App\Database\DatabaseAccess;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ProfileFunction
{
    public function notes_profile($uid): array
    {
        $content = array();

        $get_user_figures_array = IndexFunction::profile_user_figures($uid);

        $username      = strtolower($get_user_figures_array['username']);
        $name          = $get_user_figures_array['name'];
        $state         = ($get_user_figures_array['state'] === 1) ? 'darkmode' : 'lightmode';
        $location      = $get_user_figures_array['location'];
        $website       = $get_user_figures_array['website'];
        $bio           = nl2br($get_user_figures_array['about']);
        $bio_forChange = trim($bio);
        $cover         = $get_user_figures_array['cover'];
        $display       = $get_user_figures_array['display'];

        $subs_number = IndexFunction::subscribes($uid, 'followers');   // the people who follow me
        $my_subs     = IndexFunction::subscribes($uid, 'following');    // the people I follow

        $content = [
            'username'  => $username,
            'name'      => $name,
            'state'     => $state,
            'location'  => $location,
            'website'   => $website,
            'about'     => $bio,
            'cover'     => $cover,
            'display'   => $display,
            'followers' => $subs_number,
            'following' => $my_subs,
        ];
        return $content;
    }

    public function notes_articles($uid): array
    {
        $content = array();

        $connection_sur = new DatabaseAccess();
        $connection_sur = $connection_sur->connect('sur');

        // $stmt = $connection_sur->prepare("SELECT uid, pid FROM big_sur WHERE uid = ? AND access = 1 ORDER BY sid DESC LIMIT 15");
        $stmt = $connection_sur->prepare("SELECT uid, pid, access FROM big_sur WHERE uid = ? ORDER BY sid DESC LIMIT 15");
        $stmt->bind_param('s', $uid);
        $stmt->execute();
        $get_result = $stmt->get_result();
        $num_rows = $get_result->num_rows;

        while( $get_rows = $get_result->fetch_array(MYSQLI_ASSOC) ) {

            # Get post and my details
                $the_pid    = $get_rows['pid'];
                $poster_uid = $get_rows['uid'];
                $the_access = $get_rows['access'];
            #

            # Instantiate acting variables
                $my_note_row = IndexFunction::get_this_note($the_pid);
                $note_title  = stripslashes($my_note_row['title']);
                $note_parags = $my_note_row['paragraphs'];
                $note_cover  = IndexFunction::note_cover($my_note_row['cover'], 'notes');
                $note_state_article_or_image = ($my_note_row['state'] == 'art') ? false : true;
            #

            $get_note_poster_details = IndexFunction::get_me($poster_uid);
            $note_poster_name        = $get_note_poster_details['name'];
            $note_poster_uname       = $get_note_poster_details['username'];

            # Get me view details
                $if_view  = IndexFunction::get_if_views($the_pid, $uid);
                $view_eye = ($if_view === true) ? '*' : '';
            #
            $content[] = [
                'access'          => $the_access,
                'pid'             => $the_pid,
                'title'           => $note_title,
                'paragraphs'      => $note_parags,
                'cover'           => $note_cover,
                'note_is_img'     => $note_state_article_or_image,
                'poster_name'     => $note_poster_name,
                'poster_username' => $note_poster_uname,
                'if_view'         => $view_eye,
            ];
        }
        return $content;
    }

    public function notes_subscribe($people_uid, $uid, $visitor_state): array
    {
        $subscribe_state = ($visitor_state == true) ? false : IndexFunction::get_subscribe_state($people_uid, $uid);
        $state_variables = IndexFunction::subscribe_state_variables($subscribe_state);
        $sub_state_text  = $state_variables['title'];
        $sub_state_state = $state_variables['state'];
        return [
            'title' => $sub_state_text,
            'state' => $sub_state_state,
        ];
    }

    public function notes_follows($uid): array
    {
        $content = array();
        $connection_sur = new DatabaseAccess();
        $connection_sur = $connection_sur->connect('sur');

        $stmt = $connection_sur->prepare("SELECT customer FROM subscribes WHERE publisher = ? AND state = 1");
        $stmt->bind_param('s', $uid);
        $stmt->execute();
        $get_subbers = $stmt->get_result();

        $check = ($get_subbers->num_rows > 0); // returns bool
        while( $check && ($subbers = $get_subbers->fetch_array(MYSQLI_ASSOC)) ) {

            $subber_id = $subbers['customer'];

            $subber_details = IndexFunction::retrieve_details($subber_id);
            $subber_uname   = $subber_details['username'];
            $subber_name    = $subber_details['name'];
            $subber_display = $subber_details['display_small'];

            // labour
            $content[] = [
                'name'     => $subber_name,
                'username' => $subber_uname,
                'display'  => $subber_display,
            ];
        }
        return $content;
    }

    public function notes_following($uid): array
    {
        $content = array();
        $connection_sur = new DatabaseAccess();
        $connection_sur = $connection_sur->connect('sur');

        $stmt = $connection_sur->prepare("SELECT publisher FROM subscribes WHERE customer = ? AND state = 1");
        $stmt->bind_param('s', $uid);
        $stmt->execute();
        $get_subs = $stmt->get_result();

        $check = ($get_subs->num_rows > 0); // returns bool
        while( $check && ($subs = $get_subs->fetch_array(MYSQLI_ASSOC)) ) {

            $subs_id = $subs['publisher'];

            $subs_details = IndexFunction::retrieve_details($subs_id);
            $subber_uname   = $subs_details['username'];
            $subber_name    = $subs_details['name'];
            $subber_display = $subs_details['display_small'];

            # For the subscribe button
            $subs_state       = IndexFunction::subscribe_but($subs_id, $uid);
            $subs_state_text  = $subs_state['text'];
            $subs_state_state = $subs_state['state'];

            // labour
            $content[] = [
                'name'     => $subber_name,
                'username' => $subber_uname,
                'display'  => $subber_display,

                'state'      => $subs_state_state,
                'state_text' => $subs_state_text,
            ];
        }
        return $content;
    }

    public function notes_history($uid): array 
    {
        $content = array();
        $connection_verb = new DatabaseAccess();
        $connection_verb = $connection_verb->connect('verb');

        $stmt = $connection_verb->prepare("SELECT DISTINCT(post_id) FROM visits WHERE user_id=? AND state=1 ORDER BY date DESC LIMIT 15");
        $stmt->bind_param('s', $uid);
        $stmt->execute();
        $get_result = $stmt->get_result();
        $num_rows = $get_result->num_rows;

        while( $get_rows = $get_result->fetch_array(MYSQLI_ASSOC) ) {

            # Get post and my details
                $the_pid        = $get_rows['post_id'];
                $poster_user_id = IndexFunction::get_poster_uid($the_pid)['uid'];
            #

            # Instantiate acting variables
                $my_note_row        = IndexFunction::get_this_note($the_pid);
                $note_title         = IndexFunction::ShowMore(stripslashes($my_note_row['title']), 15);
                $note_parags        = $my_note_row['paragraphs'];
                $note_cover         = IndexFunction::note_cover($my_note_row['cover'], 'notes');
                $note_state_article_or_image = ($my_note_row['state'] == 'art') ? 'hd' : '';
                $note_date          = IndexFunction::timeAgo($my_note_row['date']);
            #

            $note_poster_data  = IndexFunction::get_me($poster_user_id);
            $note_poster_name  = $note_poster_data['name'];
            $note_poster_uname = $note_poster_data['username'];

            $content[] = [
                'pid'        => $the_pid,
                'title'      => $note_title,
                'paragraphs' => $note_parags,
                'cover'      => $note_cover,
                'type'       => $note_state_article_or_image,
                'date'       => $note_date,
                'username'   => $note_poster_uname,
                'name'       => $note_poster_name,
            ];
        }
        return array(
            'content' => $content,
            'message' => 'Get articles UID has read',
        );
    }

    public function notes_draft($uid): array
    {
        $content = array();
        $connection_sur = new DatabaseAccess();
        $connection_sur = $connection_sur->connect('sur');

        $stmt = $connection_sur->prepare("SELECT pid, title, body, date FROM big_sur_draft WHERE uid = ? AND access = 1 ORDER BY sid DESC LIMIT 10");
        $stmt->bind_param('s', $uid);
        $stmt->execute();
        $get_result = $stmt->get_result();
        $num_rows = $get_result->num_rows;

        $check = ($num_rows > 0); // returns bool
        while( $get_rows = $get_result->fetch_array(MYSQLI_ASSOC) ) {

            # Get post and my details
                $the_pid        = $get_rows['pid'];
                $the_title      = stripslashes($get_rows['title']);
                $the_paragraphs = IndexFunction::count_paragraphs($get_rows['body']);
                $the_date       = IndexFunction::timeAgo($get_rows['date']);
            #
            $content[] = [
                'pid'        => $the_pid,
                'title'      => $the_title,
                'paragraphs' => $the_paragraphs,
                'date'       => $the_date,
            ];
        }
        unset($connection_sur, $stmt, $get_result, $num_rows, $check, $the_pid, $the_title, $the_paragraphs, $the_date);
        return $content;
    }

    public function notes_saved($uid): array 
    {
        $content = array();
        $connection_verb = new DatabaseAccess();
        $connection_verb = $connection_verb->connect('verb');

        $stmt = $connection_verb->prepare("SELECT puid, pid FROM saves WHERE uid = ? AND state = 1 ORDER BY sid DESC LIMIT 12");
        $stmt->bind_param('s', $uid);
        $stmt->execute();
        $get_result = $stmt->get_result();
        $num_rows = $get_result->num_rows;

        while( $get_rows = $get_result->fetch_array(MYSQLI_ASSOC) ) {

            # Get post and my details
                $the_pid    = $get_rows['pid'];
                $poster_uid = $get_rows['puid'];
            #

            # Instantiate acting variables
                $my_note_row                 = IndexFunction::get_this_note($the_pid);
                $note_title                  = IndexFunction::ShowMore(stripslashes($my_note_row['title']));
                $note_parags                 = $my_note_row['paragraphs'];
                $note_cover                  = IndexFunction::note_cover($my_note_row['cover'], 'notes');
                $note_state_article_or_image = ($my_note_row['state'] == 'art') ? 'hd' : '';
                $note_date                   = IndexFunction::timeAgo($my_note_row['date']);
            #

            $note_poster_data  = IndexFunction::get_me($poster_uid);
            $note_poster_name  = $note_poster_data['name'];
            $note_poster_uname = $note_poster_data['username'];

            # Get me view details
                $if_view = IndexFunction::get_if_views($the_pid, $uid);
                $view_eye = ($if_view === true) ? '*' : '';
            #

            $content[] = [
                'pid'        => $the_pid,
                'title'      => $note_title,
                'paragraphs' => $note_parags,
                'cover'      => $note_cover,
                'type'       => $note_state_article_or_image,
                'date'       => $note_date,
                'name'       => $note_poster_name,
                'username'   => $note_poster_uname,
                'seen'       => $view_eye,
            ];
        }
        unset(
            $connection_verb, $stmt, $get_result, $num_rows, $the_pid, $poster_uid, 
            $my_note_row, $note_title, $note_parags, $note_cover, $note_state_article_or_image, 
            $note_date, $note_poster_data, $note_poster_name, $note_poster_uname, $if_view, $view_eye
        );
        return $content;
    }
} 