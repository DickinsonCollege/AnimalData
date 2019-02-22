<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$group = escapehtml($_GET['group']);
$subgroup = escapehtml($_GET['subgroup']);

$sql = "select active from sub_group where animal_group like '".$group.
       "' and sub_group like '".$subgroup."'";
$result = $dbcon->query($sql);
$con = "1";
if ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con = $row['active'];
}

echo $con;

?>
