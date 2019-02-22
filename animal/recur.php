<?php
$host = "localhost";
$user = 'critter';
$pass = 'critterpass';
$dbName = 'critterdb';
$dbcon = new PDO("mysql:host=".$host.";dbname=".$dbName, $user, $pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET sql_mode="TRADITIONAL"'));
$dbcon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$dbcon->query("SET SESSION sql_mode = 'ALLOW_INVALID_DATES'");

function insert($row, $today) {
   global $dbcon;
   $sql = "select * from task_master where list_date = '".$today."'";
   $result = $dbcon->query($sql);
   if ($irow = $result->fetch(PDO::FETCH_ASSOC)) {
      $id = $irow['id'];
   } else {
      $sql = "insert into task_master(list_date) values(:dt)";
      try {
         $stmt = $dbcon->prepare($sql);
         $stmt->bindParam(':dt', $today, PDO::PARAM_STR);
         $stmt->execute();
      } catch (PDOException $p) {
         echo $p->getMessage();
         $dbcon->rollBack();
         die();
      }
      $id = $dbcon->lastInsertId();
   }

   $sql = "insert into task_entry(m_id, task, comments, animal_group, sub_group, ".
          "workers, minutes, userid, complete) values(:id, :task, :comments, ".
          ":group, :subgroup, :workers, :minutes, :user, 0)";
   try {
      $stmt = $dbcon->prepare($sql);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $task = $row['task'];
      $group = $row['animal_group'];
      $subgroup = $row['sub_group'];
      $comment = $row['comments'];
      $workers = $row['workers'];
      $minutes = $row['minutes'];
      $user = $row['userid'];
      $stmt->bindParam(':task', $task, PDO::PARAM_STR);
      $stmt->bindParam(':group', $group, PDO::PARAM_STR);
      $stmt->bindParam(':subgroup', $subgroup, PDO::PARAM_STR);
      $stmt->bindParam(':comments', $comment, PDO::PARAM_STR);
      $stmt->bindParam(':user', $user, PDO::PARAM_STR);
      $stmt->bindParam(':workers', $workers, PDO::PARAM_INT);
      $stmt->bindParam(':minutes', $minutes, PDO::PARAM_INT);
      $stmt->execute();
   } catch (PDOException $p) {
      echo $p->getMessage();
      $dbcon->rollBack();
      die();
   }
}

$dbcon->beginTransaction();

$today = new DateTime();
$fToday = $today->format('Y-m-d');
$sql = "select * from task_recurring";
$res = $dbcon->query($sql);
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   $date = new DateTime($row['start_date']);
   if ($today >= $date) {
      if ($row['recur'] == 'DAILY') {
         insert($row, $fToday);
      } else if ($row['recur'] == 'MONTHLY') {
         if ($today->format('d') == $date->format('d') ||
             ($today->format('d') == $today->format('t') && 
              $date->format('d') > $today->format('d'))) {
            insert($row, $fToday);
         }
      } else {
         if ($row['recur'] == 'WEEKLY' ) {
            $intv = 7;
         } else if ($row['recur'] == 'BIWEEKLY') {
            $intv = 14;
         } else {
            echo "Error: invalid recurrence interval!";
            die();
         }
         $diff = $today->diff($date)->days;
         if ($diff % $intv == 0) {
            insert($row, $fToday);
         }
      }
   }
}

$dbcon->commit();
?>
