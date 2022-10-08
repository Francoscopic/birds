<?php


$dbhost = 'localhost';
$dbname = 'notes';
$dbuser = 'root';
// $dbpass = 'XkWX3vXbsJG5FiM2';
$dbpass = '';
$connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($connection->connect_error) die($connection->connect_error);

# DB OPEN for BUSINESS

// $_dbuser = 'general_for_sur';
$_dbuser = 'root';
// $_dbpass = '3RNPIDkTOvqaU2v3';
$_dbpass = '';

$__dbname = 'notes_verbs';
$connection_verb = new mysqli($dbhost, $_dbuser, $_dbpass, $__dbname);
if ($connection_verb->connect_error) die($connection_verb->connect_error);

$__dbname_ = 'notes_big_sur';
$connection_sur = new mysqli($dbhost, $_dbuser, $_dbpass, $__dbname_);
if ($connection_sur->connect_error) die($connection_sur->connect_error);

$__dbname__ = 'notes_help';
$connection_help = new mysqli($dbhost, $_dbuser, $_dbpass, $__dbname__);
if ($connection_help->connect_error) die($connection_help->connect_error);

 ?>
