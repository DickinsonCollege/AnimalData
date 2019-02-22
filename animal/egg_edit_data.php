<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$id = escapehtml($_POST['id']);
$sql = "select * from egg_log where id = ".$id;
$res = $dbcon->query($sql);

if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo json_encode($row);
} else {
   echo "Error: no such egg log record.";
}
?>
