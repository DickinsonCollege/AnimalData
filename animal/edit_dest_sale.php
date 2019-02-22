<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$dest = escapehtml($_POST['edit_dest_sale_dest']);
$newdest = strtoupper(escapehtml($_POST['edit_dest_sale_newdest']));
$active = $_POST['edit_dest_sale_active'];

$dbcon->beginTransaction();
if ($newdest == "") {
   $newdest = $dest;
}
$sql = "update destination set destination=:newdest, active=:active ".
       "where destination=:dest";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':dest', $dest, PDO::PARAM_STR);
   $stmt->bindParam(':newdest', $newdest, PDO::PARAM_STR);
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
