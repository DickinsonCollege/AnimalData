<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$active = escapehtml($_GET['active']);
$all = escapehtml($_GET['all']);

$sql = "select unit from feed_units";
if ($active == "true") {
   $sql .= " where active=1";
}
if ($all == "true") {
   echo "<option value='ALL'>ALL</option>";
   echo "<option value='N/A'>N/A</option>";
}

$result = $dbcon->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   echo "<option value='".$row['unit']."'>".$row['unit']."</option>";
}

?>
