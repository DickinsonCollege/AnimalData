<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$dbcon->beginTransaction();

$sql = "delete from task_recurring";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$rows = $_POST['num_recur_task_rows'];
$sql = "insert into task_recurring(start_date, task, comments, animal_group, ".
       "sub_group, workers, minutes, userid, recur) values(:dt, :task, :comments, ".
       ":group, :subgroup, :workers, :minutes, :user, :recur)";
try {
   $stmt = $dbcon->prepare($sql);
   for ($i = 1; $i <= $rows; $i++) {
      $task = escapehtml($_POST['recur_task_task_'.$i]);
      if ($task != "") {
         $group = escapehtml($_POST['recur_task_group_'.$i]);
         $subgroup = escapehtml($_POST['recur_task_subgroup_'.$i]);
         $comment = escapehtml($_POST['recur_task_comment_'.$i]);
         $recur = escapehtml($_POST['recur_task_occurs_'.$i]);
         $year = escapehtml($_POST['recur_task_date_'.$i."_year"]);
         $month = escapehtml($_POST['recur_task_date_'.$i."_month"]);
         $day = escapehtml($_POST['recur_task_date_'.$i."_day"]);
         $sqlDate = $year."-".$month."-".$day;
         $workers = $_POST['recur_task_workers_'.$i];
         $minutes = $_POST['recur_task_minutes_'.$i];
         $user = $_SESSION['user'];
         $stmt->bindParam(':task', $task, PDO::PARAM_STR);
         $stmt->bindParam(':group', $group, PDO::PARAM_STR);
         $stmt->bindParam(':subgroup', $subgroup, PDO::PARAM_STR);
         $stmt->bindParam(':comments', $comment, PDO::PARAM_STR);
         $stmt->bindParam(':user', $user, PDO::PARAM_STR);
         $stmt->bindParam(':dt', $sqlDate, PDO::PARAM_STR);
         $stmt->bindParam(':recur', $recur, PDO::PARAM_STR);
         $stmt->bindParam(':workers', $workers, PDO::PARAM_INT);
         $stmt->bindParam(':minutes', $minutes, PDO::PARAM_INT);
         $stmt->execute();
      }
   }
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
