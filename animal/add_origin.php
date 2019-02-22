<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$origin = strtoupper(escapehtml($_POST['add_animal_origin']));

if ($origin == "") {
   echo "Error: enter an origin.";
   die();
}

$dbcon->beginTransaction();
$sql = "insert into origin(origin, active) values (:origin, 1)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':origin', $origin, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
