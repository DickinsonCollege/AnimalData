<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";


$dbcon->beginTransaction();
$rows = $_POST['num_daily_chores'];

try {

   $sql = "update task_entry set comments=:comment, workers=:workers, ".
       "minutes=:minutes, complete=:comp, userid=:user where id=:id";
   $stmt = $dbcon->prepare($sql);
   for ($i = 0; $i < $rows; $i++) {
      $comment = escapehtml($_POST['daily_chores_comments_'.$i]);
      $workers = $_POST['daily_chores_workers_'.$i];
      $minutes = $_POST['daily_chores_minutes_'.$i];
      $complete = $_POST['daily_chores_complete_'.$i];
      $id = $_POST['daily_chores_id_'.$i];

      $stmt->bindParam(':workers', $workers, PDO::PARAM_INT);
      $stmt->bindParam(':minutes', $minutes, PDO::PARAM_INT);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
      $stmt->bindParam(':user', $_SESSION['user'], PDO::PARAM_STR);
      $comp = 0;
      if ($complete == "on") {
         $comp = 1;
      }
      $stmt->bindParam(':comp', $comp, PDO::PARAM_INT);
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
