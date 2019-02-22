<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$task = escapehtml($_POST['edit_task_task']);
$newtask = strtoupper(escapehtml($_POST['edit_task_newtask']));
$active = $_POST['edit_task_active'];

$dbcon->beginTransaction();
if ($newtask == "") {
   $newtask = $task;
}
$sql = "update task set task=:newtask, active=:active ".
       "where task=:task";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':task', $task, PDO::PARAM_STR);
   $stmt->bindParam(':newtask', $newtask, PDO::PARAM_STR);
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
