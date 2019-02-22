<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$autoId = $_POST['sheep_care_edit_auto_id'];
$id = escapehtml($_POST['sheep_care_edit_id']);
$eye = $_POST['sheep_care_edit_eye'];
$body = $_POST['sheep_care_edit_body'];
$tail = escapehtml($_POST['sheep_care_edit_tail']);
$nose = escapehtml($_POST['sheep_care_edit_nose']);
$coat = escapehtml($_POST['sheep_care_edit_coat']);
$jaw = $_POST['sheep_care_edit_jaw'];
$wormer = escapehtml($_POST['sheep_care_edit_wormer']);
$quantity = escapehtml($_POST['sheep_care_edit_wormer_quantity']);
$hoof = $_POST['sheep_care_edit_hoof'];
$trim = $_POST['sheep_care_edit_trim'];
$weight = $_POST['sheep_care_edit_weight'];
$estimated = $_POST['sheep_care_edit_estimated'];
$comments = escapehtml($_POST['sheep_care_edit_comments']);
$mth = $_POST['sheep_care_edit_date_month'];
$day = $_POST['sheep_care_edit_date_day'];
$year = $_POST['sheep_care_edit_date_year'];

$sqlDate = $year."-".$mth."-".$day;

$dbcon->beginTransaction();
$sql = "update sheep_care set care_date=:dt, animal_id=:id, eye=:eye, ".
       "body=:body, tail=:tail, nose=:nose, coat=:coat, jaw=:jaw, wormer=".
       ":wormer, wormer_quantity=:quantity, hoof=:hoof, trim=:trim, weight=".
       ":weight, estimated=:estimated, comments=:comments where id=:autoId";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':dt', $sqlDate, PDO::PARAM_STR);
   $stmt->bindParam(':id', $id, PDO::PARAM_STR);
   $stmt->bindParam(':eye', $eye, PDO::PARAM_INT);
   $stmt->bindParam(':body', $body, PDO::PARAM_INT);
   $stmt->bindParam(':tail', $tail, PDO::PARAM_STR);
   $stmt->bindParam(':nose', $nose, PDO::PARAM_STR);
   $stmt->bindParam(':coat', $coat, PDO::PARAM_STR);
   $stmt->bindParam(':jaw', $jaw, PDO::PARAM_INT);
   $stmt->bindParam(':wormer', $wormer, PDO::PARAM_STR);
   $stmt->bindParam(':quantity', $quantity, PDO::PARAM_STR);
   $stmt->bindParam(':hoof', $hoof, PDO::PARAM_STR);
   $stmt->bindParam(':trim', $trim, PDO::PARAM_STR);
   $stmt->bindParam(':weight', $weight, PDO::PARAM_INT);
   $stmt->bindParam(':estimated', $estimated, PDO::PARAM_STR);
   $stmt->bindParam(':comments', $comments, PDO::PARAM_STR);
   $stmt->bindParam(':autoId', $autoId, PDO::PARAM_INT);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}
$dbcon->commit();
echo "success!";
?>
