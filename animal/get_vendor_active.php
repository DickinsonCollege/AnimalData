<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$vendor = escapehtml($_GET['vendor']);

$sql = "select active from vendor where vendor like '".$vendor."'";
$result = $dbcon->query($sql);
$con = "1";
if ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con = $row['active'];
}

echo $con;

?>
