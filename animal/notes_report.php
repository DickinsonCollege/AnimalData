<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$fyear = $_POST['notes_report_from_year'];
$fday = $_POST['notes_report_from_day'];
$fmth = $_POST['notes_report_from_month'];
$tyear = $_POST['notes_report_to_year'];
$tday = $_POST['notes_report_to_day'];
$tmth = $_POST['notes_report_to_month'];

$sqlFrom = $fyear."-".$fmth."-".$fday;
$sqlTo = $tyear."-".$tmth."-".$tday;

$sql = "select * from notes where note_date between '".
       $sqlFrom."' and '".$sqlTo."'";

echo "<div id = 'notes_report_scroll'>&nbsp;</div>";
$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo "<h2>Notes Report</h2>";
   echo "<div class='tablediv'>";
   echo "<table border data-role='table' class='ui-responsive'>";
   echo "<thead><tr><th>Date</th><th>Note</th><th>User</th><th>Picture</th>";
   if ($_SESSION['admin']) {
      echo "<th>Edit</th><th>Delete</th>";
   }
   echo "</tr></thead><tbody>";
   do {
      echo "<tr><td>".humanDate($row['note_date'])."</td><td>".$row['note'].
           "</td><td>".$row['userid']."<td>";
      $filename = $row['filename'];
      if ($filename == "") {
         echo "&nbsp;";
      } else {
         $width = "100";
         $pos = strrpos($filename, ".");
         $ext = substr($filename, $pos + 1);
         echo '<img style="width:'.$width.'px" src="'.$filename.'"/>';
      }
      echo "</td>";
      if ($_SESSION['admin']) {
         echo "<td>";
         echo "<a href='#notes_edit' class='ui-btn' onclick='notes_edit_init(".
         $row['id'].");'>Edit</a>";
         echo "</td><td>";
         echo "<a href='#notes_delete' class='ui-btn' onclick='notes_delete(".
         $row['id'].");'>Delete</a>";
         echo "</td>";
      }
      echo "</tr>";
   } while ($row = $res->fetch(PDO::FETCH_ASSOC));
   echo "</tbody></table>";
   echo "<div>&nbsp;</div>";
   echo "<div>&nbsp;</div>";
   echo "<form name='form' method='POST' action='/animal/down.php'>";
   echo "<input type = \"hidden\" name = \"query\" value = \"".
      escapehtml($sql)."\">";
   echo '<input type="submit" name="submit" value="Download Report"'.
        ' id="notes_report_download" class="ui-btn" style="width:100%">';
   echo "</form>";
} else {
  echo "<h2>No notes match specified parameters.</h2>";
}
echo "<script>";
echo "$('html,body').animate({scrollTop: $('#notes_report_scroll').offset().top });";
echo "</script>";

?>
