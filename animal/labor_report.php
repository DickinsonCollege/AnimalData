<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$task = escapehtml($_POST['labor_report_task']);
$group = escapehtml($_POST['labor_report_group']);
$subgroup = escapehtml($_POST['labor_report_subgroup']);
$fyear = $_POST['labor_report_from_year'];
$fday = $_POST['labor_report_from_day'];
$fmth = $_POST['labor_report_from_month'];
$tyear = $_POST['labor_report_to_year'];
$tday = $_POST['labor_report_to_day'];
$tmth = $_POST['labor_report_to_month'];

$sqlFrom = $fyear."-".$fmth."-".$fday;
$sqlTo = $tyear."-".$tmth."-".$tday;

$sql = "select task_entry.*, (workers * minutes) / 60 as hours, list_date".
       " from task_master, task_entry ".
       "where task_entry.m_id = task_master.id and animal_group like '".
       $group."' and sub_group like '".$subgroup."' and task like '".
       $task."' and list_date between '".$sqlFrom."' and '".$sqlTo."'";
$sqlDown = $sql;

echo "<div id = 'labor_report_scroll'>&nbsp;</div>";

$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo "<h2>Labor Report</h2>";
   echo "<div class='tablediv'>";
   echo "<table border data-role='table' class='ui-responsive'>";
   echo "<thead><tr><th>Date</th><th>Task</th><th>Comments</th>".
        "<th>Animal Group</th><th>Subgroup</th><th>Hours</th><th>Complete</th>";
   if ($_SESSION['admin']) {
      echo "<th>User</th><th>Edit</th><th>Delete</th>";
   }
   echo "</tr></thead><tbody>";
   $tot = 0;
   do {
      echo "<tr><td>";
      echo humanDate($row['list_date']);
      echo "</td><td>";
      echo $row['task'];
      echo "</td><td>";
      echo $row['comments'];
      echo "</td><td>";
      echo $row['animal_group'];
      echo "</td><td>";
      echo $row['sub_group'];
      echo "</td><td>";
      if ($row['complete'] == 1) {
         $tot += $row['hours'];
      }
      $hours = number_format((float) $row['hours'], 2, '.', '');
      echo $hours;
      echo "</td><td>";
      if ($row['complete'] == 1) {
         echo "Yes";
      } else {
         echo "No";
      }
      echo "</td>";
      if ($_SESSION['admin']) {
         echo "<td>";
      echo $row['userid'];
      echo "</td><td>";
         echo "<a href='#labor_edit' class='ui-btn' onclick='labor_edit_init(".
            $row['id'].");'>Edit</a>";
         echo "</td><td>";
         echo "<a href='#labor_delete' class='ui-btn' onclick='labor_delete(".
            $row['id'].");'>Delete</a>";
         echo "</td>";
      }
      echo "</tr>";
   } while ($row = $res->fetch(PDO::FETCH_ASSOC));
   echo "</tbody></table>";
   echo "</div>";
   echo "<div>&nbsp;</div>";
   echo "<h2>Total Hours Completed: ".number_format((float) $tot, 2, '.', '')."</h2>";
   if ($task == "%") {
      $sql = "select task, sum((workers * minutes) / 60) as hours".
             " from task_master, task_entry ".
             "where task_entry.m_id = task_master.id and animal_group like '".
             $group."' and sub_group like '".$subgroup."' and task like '".
             $task."' and list_date between '".$sqlFrom."' and '".$sqlTo."' ".
             "and complete = 1 group by task";
      echo "<h2>Total Hours by Task</h2>";
      echo "<table border class='ui-responsive'><thead><tr><th>Task</th>";
      echo "<th>Total Hours</th></thead></tbody>";
      $res = $dbcon->query($sql);
      while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
         echo "<tr><td>".$row['task']."</td><td>".
         number_format((float) $row['hours'], 2, ".", "")."</td></tr>";
      }
      echo "</tbody></table>";
      echo "<div>&nbsp;</div>";
   }
   if ($group == "%") {
      $sql = "select animal_group, sum((workers * minutes) / 60) as hours".
             " from task_master, task_entry ".
             "where task_entry.m_id = task_master.id and animal_group like '".
             $group."' and sub_group like '".$subgroup."' and task like '".
             $task."' and list_date between '".$sqlFrom."' and '".$sqlTo."' ".
             "and complete = 1 group by animal_group";
      echo "<h2>Total Hours by Animal Group</h2>";
      echo "<table border class='ui-responsive'><thead><tr><th>Animal Group</th>";
      echo "<th>Total Hours</th></thead></tbody>";
      $res = $dbcon->query($sql);
      while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
         echo "<tr><td>".$row['animal_group']."</td><td>".
         number_format((float) $row['hours'], 2, ".", "")."</td></tr>";
      }
      echo "</tbody></table>";
      echo "<div>&nbsp;</div>";
   }
/*
   echo "<table border class='ui-responsive'><thead><tr><th>Total</th>";
   echo "<th>Male</th><th>Female</th>";
   if ($tnum > 0) {
      echo "<th>Weight (total lbs.)</th><th>Weight (average lbs.)</th>";
   }
   echo "</tr></thead>";
   echo "<tr><td>".$tot."</td><td>".$male."</td><td>".$female."</td>";
   if ($tnum > 0) {
      echo "<td>".$twght."</td><td>".($twght / $tnum)."</td>";
   }
   echo "</tr>";
   echo "</table>";
   echo "<div>&nbsp;</div>";
*/

   echo "<form name='form' method='POST' action='/animal/down.php'>";
   echo "<input type = \"hidden\" name = \"query\" value = \"".
      escapehtml($sqlDown)."\">";
   echo '<input type="submit" name="submit" value="Download Report"'.
        ' id="labor_report_download" class="ui-btn" style="width:100%">';
   echo "</form>";
} else {
   echo "<h2>No labor records match specified parameters.</h2>";
}
echo "<script>";
echo "$('html,body').animate({scrollTop: $('#labor_report_scroll').offset().top });";
echo "</script>";
?>
