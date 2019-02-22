<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$sql = "select animal_group from animal_group where active = 1";
$result = $dbcon->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   echo "<option value='".$row['animal_group']."'>".
      $row['animal_group']."</option>";
}

?>
