<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$forage = escapehtml($_GET['forage']);
$sql = "select * from forage where forage = '".$forage."'";
$res = $dbcon->query($sql);

if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo json_encode($row);
} else {
   echo "Error: no such forage.";
}
?>
