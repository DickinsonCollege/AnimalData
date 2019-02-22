<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$numrows = $_POST['update_subgroups_rows'];

$dbcon->beginTransaction();
$sql = "update animal set sub_group=:sg where id=:id";
try {
   $stmt = $dbcon->prepare($sql);

   for ($i = 1; $i <= $numrows; $i++) {
      $id = $_POST['sg_edit_id'.$i];
      $sg = $_POST['sg_edit'.$i];
      $stmt->bindParam(':sg', $sg, PDO::PARAM_STR);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
   }
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}
$dbcon->commit();
echo "success!";
?>
