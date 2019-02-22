<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$wormer = strtoupper(escapehtml($_POST['wormer']));

if ($wormer == "") {
   echo "Error: enter a worming product.";
   die();
}

$dbcon->beginTransaction();
$sql = "insert into wormer(wormer) values (:wormer)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':wormer', $wormer, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
