<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$feed_unit = strtoupper(escapehtml($_POST['add_feed_unit_unit']));

if ($feed_unit == "") {
   echo "Error: enter a feed unit.";
   die();
}

$dbcon->beginTransaction();
$sql = "insert into feed_units(unit) values (:feed_unit)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':feed_unit', $feed_unit, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
