<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$paddock = escapehtml($_POST['edit_paddock_paddock']);
$newpaddock = strtoupper(escapehtml($_POST['edit_paddock_newpaddock']));
$forage = strtoupper(escapehtml($_POST['edit_paddock_forage']));
$size = $_POST['edit_paddock_size'];
$active = $_POST['edit_paddock_active'];

$dbcon->beginTransaction();
if ($newpaddock == "") {
   $newpaddock = $paddock;
}
$sql = "update paddock set paddock_id=:newpaddock, active=:active, size=:size, ".
       "forage=:forage where paddock_id=:paddock";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':paddock', $paddock, PDO::PARAM_STR);
   $stmt->bindParam(':newpaddock', $newpaddock, PDO::PARAM_STR);
   $stmt->bindParam(':active', $active, PDO::PARAM_INT);
   $stmt->bindParam(':size', $size, PDO::PARAM_STR);
   $stmt->bindParam(':forage', $forage, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
