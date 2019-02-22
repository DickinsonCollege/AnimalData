<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$mth = $_POST['feed_input_date_month'];
$day = $_POST['feed_input_date_day'];
$year = $_POST['feed_input_date_year'];
$type = escapehtml($_POST['feed_input_type']);
$subtype = escapehtml($_POST['feed_input_subtype']);
$for = escapehtml($_POST['feed_input_group']);
if ($for == '%') {
   $for = 'ALL';
}
$vendor = escapehtml($_POST['feed_input_vendor']);
$unit = escapehtml($_POST['feed_input_unit']);
$purch = $_POST['feed_input_purchased'];
$price = $_POST['feed_input_price'];
$weight = $_POST['feed_input_unit_weight'];
$comments = escapehtml($_POST['feed_input_comments']);

$sqlDate = $year."-".$mth."-".$day;

$dbcon->beginTransaction();
$sql = "insert into feed_purchase(purch_date, type, subtype, animal_group, ".
       "vendor, unit, purchased, price_unit, weight_unit, comments) values ".
       "(:dt, :type, :subtype, :for, :vendor, :unit, :purch, :price, ".
       ":weight, :comments)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':dt', $sqlDate, PDO::PARAM_STR);
   $stmt->bindParam(':type', $type, PDO::PARAM_STR);
   $stmt->bindParam(':subtype', $subtype, PDO::PARAM_STR);
   $stmt->bindParam(':for', $for, PDO::PARAM_STR);
   $stmt->bindParam(':vendor', $vendor, PDO::PARAM_STR);
   $stmt->bindParam(':unit', $unit, PDO::PARAM_STR);
   $stmt->bindParam(':purch', $purch, PDO::PARAM_STR);
   $stmt->bindParam(':price', $price, PDO::PARAM_STR);
   $stmt->bindParam(':weight', $weight, PDO::PARAM_STR);
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
