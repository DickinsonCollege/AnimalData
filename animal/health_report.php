<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$id = escapehtml($_POST['vet_report_id']);
$fyear = $_POST['herd_health_from_year'];
$fday = $_POST['herd_health_from_day'];
$fmth = $_POST['herd_health_from_month'];
$tyear = $_POST['herd_health_to_year'];
$tday = $_POST['herd_health_to_day'];
$tmth = $_POST['herd_health_to_month'];

$sqlFrom = $fyear."-".$fmth."-".$fday;
$sqlTo = $tyear."-".$tmth."-".$tday;

$sql = "select animal_group, count(*) as cnt, ".
       "group_concat(distinct reason order by reason asc separator ';')".
       " as reas from vet, animal ".
       "where vet.animal_id = animal.animal_id ".
       "and care_date between '".$sqlFrom."' and '".$sqlTo.
       "' group by animal_group order by animal_group";

echo "<div id = 'herd_health_scroll'>&nbsp;</div>";

$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo "<h2>Vet Care Incidents</h2>";
   echo "<div class='tablediv'>";
   echo "<table border data-role='table' class='ui-responsive'>";
   echo "<thead><tr><th>Animal Group</th><th>Number of Incidents</th>";
   echo "<th>Reasons for Care</th></tr></thead>";
   do {
      echo "<tr><td>";
      echo $row['animal_group'];
      echo "</td><td>";
      echo $row['cnt'];
      echo "</td><td>";
      echo $row['reas'];
      echo "</td></tr>";
   } while ($row = $res->fetch(PDO::FETCH_ASSOC));
   echo "</table>";
   echo "</div>";
} else {
   echo "<h2>No Vet Care Incidents</h2>";
}

$sql = "select medication, units, sum(units_given) as sm from vet, meds_given ".
       "where vet.id = meds_given.id ".
       "and care_date between '".$sqlFrom."' and '".$sqlTo.
       "' group by medication, units order by medication, units";

$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo "<h2>Total Medication Given</h2>";
   echo "<div class='tablediv'>";
   echo "<table border data-role='table' class='ui-responsive'>";
   echo "<thead><tr><th>Medication</th><th>Total Units Given</th>";
   echo "</tr></thead>";
   do {
      echo "<tr><td>";
      echo $row['medication'];
      echo "</td><td>";
      echo $row['sm']." ".$row['units'];
      echo "</td></tr>";
   } while ($row = $res->fetch(PDO::FETCH_ASSOC));
   echo "</table>";
   echo "</div>";
} else {
   echo "<h2>No Medication Given</h2>";
}

$eye_chart = "eye_chart_health";
$body_chart = "body_chart_health";
$group = "%";
$crits = "Sheep/Goat";

include $_SERVER['DOCUMENT_ROOT']."/animal/famcha.php";

echo "<script>";
echo "$('html,body').animate({scrollTop: $('#herd_health_scroll').offset().top });";
echo "</script>";
?>
