<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$autoId = $_POST['feed_edit_auto_id'];
$mth = $_POST['feed_edit_date_month'];
$day = $_POST['feed_edit_date_day'];
$year = $_POST['feed_edit_date_year'];
$type = escapehtml($_POST['feed_edit_type']);
$subtype = escapehtml($_POST['feed_edit_subtype']);
$for = escapehtml($_POST['feed_edit_group']);
$vendor = escapehtml($_POST['feed_edit_vendor']);
$unit = escapehtml($_POST['feed_edit_unit']);
$purch = $_POST['feed_edit_purchased'];
$price = $_POST['feed_edit_price'];
$weight = $_POST['feed_edit_unit_weight'];
$comments = escapehtml($_POST['feed_edit_comments']);

$sqlDate = $year."-".$mth."-".$day;

$dbcon->beginTransaction();
$sql = "update feed_purchase set purch_date=:dt, type=:type, ".
       "subtype=:subtype, animal_group=:for, vendor=:vendor, ".
       "unit=:unit, purchased=:purch, price_unit=:price, ".
       "weight_unit=:weight, comments=:comments where id=:autoId";
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
