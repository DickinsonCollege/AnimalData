<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$house = escapehtml($_GET['house']);

$sql = "select active from slay_house where slay_house like '".$house."'";
$result = $dbcon->query($sql);
$con = "1";
if ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con = $row['active'];
}

echo $con;

?>
