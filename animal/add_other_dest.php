<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$dest = strtoupper(escapehtml($_POST['add_other_dest']));

if ($dest == "") {
   echo "Error: enter a destination.";
   die();
}

$dbcon->beginTransaction();
$sql = "insert into other_dest(destination, active) values (:dest, 1)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':dest', $dest, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
