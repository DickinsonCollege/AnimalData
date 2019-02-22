<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$feed_unit = escapehtml($_POST['edit_feed_unit_unit']);
$newfeed_unit = strtoupper(escapehtml($_POST['edit_feed_unit_newunit']));
$active = $_POST['edit_feed_unit_active'];

$dbcon->beginTransaction();
if ($newfeed_unit == "") {
   $newfeed_unit = $feed_unit;
}
$sql = "update feed_units set unit=:newfeed_unit, active=:active ".
       "where unit=:feed_unit";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':feed_unit', $feed_unit, PDO::PARAM_STR);
   $stmt->bindParam(':newfeed_unit', $newfeed_unit, PDO::PARAM_STR);
   $stmt->bindParam(':active', $active, PDO::PARAM_INT);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
