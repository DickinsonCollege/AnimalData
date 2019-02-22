<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$reason = escapehtml($_GET['reason']);

$sql = "select active from other_reason where reason like '".$reason."'";
$result = $dbcon->query($sql);
$con = "1";
if ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con = $row['active'];
}

echo $con;

?>
