<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$active = escapehtml($_GET['active']);
$all = escapehtml($_GET['all']);
$type = escapehtml($_GET['type']);

$sql = "select subtype from feed_subtype where type like '".$type."'";

if ($active == "true") {
   $sql .= " and active=1";
}
if ($all == "true") {
   echo "<option value='%'>ALL</option>";
}

$result = $dbcon->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   echo "<option value='".$row['subtype']."'>".$row['subtype']."</option>";
}

?>
