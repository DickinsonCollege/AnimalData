<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$active = escapehtml($_GET['active']);

$sql = "select medication from medication";
if ($active == 1) {
   $sql .= " where active = 1";
}
$result = $dbcon->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   echo "<option value='".$row['medication']."'>".
      $row['medication']."</option>";
}

?>
