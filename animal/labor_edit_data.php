<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$id = escapehtml($_GET['id']);

$sql = "select task_entry.*, list_date from task_entry, task_master ".
       "where task_entry.id = ".$id." and task_entry.m_id = task_master.id";
$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo json_encode($row);
} else {
   echo "Error: no such labor record.";
   die();
}

?>
