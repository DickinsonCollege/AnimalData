<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$id = strtoupper(escapehtml($_POST['add_paddock_id']));
$forage = strtoupper(escapehtml($_POST['add_paddock_forage']));
$size = $_POST['add_paddock_size'];

$dbcon->beginTransaction();
$sql = "insert into paddock(paddock_id, size, forage) values (:id, :size, ".
       ":forage)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':forage', $forage, PDO::PARAM_STR);
   $stmt->bindParam(':size', $size, PDO::PARAM_STR);
   $stmt->bindParam(':id', $id, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
