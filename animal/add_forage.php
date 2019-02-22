<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$forage = strtoupper(escapehtml($_POST['add_forage_forage']));
$density = $_POST['add_forage_density'];

$dbcon->beginTransaction();
$sql = "insert into forage(forage, density) values (:forage, :density)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':forage', $forage, PDO::PARAM_STR);
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
