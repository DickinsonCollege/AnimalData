<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$type = escapehtml($_GET['type']);

$arr = array();

$sql = "select subtype from feed_subtype where type like '".$type.
   "' and active = 1";
$result = $dbcon->query($sql);
$con = "";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['subtype']."'>".
        $row['subtype']."</option>";
}

$arr['subtype'] = $con;

$sql = "select animal_group from animal_group";
$result = $dbcon->query($sql);
$con = "<option value='ALL'>ALL</option><option value='N/A'>N/A</option>";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['animal_group']."'>".
        $row['animal_group']."</option>";
}

$arr['group'] = $con;

$sql = "select vendor from vendor where active = 1";
$result = $dbcon->query($sql);
$con = "";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['vendor']."'>".
        $row['vendor']."</option>";
}

$arr['vendor'] = $con;

$sql = "select unit from feed_units where active = 1";
$result = $dbcon->query($sql);
$con = "";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['unit']."'>".
        $row['unit']."</option>";
}

$arr['unit'] = $con;

echo json_encode($arr);
?>
