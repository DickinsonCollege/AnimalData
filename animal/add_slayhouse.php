<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$house = strtoupper(escapehtml($_POST['add_animal_slay']));

if ($house == "") {
   echo "Error: enter a slaughter house.";
   die();
}

$dbcon->beginTransaction();
$sql = "insert into slay_house(slay_house, active) values (:house, 1)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':house', $house, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
