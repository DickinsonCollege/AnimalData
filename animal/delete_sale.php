<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$id = $_GET['id'];

$dbcon->beginTransaction();

$sql = "select animal_id from sale where id=".$id;
$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   $an_id = $row['animal_id'];
} else {
   echo "Error: no such sale record!";
   $dbcon->rollBack();
   die();
}

$sql = "delete from sale where id=:id";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':id', $id, PDO::PARAM_INT);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$sql = "update animal set alive=1 where animal_id = :anid";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':anid', $an_id, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
