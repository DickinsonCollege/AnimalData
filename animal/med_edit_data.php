<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$medication = escapehtml($_GET['med']);
$sql = "select * from medication where medication = '".$medication."'";
$res = $dbcon->query($sql);

if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo json_encode($row);
} else {
   echo "Error: no such medication.";
}
?>
