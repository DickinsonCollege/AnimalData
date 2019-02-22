<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$active = escapehtml($_GET['active']);

$arr = array();

$sql = "select paddock_id from paddock";
if ($active == 1) {
   $sql .= " where active = 1";
}
$sql .= " order by paddock_id";
$con = "";
$result = $dbcon->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['paddock_id']."'>".
      $row['paddock_id']."</option>";
}
$arr['paddock'] = $con;


$sql = "select animal_group from animal_group where active = 1";
$result = $dbcon->query($sql);
$con = "";
$first = true;
$grp = "";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .=  "<option value='".$row['animal_group']."'>".
      $row['animal_group']."</option>";
   if ($first) {
      $first = false;
      $grp = $row['animal_group'];
   }
}
$arr['group'] = $con;

$con = "";
if ($grp !== "") {
   $sql = "select sub_group from sub_group where animal_group like '".$grp."'";
   $sql .= " and active = 1";
   $result = $dbcon->query($sql);
   while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
      $con .= "<option value='".$row['sub_group']."'>".$row['sub_group']."</option>";
   }
}
$arr['subgroup'] = $con;
   
echo json_encode($arr);
?>
