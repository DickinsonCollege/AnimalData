<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$arr = array();

$sql = "select * from task_recurring";
$result = $dbcon->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   array_push($arr, $row);
}

echo json_encode($arr);
?>
