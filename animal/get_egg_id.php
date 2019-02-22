<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$date = escapehtml($_GET['date']);

$sql = "select id from egg_log where coll_date = '".$date."'";

$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo $row['id'];
}
?>
