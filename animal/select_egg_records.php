<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$date = escapehtml($_GET['date']);
$edit = $_GET['edit'];

$sql = "select * from egg_log where coll_date = '".$date."'";

echo "<div class='tablediv'>";
echo "<table border data-role='table' class='ui-responsive'>";
echo "<thead><tr><th>Date</th><th>Eggs Collected</th><th>Comments</th>";
if ($edit == "true") {
   echo "<th>Edit</th>";
} else {
   echo "<th>Delete</th>";
}
echo "</tr></thead>";
$res = $dbcon->query($sql);
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo "<tr><td>";
   echo humanDate($row['coll_date']);
   echo "</td><td>";
   $num = number_format((float) $row['number'], 0, '.', '');
   echo $num;
   echo "</td><td>";
   echo $row['comments'];
   echo "</td><td>";
   if ($edit == "true") {
      echo "<a href='#egg_edit' class='ui-btn' onclick='egg_edit_init(".
            $row['id'].");'>Edit</a>";
   } else {
      echo "<a href='#egg_delete' class='ui-btn' onclick='egg_delete(".
            $row['id'].");'>Delete</a>";
   }
   echo "</td><td>";
   echo "</tr>";
}
echo "</table>";
echo "</div>";
?>
