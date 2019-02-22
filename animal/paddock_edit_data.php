<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$paddock = escapehtml($_GET['paddock']);
$sql = "select * from paddock where paddock_id = '".$paddock."'";
$res = $dbcon->query($sql);

if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo json_encode($row);
} else {
   echo "Error: no such paddock.";
}
?>
