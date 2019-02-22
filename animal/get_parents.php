<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$group = escapehtml($_GET['group']);
$arr = array();

$sql = "select animal_id from animal where animal_group like '".$group.
       "' and gender = 'F' and alive = 1";
$result = $dbcon->query($sql);
$con = "";
$con .= "<option value='N/A'>N/A</option>";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['animal_id']."'>".
        $row['animal_id']."</option>";
}

$arr['mother'] = $con;

$sql = "select animal_id from animal where animal_group like '".$group.
       "' and gender = 'M'";
$result = $dbcon->query($sql);
$con = "";
$con .= "<option value='N/A'>N/A</option>";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['animal_id']."'>".
        $row['animal_id']."</option>";
}

$arr['father'] = $con;

echo json_encode($arr);


?>
