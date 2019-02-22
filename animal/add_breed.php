<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$group = escapehtml($_POST['add_breed_group']);
$breed = strtoupper(escapehtml($_POST['add_breed_breed']));

if ($breed == "") {
   echo "Error: enter a breed.";
   die();
}

$dbcon->beginTransaction();
$sql = "insert into breed(animal_group, breed, active) values ".
       "(:group, :breed, 1)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':group', $group, PDO::PARAM_STR);
   $stmt->bindParam(':breed', $breed, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}
$dbcon->commit();
echo "success!";
?>
