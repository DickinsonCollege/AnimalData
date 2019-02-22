<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$arr = array();

$sql = "select animal_id, name from animal where (animal_group = 'SHEEP' or ".
       "animal_group = 'GOATS') and alive = 1 order by animal_id";
$result = $dbcon->query($sql);
$con = "";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['animal_id']."'>".
           print_name($row['animal_id'], $row['name'])."</option>";
}

$arr['animal_id'] = $con;

$sql = "select wormer from wormer where active=1";
$result = $dbcon->query($sql);
$con = "";
$con .= "<option value='NONE'>NONE</option>";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['wormer']."'>".
        $row['wormer']."</option>";
}

$arr['wormer'] = $con;

echo json_encode($arr);


?>
