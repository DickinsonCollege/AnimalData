<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$forage = escapehtml($_POST['edit_forage_forage']);
$newforage = strtoupper(escapehtml($_POST['edit_forage_newforage']));
$density = $_POST['edit_forage_density'];
$active = $_POST['edit_forage_active'];

$dbcon->beginTransaction();
if ($newforage == "") {
   $newforage = $forage;
}
$sql = "update forage set forage=:newforage, active=:active, density=:density ".
       "where forage=:forage";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':forage', $forage, PDO::PARAM_STR);
   $stmt->bindParam(':newforage', $newforage, PDO::PARAM_STR);
   $stmt->bindParam(':active', $active, PDO::PARAM_INT);
   $stmt->bindParam(':density', $density, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
