<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$type = escapehtml($_GET['type']);

$sql = "select active from feed_type where type like '".$type."'";
$result = $dbcon->query($sql);
$con = "1";
if ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con = $row['active'];
}

echo $con;

?>
