<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$wormer = escapehtml($_POST['edit_wormer_wormer']);
$newwormer = strtoupper(escapehtml($_POST['edit_wormer_newwormer']));
$active = $_POST['edit_wormer_active'];

$dbcon->beginTransaction();

if ($newwormer != "") {
   $sql = "update sheep_care set wormer=:newwormer where wormer=:wormer";
   try {
      $stmt = $dbcon->prepare($sql);
      $stmt->bindParam(':wormer', $wormer, PDO::PARAM_STR);
      $stmt->bindParam(':newwormer', $newwormer, PDO::PARAM_STR);
      $stmt->execute();
   } catch (PDOException $p) {
      echo $p->getMessage();
      $dbcon->rollBack();
      die();
   }
}

if ($newwormer == "") {
   $newwormer = $wormer;
}

$sql = "update wormer set wormer=:newwormer, active=:active".
       " where wormer=:wormer";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':wormer', $wormer, PDO::PARAM_STR);
   $stmt->bindParam(':newwormer', $newwormer, PDO::PARAM_STR);
   $stmt->bindParam(':active', $active, PDO::PARAM_INT);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
