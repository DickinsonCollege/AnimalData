<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$dt = escapehtml($_GET['date']);

$arr = array();

$sql = "select id from task_master where list_date = '".$dt."'";
$result = $dbcon->query($sql);
if ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $id = $row['id'];
   $sql = "select * from task_entry where m_id = ".$id." order by complete";
   $result = $dbcon->query($sql);
   while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
      array_push($arr, $row);
   }
}

echo json_encode($arr);
?>
