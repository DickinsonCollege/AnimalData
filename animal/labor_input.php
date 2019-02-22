<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$mth = $_POST['labor_input_date_month'];
$day = $_POST['labor_input_date_day'];
$year = $_POST['labor_input_date_year'];
$task = escapehtml($_POST['labor_input_task']);
$comments = escapehtml($_POST['labor_input_comments']);
$group = escapehtml($_POST['labor_input_group']);
if ($group == '%') {
   $group = 'ALL';
}
$subgroup = escapehtml($_POST['labor_input_subgroup']);
if ($subgroup == '%') {
   $subgroup = 'ALL';
}
$workers = $_POST['labor_input_workers'];
$minutes = $_POST['labor_input_minutes'];

$sqlDate = $year."-".$mth."-".$day;

$dbcon->beginTransaction();

$sql = "select * from task_master where list_date = '".$sqlDate."'";
$result = $dbcon->query($sql);
if ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $id = $row['id'];
} else {
   $sql = "insert into task_master(list_date) values(:dt)";
   try {
      $stmt = $dbcon->prepare($sql);
      $stmt->bindParam(':dt', $sqlDate, PDO::PARAM_STR);
      $stmt->execute();
   } catch (PDOException $p) {
      echo $p->getMessage();
      $dbcon->rollBack();
      die();
   }
   $id = $dbcon->lastInsertId();
}

$sql = "insert into task_entry(m_id, task, comments, animal_group, ".
       "sub_group, workers, minutes, userid, complete) values (:id, :task, ".
       ":comments, :group, :subgroup, :workers, :minutes, :user, 1)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':id', $id, PDO::PARAM_INT);
   $stmt->bindParam(':task', $task, PDO::PARAM_STR);
   $stmt->bindParam(':comments', $comments, PDO::PARAM_STR);
   $stmt->bindParam(':group', $group, PDO::PARAM_STR);
   $stmt->bindParam(':subgroup', $subgroup, PDO::PARAM_STR);
   $stmt->bindParam(':workers', $workers, PDO::PARAM_INT);
   $stmt->bindParam(':minutes', $minutes, PDO::PARAM_INT);
   $stmt->bindParam(':user', $_SESSION['user'], PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
