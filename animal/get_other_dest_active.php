<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$dest = escapehtml($_GET['dest']);

$sql = "select active from other_dest where destination like '".$dest."'";
$result = $dbcon->query($sql);
$con = "1";
if ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con = $row['active'];
}

echo $con;

?>
