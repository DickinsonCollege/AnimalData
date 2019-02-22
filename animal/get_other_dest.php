<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$active = escapehtml($_GET['active']);
$all = escapehtml($_GET['all']);

$sql = "select destination from other_dest";
if ($active == "true") {
   $sql .= " where active=1";
}
$result = $dbcon->query($sql);
if ($all == "true") {
   echo "<option value='%'>ALL</option>";
}
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   echo "<option value='".$row['destination']."'>".$row['destination']."</option>";
}

?>
