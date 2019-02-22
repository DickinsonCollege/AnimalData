<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$reason = escapehtml($_POST['edit_reason_reason']);
$newreason = strtoupper(escapehtml($_POST['edit_reason_newreason']));
$active = $_POST['edit_reason_active'];

$dbcon->beginTransaction();
if ($newreason == "") {
   $newreason = $reason;
}
$sql = "update reason set reason=:newreason, active=:active ".
       "where reason=:reason";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':reason', $reason, PDO::PARAM_STR);
   $stmt->bindParam(':newreason', $newreason, PDO::PARAM_STR);
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
