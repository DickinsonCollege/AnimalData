<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$name = escapehtml($_GET['name']);

$sql = "select * from animal where name='".$name."' and alive=1";
$result = $dbcon->query($sql);
if ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   echo "true";
} else {
   echo "false";
}
?>
