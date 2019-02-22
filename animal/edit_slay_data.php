<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$id = escapehtml($_GET['id']);

$sql = "select * from slaughter where id = ".$id;
$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo json_encode($row);
} else {
   echo "Error: no such slaughter record.";
   die();
}
?>
