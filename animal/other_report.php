<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$fyear = $_POST['other_report_from_year'];
$fday = $_POST['other_report_from_day'];
$fmth = $_POST['other_report_from_month'];
$tyear = $_POST['other_report_to_year'];
$tday = $_POST['other_report_to_day'];
$tmth = $_POST['other_report_to_month'];

$sqlFrom = $fyear."-".$fmth."-".$fday;
$sqlTo = $tyear."-".$tmth."-".$tday;

$group = escapehtml($_POST['other_report_group']);
$reason = escapehtml($_POST['other_report_reason']);

$sql = "select other_remove.*, name, animal_group, birthdate ".
       "from other_remove, animal where other_remove.animal_id = ".
       "animal.animal_id and animal_group like '".$group."' and reason like '".
       $reason."' and remove_date between '".$sqlFrom."' and '".$sqlTo."'";

echo "<div id = 'other_report_scroll'>&nbsp;</div>";
$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo "<h2>Other Removal Report</h2>";
   echo "<div class='tablediv'>";
   echo "<table border data-role='table' class='ui-responsive'>";
   echo "<thead><tr><th>Removal Date</th><th>Animal ID</th><th>Name</th>".
        "<th>Animal Group</th><th>Date of Birth</th><th>Reason</th>".
        "<th>Destination</th><th>Weight</th><th>Comments</th>";
   if ($_SESSION['admin']) {
      echo "<th>Edit</th><th>Delete</th>";
   }
   echo "</tr></thead><tbody>";
   $cnt = 0;
   do {
      $cnt++;
      echo "<tr><td>".humanDate($row['remove_date'])."</td><td>".
           $row['animal_id']."</td><td>".$row['name']."<td>".
           $row['animal_group'].  "</td><td>".humanDate($row['birthdate']).
           "</td><td>".$row['reason']."</td><td>".$row['destination'].
           "</td><td>".$row['weight'].  " lbs.</td><td>".$row['comments']."</td>";
      if ($_SESSION['admin']) {
         echo "<td>";
         echo "<a href='#other_edit' class='ui-btn' onclick='other_edit_init(".
         $row['id'].");'>Edit</a>";
         echo "</td><td>";
         echo "<a href='#other_delete' class='ui-btn' onclick='other_delete(".
         $row['id'].");'>Delete</a>";
         echo "</td>";
      }
      echo "</tr>";
   } while ($row = $res->fetch(PDO::FETCH_ASSOC));
   echo "</tbody></table></div>";
   echo "<div>&nbsp;</div>";
   echo "<h2>".$cnt." Animals Removed</h2>";
   echo "<div>&nbsp;</div>";
   echo "<form name='form' method='POST' action='/animal/down.php'>";
   echo "<input type = \"hidden\" name = \"query\" value = \"".
      escapehtml($sql)."\">";
   echo '<input type="submit" name="submit" value="Download Report"'.
        ' id="other_report_download" class="ui-btn" style="width:100%">';
   echo "</form>";
} else {
  echo "<h2>No removal records match specified parameters.</h2>";
}
echo "<script>";
echo "$('html,body').animate({scrollTop: $('#other_report_scroll').offset().top });";
echo "</script>";

?>
