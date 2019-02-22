<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$id = $_GET['id'];

$sql = "select filename from notes where id = ".$id;
$res = $dbcon->query($sql);
$file = "";
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   $file = $row['filename'];
}
if ($file != "") {
   unlink($file);
}

$dbcon->beginTransaction();
$sql = "delete from notes where id=:id";
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
