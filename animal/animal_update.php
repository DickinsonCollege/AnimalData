<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$id = escapehtml($_GET['id']);

$arr = "";

$sql = "select * from animal where animal_id = '".$id."'";
$result = $dbcon->query($sql);
if ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $arr = $row;
}

if ($arr != "") {
   if ($arr['animal_group'] == 'CATTLE') {
      // START = get weight from cattle care record
   } else {
      $sql = "select weight from sheep_care where sheep_care.animal_id = '".
             $id."' and care_date >= all (select care_date from sheep_care ".
             "where animal_id = '".$id."') limit 1";
      $result = $dbcon->query($sql); 
      if ($row = $result->fetch(PDO::FETCH_ASSOC)) {
         $arr['weight'] = $row['weight'];
      }
   }
}

echo json_encode($arr);
?>
