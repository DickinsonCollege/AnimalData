<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$feed_type = escapehtml($_POST['edit_feed_type_type']);
$newfeed_type = strtoupper(escapehtml($_POST['edit_feed_type_newfeed_type']));
$active = $_POST['edit_feed_type_active'];

$dbcon->beginTransaction();

$sql = "update feed_type set active=:active where type=:feed_type";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':feed_type', $feed_type, PDO::PARAM_STR);
   $stmt->bindParam(':active', $active, PDO::PARAM_INT);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

if ($newfeed_type != "" && $feed_type != $newfeed_type) {
   // START insert into feed_type, update feed_purchase and feed_subtype and 
   // then delete old record from feed_type
   $sql = "insert into feed_type(type, active) values(:type, :active)";
   try {
      $stmt = $dbcon->prepare($sql);
      $stmt->bindParam(':type', $newfeed_type, PDO::PARAM_STR);
      $stmt->bindParam(':active', $active, PDO::PARAM_INT);
      $stmt->execute();
   } catch (PDOException $p) {
      echo $p->getMessage();
      $dbcon->rollBack();
      die();
   }

   $sql = "insert into feed_subtype(type, subtype, active) ".
             "(select :newtype, subtype, :active from feed_subtype ".
             " where type=:type)";
   try {
      $stmt = $dbcon->prepare($sql);
      $stmt->bindParam(':newtype', $newfeed_type, PDO::PARAM_STR);
      $stmt->bindParam(':type', $feed_type, PDO::PARAM_STR);
      $stmt->bindParam(':active', $active, PDO::PARAM_INT);
      $stmt->execute();
   } catch (PDOException $p) {
      echo $p->getMessage();
      $dbcon->rollBack();
      die();
   }

   $sql = "update feed_purchase set type=:newtype where type=:type";
   try {
      $stmt = $dbcon->prepare($sql);
      $stmt->bindParam(':newtype', $newfeed_type, PDO::PARAM_STR);
      $stmt->bindParam(':type', $feed_type, PDO::PARAM_STR);
      $stmt->execute();
   } catch (PDOException $p) {
      echo $p->getMessage();
      $dbcon->rollBack();
      die();
   }

   $sql = "delete from feed_subtype where type=:type";
   try {
      $stmt = $dbcon->prepare($sql);
      $stmt->bindParam(':type', $feed_type, PDO::PARAM_STR);
      $stmt->execute();
   } catch (PDOException $p) {
      echo $p->getMessage();
      $dbcon->rollBack();
      die();
   }

   $sql = "delete from feed_type where type=:type";
   try {
      $stmt = $dbcon->prepare($sql);
      $stmt->bindParam(':type', $feed_type, PDO::PARAM_STR);
      $stmt->execute();
   } catch (PDOException $p) {
      echo $p->getMessage();
      $dbcon->rollBack();
      die();
   }
}

$dbcon->commit();
echo "success!";
?>
