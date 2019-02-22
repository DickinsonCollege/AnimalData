<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$type = escapehtml($_GET['type']);
$subtype = escapehtml($_GET['subtype']);

$sql = "select active from feed_subtype where type like '".$type.
       "' and subtype like '".$subtype."'";
$result = $dbcon->query($sql);
$con = "1";
if ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con = $row['active'];
}

echo $con;

?>
