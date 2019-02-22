<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$wormer = escapehtml($_GET['wormer']);
$sql = "select * from wormer where wormer = '".$wormer."'";
$res = $dbcon->query($sql);

if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo json_encode($row);
} else {
   echo "Error: no such wormer.";
}
?>
