<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$med = escapehtml($_POST['edit_med_med']);
$newmed = strtoupper(escapehtml($_POST['edit_med_newmed']));
$dose = strtoupper(escapehtml($_POST['edit_med_dose']));
$active = $_POST['edit_med_active'];

$dbcon->beginTransaction();
if ($newmed == "") {
   $newmed = $med;
}
$sql = "update medication set medication=:newmed, active=:active, ".
       "dosage=:dose where medication=:med";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':med', $med, PDO::PARAM_STR);
   $stmt->bindParam(':newmed', $newmed, PDO::PARAM_STR);
   $stmt->bindParam(':active', $active, PDO::PARAM_INT);
   $stmt->bindParam(':dose', $dose, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
