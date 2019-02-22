<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

// $id = escapehtml($_POST['id']);
$result = array();

$id = escapehtml($_GET['id']);

$sql = "select * from sheep_care where id = ".$id;
$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   $result['sheep'] = $row;
} else {
   echo "Error: no such sheep/goat care record.";
   die();
}

$sql = "select animal_id from animal where (animal_group = 'SHEEP' or ".
       "animal_group = 'GOATS') and alive = 1";
$res = $dbcon->query($sql);
$con = "";
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['animal_id']."'>".$row['animal_id']."</option>";
}
$result['ids'] = $con;

$sql = "select wormer from wormer where active=1";
$res = $dbcon->query($sql);
$con = "";
$con .= "<option value='NONE'>NONE</option>";
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['wormer']."'>".
        $row['wormer']."</option>";
}

$result['wormer'] = $con;

echo json_encode($result);
?>
