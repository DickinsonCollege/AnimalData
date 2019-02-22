<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$active = escapehtml($_GET['active']);

$sql = "select task from task";
if ($active == "true") {
   $sql .= " where active = 1";
}
$sql .= " order by task";
$result = $dbcon->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   echo "<option value='".$row['task']."'>".$row['task']."</option>";
}

?>
