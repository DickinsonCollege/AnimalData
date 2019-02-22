<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$autoId = $_POST['slay_edit_auto_id'];
$id = escapehtml($_POST['slay_edit_id']);
$origId = escapehtml($_POST['slay_edit_orig_id']);
$mth = $_POST['slay_edit_date_month'];
$day = $_POST['slay_edit_date_day'];
$year = $_POST['slay_edit_date_year'];
$tag = escapehtml($_POST['slay_edit_tag']);
$weight = $_POST['slay_edit_weight'];
$estimated = escapehtml($_POST['slay_edit_estimated']);
$house = escapehtml($_POST['slay_edit_house']);
$hauler = escapehtml($_POST['slay_edit_hauler']);
$haul_equip = escapehtml($_POST['slay_edit_haul_equip']);
$fee = $_POST['slay_edit_fee'];
$comments = escapehtml($_POST['slay_edit_comments']);

$sqlDate = $year."-".$mth."-".$day;

$dbcon->beginTransaction();
$sql = "update slaughter set animal_id=:id, slay_date=:dt, sale_tag=:tag, ".
       "weight=:weight, estimated=:estimated, slay_house=:house, ".
       "hauler=:hauler, haul_equip=:haul_equip, fees=:fee, ".
       "comments=:comments where id=:autoId";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':id', $id, PDO::PARAM_STR);
   $stmt->bindParam(':dt', $sqlDate, PDO::PARAM_STR);
   $stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
   $stmt->bindParam(':weight', $weight, PDO::PARAM_STR);
   $stmt->bindParam(':estimated', $estimated, PDO::PARAM_STR);
   $stmt->bindParam(':house', $house, PDO::PARAM_STR);
   $stmt->bindParam(':hauler', $hauler, PDO::PARAM_STR);
   $stmt->bindParam(':haul_equip', $haul_equip, PDO::PARAM_STR);
   $stmt->bindParam(':comments', $comments, PDO::PARAM_STR);
   $stmt->bindParam(':fee', $fee, PDO::PARAM_STR);
   $stmt->bindParam(':autoId', $autoId, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

if ($id != $origId) {
   // kill new animal
   $sql = "update animal set alive=0 where animal_id = :id";
   try {
      $stmt = $dbcon->prepare($sql);
      $stmt->bindParam(':id', $id, PDO::PARAM_STR);
      $stmt->execute();
   } catch (PDOException $p) {
      echo $p->getMessage();
      $dbcon->rollBack();
      die();
   }

   // resurrect old animal
   $sql = "update animal set alive=1 where animal_id = :origId";
   try {
      $stmt = $dbcon->prepare($sql);
      $stmt->bindParam(':origId', $origId, PDO::PARAM_STR);
      $stmt->execute();
   } catch (PDOException $p) {
      echo $p->getMessage();
      $dbcon->rollBack();
      die();
   }
}

$dbcon->commit();
echo "success!";
?>
