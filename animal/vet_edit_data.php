<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$id = escapehtml($_POST['id']);
$sql = "select * from vet where id = ".$id;
$res = $dbcon->query($sql);

$result = array();
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   $result["vet"] = $row;
   $sql = "select * from meds_given where id = ".$id;
   $res = $dbcon->query($sql);
   if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
      $mres = array();
      do {
         array_push($mres, $row);
      } while ($row = $res->fetch(PDO::FETCH_ASSOC));
      $result["meds_given"] = $mres;
   } else {
      $result["meds_given"] = "";
   }
   echo json_encode($result);
} else {
   echo "Error: no such vet record.";
}
?>
