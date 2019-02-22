<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$any = escapehtml($_GET['any']);
$active = escapehtml($_GET['active']);

$arr = array();

$con = "";
if ($any != "false") {
   $con .=  "<option value='%'>ALL</option>";
}
$gsql = "select distinct animal_group from animal ";
if ($active == "true") {
  $gsql .= "where alive = 1 ";
}
$gsql .= "order by animal_group";
$gresult = $dbcon->query($gsql);
while ($grow = $gresult->fetch(PDO::FETCH_ASSOC)) {
   $grp = $grow['animal_group'];
   $con .=  "<optgroup label='".$grp."'>";
   $sql = "select animal_id, name from animal where ";
   if ($active == "true") {
      $sql .= "alive = 1 and ";
   }
   $sql .= "animal_group = '".$grp."' order by animal_id";
   $result = $dbcon->query($sql);
   while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
      $con .= "<option value='".$row['animal_id']."'>".
              print_name($row['animal_id'], $row['name'])."</option>";
   }
   $con .=  "</optgroup>";
}

$arr['id'] = $con;

$sql = "select reason from reason";
if ($active == "true") {
   $sql .= " where active=1";
}
$result = $dbcon->query($sql);
$con = "";
if ($any != "false") {
   $con .=  "<option value='%'>ALL</option>";
}
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['reason']."'>".$row['reason']."</option>";
}

$arr['reason'] = $con;

$sql = "select medication, dosage from medication";
if ($active == "true") {
   $sql .= " where active=1";
}
$result = $dbcon->query($sql);
$con = "";
if ($any != "false") {
   $con .=  "<option value='%'>ALL</option>";
}
$dose = "";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['medication']."'>".
           $row['medication']."</option>";
   $dose .= "<option value='".$row['dosage']."'>".
           $row['dosage']."</option>";
}

$arr['medication'] = $con;
$arr['dosage'] = $dose;

echo json_encode($arr);
?>
