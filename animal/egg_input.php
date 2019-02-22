<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$mth = $_POST['egg_input_date_month'];
$day = $_POST['egg_input_date_day'];
$year = $_POST['egg_input_date_year'];
$num = $_POST['egg_input_amt'];
$comments = escapehtml($_POST['egg_input_comments']);

$sqlDate = $year."-".$mth."-".$day;

$dbcon->beginTransaction();
$sql = "insert into egg_log(coll_date, number, comments) values (:dt, :num, :comments)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':dt', $sqlDate, PDO::PARAM_STR);
   $stmt->bindParam(':num', $num, PDO::PARAM_INT);
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
