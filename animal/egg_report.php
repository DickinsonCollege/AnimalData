<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$fyear = $_POST['egg_report_from_year'];
$fday = $_POST['egg_report_from_day'];
$fmth = $_POST['egg_report_from_month'];
$tyear = $_POST['egg_report_to_year'];
$tday = $_POST['egg_report_to_day'];
$tmth = $_POST['egg_report_to_month'];

$sqlFrom = $fyear."-".$fmth."-".$fday;
$sqlTo = $tyear."-".$tmth."-".$tday;

$sql = "select coll_date, sum(number) as number, count(*) as entries, ".
       "group_concat(comments SEPARATOR ';') as comments from egg_log where coll_date between '".
       $sqlFrom."' and '".$sqlTo."' group by coll_date order by coll_date";

echo "<div id = 'egg_report_scroll'>&nbsp;</div>";

$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo "<h2>Egg Log Report</h2>";
   echo "<div class='tablediv'>";
   echo "<table border data-role='table' class='ui-responsive'>";
   echo "<thead><tr><th>Date</th><th>Eggs Collected</th><th>Number of Entries</th><th>Comments</th>";
   if ($_SESSION['admin']) {
      echo "<th>Edit</th><th>Delete</th>";
   }
   echo "</tr></thead>";
   $totEggs = 0;
   $graphData = "[";
   do {
      echo "<tr><td>";
      echo humanDate($row['coll_date']);
      echo "</td><td>";
      $num = number_format((float) $row['number'], 0, '.', '');
      echo $num;
      $totEggs += $num;
      $graphData .= "['".$row['coll_date']." 12:00AM', ".$num."],";
      echo "</td><td>";
      echo $row['entries'];
      echo "</td><td>";
      echo $row['comments'];
      echo "</td>";
      if ($_SESSION['admin']) {
         echo "<td>";
         echo "<a href='#egg_select' class='ui-btn' onclick='egg_select(true, \"".
            $row['coll_date']."\", ".$row['entries'].");'>Edit</a>";
         echo "</td><td>";
         echo "<a href='#egg_select' class='ui-btn' onclick='egg_select(false, \"".
            $row['coll_date']."\", ".$row['entries'].");'>Delete</a>";
         echo "</td>";
      }
      echo "</tr>";
   } while ($row = $res->fetch(PDO::FETCH_ASSOC));
   echo "</table>";
   echo "</div>";
   echo "<div>&nbsp;</div>";
   $graphData = rtrim($graphData, ",")."]";
   echo "<h2>Total Eggs Collected This Period: ".$totEggs.".</h2>";
   echo "<div class='tablediv'>";
   // echo "<div id='egg_chart' style='width:620px'></div>";
   echo "<div id='egg_chart'></div>";
   echo "</div>";
   echo "<div>&nbsp;</div>";
   $sqlRange = "select DATEDIFF(max(coll_date), min(coll_date)) as diff ".
       "from egg_log where coll_date between '".$sqlFrom."' and '".$sqlTo."'";
   $res = $dbcon->query($sqlRange);
   if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
      $diff = $row['diff'];
   } else {
      $diff = 1;
   }
   $interval = (int) ($diff/20) + 1;
   $ticks = 20;
   if ($diff < 20) {
      $ticks = $diff;
   }
   echo "<script>";
echo "
var plot;
$(document).ready(function(){
   plot = linePlot('egg_chart', 'Daily Egg Production', 'Eggs', ".$graphData.", ".$ticks.");
});";
   echo "</script>";
   echo "<input type='button' class='ui-btn' style='width:100%', onclick='replot(plot);'".
        " value='Refresh Graph'>";
   echo "<form name='form' method='POST' action='/animal/down.php'>";
   echo "<input type = \"hidden\" name = \"query\" value = \"".
      escapehtml($sql)."\">";
   echo '<input type="submit" name="submit" value="Download Report"'.
        ' id="egg_report_download" class="ui-btn" style="width:100%">';
   echo "</form>";
} else {
  echo "<h2>No egg log records match specified parameters.</h2>";
} 
echo "<script>";
echo "$('html,body').animate({scrollTop: $('#egg_report_scroll').offset().top });";
echo "</script>";
?>
