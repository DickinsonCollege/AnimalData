<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$mth = $_POST['add_daily_task_date_month'];
$day = $_POST['add_daily_task_date_day'];
$year = $_POST['add_daily_task_date_year'];
$rows = $_POST['num_daily_task_rows'];

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

$sql = "delete from task_entry where m_id = :id";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':id', $id, PDO::PARAM_INT);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$sql = "insert into task_entry(m_id, task, comments, animal_group, sub_group, ".
       "workers, minutes, userid, complete) values(:id, :task, :comments, ".
       ":group, :subgroup, :workers, :minutes, :user, :complete)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':id', $id, PDO::PARAM_INT);
   for ($i = 1; $i <= $rows; $i++) {
      $task = escapehtml($_POST['daily_task_task_'.$i]);
      if ($task != "") {
         $group = escapehtml($_POST['daily_task_group_'.$i]);
         $subgroup = escapehtml($_POST['daily_task_subgroup_'.$i]);
         $comment = escapehtml($_POST['daily_task_comment_'.$i]);
         $workers = $_POST['daily_task_workers_'.$i];
         $minutes = $_POST['daily_task_minutes_'.$i];
         $user = escapehtml($_POST['daily_task_user_'.$i]);
         if ($user == "") {
            $user = $_SESSION['user'];
         }
         $comp = $_POST['daily_task_complete_'.$i];
         $stmt->bindParam(':task', $task, PDO::PARAM_STR);
         $stmt->bindParam(':group', $group, PDO::PARAM_STR);
         $stmt->bindParam(':subgroup', $subgroup, PDO::PARAM_STR);
         $stmt->bindParam(':comments', $comment, PDO::PARAM_STR);
         $stmt->bindParam(':user', $user, PDO::PARAM_STR);
         $stmt->bindParam(':complete', $comp, PDO::PARAM_INT);
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
