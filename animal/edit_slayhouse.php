<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$house = escapehtml($_POST['edit_slayhouse_house']);
$newhouse = strtoupper(escapehtml($_POST['edit_slayhouse_newhouse']));
$active = $_POST['edit_slayhouse_active'];

$dbcon->beginTransaction();
if ($newhouse == "") {
   $newhouse = $house;
}
$sql = "update slay_house set slay_house=:newhouse, active=:active ".
       "where slay_house=:house";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':house', $house, PDO::PARAM_STR);
   $stmt->bindParam(':newhouse', $newhouse, PDO::PARAM_STR);
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
