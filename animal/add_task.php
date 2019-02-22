<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$task = strtoupper(escapehtml($_POST['add_task_task']));

if ($task == "") {
   echo "Error: enter a task.";
   die();
}

$dbcon->beginTransaction();
$sql = "insert into task(task, active) values (:task, 1)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':task', $task, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
