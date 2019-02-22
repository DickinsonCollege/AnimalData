<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$alive = escapehtml($_GET['alive']);

$con = "";
$gsql = "select distinct animal_group from animal";
if ($alive == "true") {
   $gsql .= " where alive = 1";
}
$gsql .= " order by animal_group";
$gresult = $dbcon->query($gsql);
while ($grow = $gresult->fetch(PDO::FETCH_ASSOC)) {
   $grp = $grow['animal_group'];
   $con .=  "<optgroup label='".$grp."'>";
   $sql = "select animal_id, name from animal where animal_group = '".$grp."'";
   if ($alive == "true") {
      $sql .= " and alive = 1";
   }
   $sql .= " order by animal_id";
   $result = $dbcon->query($sql);
   while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
      $con .= "<option value='".$row['animal_id']."'>".
              print_name($row['animal_id'], $row['name'])."</option>";
   }
   $con .=  "</optgroup>";
}

echo $con;
?>
