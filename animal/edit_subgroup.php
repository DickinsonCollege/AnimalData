<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$group = escapehtml($_POST['edit_subgroup_group']);
$subgroup = escapehtml($_POST['edit_subgroup_subgroup']);
$newsubgroup = strtoupper(escapehtml($_POST['edit_subgroup_newsubgroup']));
$active = $_POST['edit_subgroup_active'];

$dbcon->beginTransaction();
if ($newsubgroup == "") {
   $newsubgroup = $subgroup;
}
$sql = "update sub_group set sub_group=:newsubgroup, active=:active ".
       "where animal_group=:group and sub_group=:subgroup";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':subgroup', $subgroup, PDO::PARAM_STR);
   $stmt->bindParam(':newsubgroup', $newsubgroup, PDO::PARAM_STR);
   $stmt->bindParam(':active', $active, PDO::PARAM_INT);
   $stmt->bindParam(':group', $group, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
