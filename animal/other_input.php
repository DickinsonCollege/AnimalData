<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$id = escapehtml($_POST['other_input_id']);
$mth = $_POST['other_input_date_month'];
$day = $_POST['other_input_date_day'];
$year = $_POST['other_input_date_year'];
$reason = escapehtml($_POST['other_input_reason']);
$dest = escapehtml($_POST['other_input_dest']);
$weight = $_POST['other_input_wt'];
$comments = escapehtml($_POST['other_input_comments']);

$sqlDate = $year."-".$mth."-".$day;

$dbcon->beginTransaction();
$sql = "insert into other_remove(animal_id, remove_date, reason, destination, ".
       "weight, comments) values (:id, :dt, :reason, :dest, :weight, ".
       ":comments)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':id', $id, PDO::PARAM_STR);
   $stmt->bindParam(':dt', $sqlDate, PDO::PARAM_STR);
   $stmt->bindParam(':reason', $reason, PDO::PARAM_STR);
   $stmt->bindParam(':dest', $dest, PDO::PARAM_STR);
   $stmt->bindParam(':weight', $weight, PDO::PARAM_STR);
   $stmt->bindParam(':comments', $comments, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

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

$dbcon->commit();
echo "success!";
?>
