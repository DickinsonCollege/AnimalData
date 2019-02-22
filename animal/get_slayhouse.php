<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$active = escapehtml($_GET['active']);
$all = escapehtml($_GET['all']);

$sql = "select slay_house from slay_house";
if ($active == "true") {
   $sql .= " where active=1";
}
$result = $dbcon->query($sql);
if ($all == "true") {
   echo "<option value='%'>ALL</option>";
}
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   echo "<option value='".$row['slay_house']."'>".$row['slay_house']."</option>";
}

?>
