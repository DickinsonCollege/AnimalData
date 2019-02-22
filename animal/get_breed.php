<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$group = escapehtml($_GET['group']);
$active = escapehtml($_GET['active']);

$sql = "select breed from breed where animal_group like '".$group.  "'";
if ($active == "true") {
   $sql .= " and active = 1";
}
$result = $dbcon->query($sql);
$con = "";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['breed']."'>".$row['breed']."</option>";
}

echo $con;

?>
