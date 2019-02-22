<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$user = escapehtml($_GET['user']);

$sql = "select * from users where username = '".$user."'";
$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo json_encode($row);
} else {
   echo "Error: no such user.";
}
?>
