<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$group = escapehtml($_POST['edit_breed_group']);
$breed = escapehtml($_POST['edit_breed_breed']);
$newbreed = strtoupper(escapehtml($_POST['edit_breed_newbreed']));
$active = $_POST['edit_breed_active'];

$dbcon->beginTransaction();
if ($newbreed == "") {
   $newbreed = $breed;
}
$sql = "update breed set breed=:newbreed, active=:active ".
       "where animal_group=:group and breed=:breed";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':breed', $breed, PDO::PARAM_STR);
   $stmt->bindParam(':newbreed', $newbreed, PDO::PARAM_STR);
   $stmt->bindParam(':active', $active, PDO::PARAM_INT);
   $stmt->bindParam(':group', $group, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
