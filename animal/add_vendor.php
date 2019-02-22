<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$vendor = strtoupper(escapehtml($_POST['add_vendor_vendor']));

if ($vendor == "") {
   echo "Error: enter a vendor.";
   die();
}

$dbcon->beginTransaction();
$sql = "insert into vendor(vendor) values (:vendor)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':vendor', $vendor, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
