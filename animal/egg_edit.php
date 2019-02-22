<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$autoId = $_POST['egg_edit_auto_id'];
$mth = $_POST['egg_edit_date_month'];
$day = $_POST['egg_edit_date_day'];
$year = $_POST['egg_edit_date_year'];
$amt = $_POST['egg_edit_amt'];
$comments = escapehtml($_POST['egg_edit_comments']);

$sqlDate = $year."-".$mth."-".$day;

$dbcon->beginTransaction();
$sql = "update egg_log set coll_date=:date, number=:amt, comments=:comments where id=:autoId";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':date', $sqlDate, PDO::PARAM_STR);
   $stmt->bindParam(':amt', $amt, PDO::PARAM_INT);
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
