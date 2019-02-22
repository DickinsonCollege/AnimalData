<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$date = escapehtml($_GET['date']);

$sql = "select animal_id from animal where (animal_group = 'SHEEP' or ".
       "animal_group = 'GOATS') and alive = 1 and animal_id not in (".
       "select animal_id from ".
       "sheep_care where care_date between DATE_SUB('".$date."', INTERVAL 1 ".
       "DAY) and '".$date."')";

$res = $dbcon->query($sql);
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo "<option value='".$row['animal_id']."'>".$row['animal_id']."</option>";
}
?>
