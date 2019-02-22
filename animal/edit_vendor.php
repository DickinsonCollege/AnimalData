<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$vendor = escapehtml($_POST['edit_vendor_vendor']);
$newvendor = strtoupper(escapehtml($_POST['edit_vendor_newvendor']));
$active = $_POST['edit_vendor_active'];

$dbcon->beginTransaction();
if ($newvendor == "") {
   $newvendor = $vendor;
}
$sql = "update vendor set vendor=:newvendor, active=:active ".
       "where vendor=:vendor";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':vendor', $vendor, PDO::PARAM_STR);
   $stmt->bindParam(':newvendor', $newvendor, PDO::PARAM_STR);
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
