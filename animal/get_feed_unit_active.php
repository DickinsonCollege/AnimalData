<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$feed_unit = escapehtml($_GET['feed_unit']);

$sql = "select active from feed_units where unit like '".$feed_unit."'";
$result = $dbcon->query($sql);
$con = "1";
if ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con = $row['active'];
}

echo $con;

?>
