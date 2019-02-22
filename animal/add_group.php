<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$group = strtoupper(escapehtml($_POST['add_animal_group']));

if ($group == "") {
   echo "Error: enter an animal group.";
   die();
}

$dbcon->beginTransaction();
$sql = "insert into animal_group(animal_group, active) values (:group, 1)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':group', $group, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$sql = "insert into breed(animal_group, breed, active) values ".
       "(:group, 'N/A', 1)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':group', $group, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$sql = "insert into sub_group(animal_group, sub_group, active) values ".
       "(:group, 'N/A', 1)";
try {
   $stmt = $dbcon->prepare($sql);
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
