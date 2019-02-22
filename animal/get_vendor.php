<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$active = escapehtml($_GET['active']);
$all = escapehtml($_GET['all']);

$sql = "select vendor from vendor";
if ($active == "true") {
   $sql .= " where active=1";
}
if ($all == "true") {
   echo "<option value='%'>ALL</option>";
}

$result = $dbcon->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   echo "<option value='".$row['vendor']."'>".$row['vendor']."</option>";
}

?>
