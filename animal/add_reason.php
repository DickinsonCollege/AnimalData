<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$reason = strtoupper(escapehtml($_POST['reason']));

if ($reason == "") {
   echo "Error: enter a reason for care.";
   die();
}

$dbcon->beginTransaction();
$sql = "insert into reason(reason) values (:reason)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':reason', $reason, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
