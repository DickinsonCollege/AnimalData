<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$autoId = $_POST['labor_edit_auto_id'];
$origDate = $_POST['labor_edit_origdate'];
$mth = $_POST['labor_edit_date_month'];
if ($mth < 10) {
   $mth = "0".$mth;
}
$day = $_POST['labor_edit_date_day'];
if ($day < 10) {
   $day = "0".$day;
}
$year = $_POST['labor_edit_date_year'];
$task = escapehtml($_POST['labor_edit_task']);
$comments = escapehtml($_POST['labor_edit_comments']);
$group = escapehtml($_POST['labor_edit_group']);
if ($group == '%') {
   $group = 'ALL';
}
$subgroup = escapehtml($_POST['labor_edit_subgroup']);
if ($subgroup == '%') {
   $subgroup = 'ALL';
}
$workers = $_POST['labor_edit_workers'];
$minutes = $_POST['labor_edit_minutes'];
$complete = 0;
if (isset($_POST['labor_edit_complete'])) {
   $complete = 1;
}


$sqlDate = $year."-".$mth."-".$day;

$dbcon->beginTransaction();
if ($sqlDate != $origDate) {
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
   $sql = "update task_entry set m_id=:id where id = :autoId";
   try {
      $stmt = $dbcon->prepare($sql);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->bindParam(':autoId', $autoId, PDO::PARAM_INT);
      $stmt->execute();
   } catch (PDOException $p) {
      echo $p->getMessage();
      $dbcon->rollBack();
      die();
   }
}

$sql = "update task_entry set task=:task, comments=:comments, ".
       "animal_group=:group, sub_group=:subgroup, workers=:workers, ".
       "minutes=:minutes, complete=:complete where id=:autoId";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':autoId', $autoId, PDO::PARAM_INT);
   $stmt->bindParam(':task', $task, PDO::PARAM_STR);
   $stmt->bindParam(':comments', $comments, PDO::PARAM_STR);
   $stmt->bindParam(':group', $group, PDO::PARAM_STR);
   $stmt->bindParam(':subgroup', $subgroup, PDO::PARAM_STR);
   $stmt->bindParam(':workers', $workers, PDO::PARAM_INT);
   $stmt->bindParam(':minutes', $minutes, PDO::PARAM_INT);
   $stmt->bindParam(':complete', $complete, PDO::PARAM_INT);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
