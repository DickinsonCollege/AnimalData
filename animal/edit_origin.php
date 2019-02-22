<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$origin = escapehtml($_POST['edit_origin_origin']);
$neworigin = strtoupper(escapehtml($_POST['edit_origin_neworigin']));
$active = $_POST['edit_origin_active'];

$dbcon->beginTransaction();
if ($neworigin == "") {
   $neworigin = $origin;
}
$sql = "update origin set origin=:neworigin, active=:active ".
       "where origin=:origin";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':origin', $origin, PDO::PARAM_STR);
   $stmt->bindParam(':neworigin', $neworigin, PDO::PARAM_STR);
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
