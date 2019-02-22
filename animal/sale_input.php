<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$id = escapehtml($_POST['sale_input_id']);
$mth = $_POST['sale_input_date_month'];
$day = $_POST['sale_input_date_day'];
$year = $_POST['sale_input_date_year'];
$tag = escapehtml($_POST['sale_input_tag']);
$dest = escapehtml($_POST['sale_input_dest']);
$weight = $_POST['sale_input_weight'];
$estimated = escapehtml($_POST['sale_input_estimated']);
$price = $_POST['sale_input_price'];
$fee = $_POST['sale_input_fee'];
$comments = escapehtml($_POST['sale_input_comments']);

$sqlDate = $year."-".$mth."-".$day;

$dbcon->beginTransaction();
$sql = "insert into sale(animal_id, sale_date, sale_tag, destination, ".
       "weight, estimated, price_lb, fees, comments) values (:id, :dt, :tag, ".
       ":dest, :weight, :estimated, :price, :fee, :comments)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':id', $id, PDO::PARAM_STR);
   $stmt->bindParam(':dt', $sqlDate, PDO::PARAM_STR);
   $stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
   $stmt->bindParam(':dest', $dest, PDO::PARAM_STR);
   $stmt->bindParam(':weight', $weight, PDO::PARAM_STR);
   $stmt->bindParam(':estimated', $estimated, PDO::PARAM_STR);
   $stmt->bindParam(':price', $price, PDO::PARAM_STR);
   $stmt->bindParam(':fee', $fee, PDO::PARAM_STR);
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
