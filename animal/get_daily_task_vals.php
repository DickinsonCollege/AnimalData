<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";


$arr = array();

$con = "";
$sql = "select task from task where active = 1";
$result = $dbcon->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
      $con .= "<option value='".$row['task']."'>".$row['task']."</option>";
}
$arr['tasks'] = $con;

$con = "<option value='ALL'>ALL</option>";
$subgroups = array();
$subgroups['ALL'] = "<option value='ALL'>ALL</option>";
$sql = "select animal_group from animal_group where active = 1";
$result = $dbcon->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['animal_group']."'>".
           $row['animal_group']."</option>";
   $subgroups[$row['animal_group']] = "<option value='ALL'>ALL</option>";
}
$arr['groups'] = $con;

$sql = "select animal_group, sub_group from sub_group where active = 1";
$result = $dbcon->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
  $grp = $row['animal_group'];
  $subgrp = $row['sub_group'];
  $sa = $subgroups[$grp];
  $sa .= "<option value='".$subgrp."'>".$subgrp."</option>";
  $subgroups[$grp] = $sa;
}
$arr['subgroups'] = $subgroups;

echo json_encode($arr);
?>
