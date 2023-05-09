<?php

namespace App\Vunction;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class ProfileFunction
{
    private $conn;

    public function __construct(Connection $connection) {
        $this->conn = $connection;
    }
    
    public function notes_profile($uid): array
    {
        $content = array();

        $get_user_figures_array = IndexFunction::profile_user_figures($this->conn, $uid);

        $username      = strtolower($get_user_figures_array['username']);
        $name          = $get_user_figures_array['name'];
        $state         = ($get_user_figures_array['state'] === 1) ? 'darkmode' : 'lightmode';
        $location      = $get_user_figures_array['location'];
        $website       = $get_user_figures_array['website'];
        $bio           = nl2br($get_user_figures_array['about']);
        $bio_forChange = trim($bio);
        $cover         = $get_user_figures_array['cover'];
        $display       = $get_user_figures_array['display'];

        $subs_number = IndexFunction::subscribes($this->conn, $uid, 'follower');   // the people who follow me
        $my_subs     = IndexFunction::subscribes($this->conn, $uid, 'following');    // the people I follow

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

        foreach($this->conn->iterateAssociativeIndexed(
            'SELECT id, uid, pid, access FROM big_sur WHERE uid = ? ORDER BY id DESC LIMIT 15', [$uid]) 
            as $id => $data
        ) {
            # Get post and my details
                $the_pid    = $data['pid'];
                $poster_uid = $data['uid'];
                $the_access = $data['access'];
            #

            # Instantiate acting variables
                $my_note_row = IndexFunction::get_this_note($this->conn, $the_pid);
                $note_title  = stripslashes($my_note_row['title']);
                $note_parags = $my_note_row['paragraphs'];
                $note_cover  = IndexFunction::note_cover($my_note_row['cover'], 'notes');
                $note_state_article_or_image = ($my_note_row['state'] == 'art') ? false : true;
            #

            $get_note_poster_details = IndexFunction::get_me($this->conn, $poster_uid);
            $note_poster_name        = $get_note_poster_details['name'];
            $note_poster_uname       = $get_note_poster_details['username'];

            # Get me view details
                $if_view  = IndexFunction::get_if_views($this->conn, $the_pid, $uid);
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
        $subscribe_state = ($visitor_state == true) ? false : IndexFunction::get_subscribe_state($this->conn, $people_uid, $uid);
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

        foreach($this->conn->iterateAssociativeIndexed(
            'SELECT id, follower FROM big_sur_subscribes WHERE following = ? AND state = 1', [$uid]) 
            as $id => $data
        ) {
            $subber_id = $data['follower'];

            $subber_details = IndexFunction::retrieve_details($this->conn, $subber_id);
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

        foreach($this->conn->iterateAssociativeIndexed(
            'SELECT id, following FROM big_sur_subscribes WHERE follower = ? AND state = 1', [$uid]) 
            as $id => $data
        ) {
            $subs_id = $data['following'];

            $subs_details = IndexFunction::retrieve_details($this->conn, $subs_id);
            $subber_uname   = $subs_details['username'];
            $subber_name    = $subs_details['name'];
            $subber_display = $subs_details['display_small'];

            # For the subscribe button
            $subs_state       = IndexFunction::subscribe_but($this->conn, $subs_id, $uid);
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

        foreach($this->conn->iterateAssociativeIndexed(
            'SELECT DISTINCT(pid) AS pid FROM verb_visits WHERE uid=? AND state=1 ORDER BY id DESC LIMIT 15', [$uid]) 
            as $id => $data
        ) {
            # Get post and my details
                $the_pid        = $id;
                $poster_user_id = IndexFunction::get_poster_uid($this->conn, $the_pid)['uid'];
            #

            # Instantiate acting variables
                $my_note_row        = IndexFunction::get_this_note($this->conn, $the_pid);
                $note_title         = IndexFunction::ShowMore(stripslashes($my_note_row['title']), 15);
                $note_parags        = $my_note_row['paragraphs'];
                $note_cover         = IndexFunction::note_cover($my_note_row['cover'], 'notes');
                $note_state_article_or_image = ($my_note_row['state'] == 'art') ? 'hd' : '';
                $note_date          = IndexFunction::timeAgo($my_note_row['date']);
            #

            $note_poster_data  = IndexFunction::get_me($this->conn, $poster_user_id);
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

        foreach($this->conn->iterateAssociativeIndexed(
            'SELECT id, pid, title, body, date FROM big_sur_draft WHERE uid = ? AND access = 1 ORDER BY id DESC LIMIT 10', [$uid]) 
            as $id => $data
        ) {
            # Get post and my details
                $the_pid        = $data['pid'];
                $the_title      = stripslashes($data['title']);
                $the_paragraphs = IndexFunction::count_paragraphs($data['body']);
                $the_date       = IndexFunction::timeAgo($data['date']);
            #
            $content[] = [
                'pid'        => $the_pid,
                'title'      => $the_title,
                'paragraphs' => $the_paragraphs,
                'date'       => $the_date,
            ];
        }
        unset($the_pid, $the_title, $the_paragraphs, $the_date);
        return $content;
    }

    public function notes_saved($uid): array 
    {
        $content = array();

        foreach($this->conn->iterateAssociativeIndexed(
            'SELECT id, puid, pid FROM verb_saves WHERE uid = ? AND state = 1 ORDER BY id DESC LIMIT 12', [$uid]) 
            as $id => $data
        ) {
            # Get post and my details
                $the_pid    = $data['pid'];
                $poster_uid = $data['puid'];
            #

            # Instantiate acting variables
                $my_note_row                 = IndexFunction::get_this_note($this->conn, $the_pid);
                $note_title                  = IndexFunction::ShowMore(stripslashes($my_note_row['title']));
                $note_parags                 = $my_note_row['paragraphs'];
                $note_cover                  = IndexFunction::note_cover($my_note_row['cover'], 'notes');
                $note_state_article_or_image = ($my_note_row['state'] == 'art') ? 'hd' : '';
                $note_date                   = IndexFunction::timeAgo($my_note_row['date']);
            #

            $note_poster_data  = IndexFunction::get_me($this->conn, $poster_uid);
            $note_poster_name  = $note_poster_data['name'];
            $note_poster_uname = $note_poster_data['username'];

            # Get me view details
                $if_view = IndexFunction::get_if_views($this->conn, $the_pid, $uid);
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
            $the_pid, $poster_uid, 
            $my_note_row, $note_title, $note_parags, $note_cover, $note_state_article_or_image, 
            $note_date, $note_poster_data, $note_poster_name, $note_poster_uname, $if_view, $view_eye
        );
        return $content;
    }
} 