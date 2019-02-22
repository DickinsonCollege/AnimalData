<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$task = escapehtml($_GET['task']);

$sql = "select active from task where task = '".$task."'";
$res = $dbcon->query($sql);

if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo $row['active'];
} else {
   echo "Error: no such task.";
}
?>
