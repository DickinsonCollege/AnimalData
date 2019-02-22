<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$type = strtoupper(escapehtml($_POST['add_feed_type']));

if ($type == "") {
   echo "Error: enter a major feed type.";
   die();
}

$dbcon->beginTransaction();
$sql = "insert into feed_type(type) values (:type)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':type', $type, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
