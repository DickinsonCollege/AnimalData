<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$type = strtoupper(escapehtml($_POST['add_feed_subtype_type']));
$subtype = strtoupper(escapehtml($_POST['add_feed_subtype_subtype']));

if ($subtype == "") {
   echo "Error: enter feed type details.";
   die();
}

$dbcon->beginTransaction();
$sql = "insert into feed_subtype(type, subtype) values (:type, :subtype)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':type', $type, PDO::PARAM_STR);
   $stmt->bindParam(':subtype', $subtype, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
