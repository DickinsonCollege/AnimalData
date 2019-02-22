<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$feed_type = escapehtml($_POST['edit_feed_subtype_type']);
$feed_subtype = escapehtml($_POST['edit_feed_subtype_subtype']);
$newfeed_subtype = strtoupper(escapehtml($_POST['edit_feed_subtype_newfeed_subtype']));
$active = $_POST['edit_feed_subtype_active'];

$dbcon->beginTransaction();

if ($newfeed_subtype == "") {
   $newfeed_subtype = $feed_subtype;
}
$sql = "update feed_subtype set active=:active, subtype=:newsubtype ".
       "where type=:feed_type and subtype=:subtype";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':feed_type', $feed_type, PDO::PARAM_STR);
   $stmt->bindParam(':newsubtype', $newfeed_subtype, PDO::PARAM_STR);
   $stmt->bindParam(':subtype', $feed_subtype, PDO::PARAM_STR);
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
