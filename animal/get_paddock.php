<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$active = escapehtml($_GET['active']);

$sql = "select paddock_id from paddock";
if ($active == 1) {
   $sql .= " where active = 1";
}
$sql .= " order by paddock_id";
$result = $dbcon->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   echo "<option value='".$row['paddock_id']."'>".
      $row['paddock_id']."</option>";
}

?>
