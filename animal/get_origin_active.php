<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$origin = escapehtml($_GET['origin']);

$sql = "select active from origin where origin like '".$origin."'";
$result = $dbcon->query($sql);
$con = "1";
if ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con = $row['active'];
}

echo $con;

?>
