<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$id = $_GET['id'];

$dbcon->beginTransaction();
$sql = "delete from move where id=:id";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':id', $id, PDO::PARAM_INT);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}
$dbcon->commit();
echo "success!";
?>
