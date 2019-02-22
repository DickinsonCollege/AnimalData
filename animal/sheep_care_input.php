<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$id = escapehtml($_POST['sheep_care_input_id']);
$eye = $_POST['sheep_care_input_eye'];
$body = $_POST['sheep_care_input_body'];
$tail = escapehtml($_POST['sheep_care_input_tail']);
$nose = escapehtml($_POST['sheep_care_input_nose']);
$coat = escapehtml($_POST['sheep_care_input_coat']);
$jaw = $_POST['sheep_care_input_jaw'];
$wormer = escapehtml($_POST['sheep_care_input_wormer']);
$quantity = escapehtml($_POST['sheep_care_input_wormer_quantity']);
$hoof = $_POST['sheep_care_input_hoof'];
$trim = $_POST['sheep_care_input_trim'];
$weight = $_POST['sheep_care_input_weight'];
$estimated = $_POST['sheep_care_input_estimated'];
$comments = escapehtml($_POST['sheep_care_input_comments']);
$mth = $_POST['sheep_care_input_date_month'];
$day = $_POST['sheep_care_input_date_day'];
$year = $_POST['sheep_care_input_date_year'];

$sqlDate = $year."-".$mth."-".$day;

$dbcon->beginTransaction();
$sql = "insert into sheep_care(care_date, animal_id, eye, body, tail, nose, ".
       "coat, jaw, wormer, wormer_quantity, hoof, trim, weight, estimated, ".
       "comments) values(:dt, :id, :eye, :body, :tail, :nose, :coat, :jaw, ".
       ":wormer, :quantity, :hoof, :trim, :weight, :estimated, :comments)";
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
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}
$dbcon->commit();
echo "success!";
?>
