<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$group = escapehtml($_POST['add_subgroup_group']);
$subgroup = strtoupper(escapehtml($_POST['add_subgroup_subgroup']));

if ($subgroup == "") {
   echo "Error: enter a subgroup.";
   die();
}

$dbcon->beginTransaction();
$sql = "insert into sub_group(animal_group, sub_group, active) values ".
       "(:group, :subgroup, 1)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':group', $group, PDO::PARAM_STR);
   $stmt->bindParam(':subgroup', $subgroup, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}
$dbcon->commit();
echo "success!";
?>
