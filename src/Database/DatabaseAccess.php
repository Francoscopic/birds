<?php

namespace App\Database;

use mysqli;

class DatabaseAccess
{

    private $dbhost = 'localhost';
    //
    private $db_user_1 = 'root';
    private $db_user_2 = 'root';
    private $db_user_3 = 'root';
    private $db_user_4 = 'root';
    //
    private $db_name_1 = 'notes';
    private $db_name_2 = 'notes_verbs';
    private $db_name_3 = 'notes_big_sur';
    private $db_name_4 = 'notes_help';
    //
    private $db_pass_1 = '';
    private $db_pass_2 = '';
    private $db_pass_3 = '';
    private $db_pass_4 = '';

    public function connect($db_alias) {
        $connection;

        $dbhost = $this->dbhost;
        //
        $db_user_1 = $this->db_user_1;
        $db_user_2 = $this->db_user_2;
        $db_user_3 = $this->db_user_3;
        $db_user_4 = $this->db_user_4;
        //
        $db_name_1 = $this->db_name_1;
        $db_name_2 = $this->db_name_2;
        $db_name_3 = $this->db_name_3;
        $db_name_4 = $this->db_name_4;
        //
        $db_pass_1 = $this->db_pass_1;
        $db_pass_2 = $this->db_pass_2;
        $db_pass_3 = $this->db_pass_3;
        $db_pass_4 = $this->db_pass_4;

        switch ($db_alias)
        {
            case 'sur':
                $connection = new mysqli($dbhost, $db_user_3, $db_pass_3, $db_name_3);
                if ($connection->connect_error) die($connection->connect_error);
                break;
            case 'verbs':
                $connection = new mysqli($dbhost, $db_user_2, $db_pass_2, $db_name_2);
                if ($connection->connect_error) die($connection->connect_error);
                break;
            case 'help':
                $connection = new mysqli($dbhost, $db_user_4, $db_pass_4, $db_name_4);
                if ($connection->connect_error) die($connection->connect_error);
                break;
            default:
                $connection = new mysqli($dbhost, $db_user_1, $db_pass_1, $db_name_1);
                if ($connection->connect_error) die($connection->connect_error);
                break;
        }
        return $connection;
    }
}


?>
