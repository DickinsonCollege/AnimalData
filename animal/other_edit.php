<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$autoId = $_POST['other_edit_auto_id'];
$id = escapehtml($_POST['other_edit_id']);
$origId = escapehtml($_POST['other_edit_orig_id']);
$mth = $_POST['other_edit_date_month'];
$day = $_POST['other_edit_date_day'];
$year = $_POST['other_edit_date_year'];
$reason = escapehtml($_POST['other_edit_reason']);
$dest = escapehtml($_POST['other_edit_dest']);
$weight = $_POST['other_edit_wt'];
$comments = escapehtml($_POST['other_edit_comments']);

$sqlDate = $year."-".$mth."-".$day;

$dbcon->beginTransaction();
$sql = "update other_remove set animal_id=:id, remove_date=:dt, ".
       "reason=:reason, destination=:dest, weight=:weight, comments=:comments".
       " where id=:autoId";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':id', $id, PDO::PARAM_STR);
   $stmt->bindParam(':dt', $sqlDate, PDO::PARAM_STR);
   $stmt->bindParam(':reason', $reason, PDO::PARAM_STR);
   $stmt->bindParam(':dest', $dest, PDO::PARAM_STR);
   $stmt->bindParam(':weight', $weight, PDO::PARAM_STR);
   $stmt->bindParam(':comments', $comments, PDO::PARAM_STR);
   $stmt->bindParam(':autoId', $autoId, PDO::PARAM_INT);
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
