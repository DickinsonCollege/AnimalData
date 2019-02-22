<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/animal/util.php';
$host = "localhost";
$user = $_SESSION['dbuser'];
$pass = $_SESSION['dbpass'];
$dbName = $_SESSION['db'];
$dbcon = new PDO("mysql:host=".$host.";dbname=".$dbName, $user, $pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode="TRADITIONAL"'));
$dbcon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbcon->query("SET SESSION sql_mode = 'ALLOW_INVALID_DATES'");
?>
