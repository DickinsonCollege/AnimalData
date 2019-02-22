<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$fyear = $_POST['slay_report_from_year'];
$fday = $_POST['slay_report_from_day'];
$fmth = $_POST['slay_report_from_month'];
$tyear = $_POST['slay_report_to_year'];
$tday = $_POST['slay_report_to_day'];
$tmth = $_POST['slay_report_to_month'];

$sqlFrom = $fyear."-".$fmth."-".$fday;
$sqlTo = $tyear."-".$tmth."-".$tday;

$group = escapehtml($_POST['slay_report_group']);
$house = escapehtml($_POST['slay_report_house']);

$sql = "select slaughter.*, name, animal_group, birthdate, ".
       "DATEDIFF(slay_date, birthdate) as age from slaughter, animal ".
       "where slaughter.animal_id = animal.animal_id and animal_group like '".
       $group."' and slay_house like '".$house."' and slay_date between '".
       $sqlFrom."' and '".$sqlTo."'";

echo "<div id = 'slay_report_scroll'>&nbsp;</div>";
$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo "<h2>Slaughter Report</h2>";
   echo "<div class='tablediv'>";
   echo "<table border data-role='table' class='ui-responsive'>";
   echo "<thead><tr><th>Slaughter Date</th><th>Animal ID</th><th>Name</th>".
        "<th>Animal Group</th><th>Date of Birth</th><th>Sale Tag</th>".
        "<th>Weight)</th><th>Average Gain/Day</th>".
        "<th>Slaughter House</th>".
        "<th>Hauler</th><th>Hauling Equipment<th>Fees</th><th>Comments</th>";
   if ($_SESSION['admin']) {
      echo "<th>Edit</th><th>Delete</th>";
   }
   echo "</tr></thead><tbody>";
   $cnt = 0;
   $tfee = 0;
   $tweight = 0;
   $tprice = 0;
   $tgain = 0;
   do {
      $cnt++;
      $tfee += $row['fees'];
      $tweight += $row['weight'];
      echo "<tr><td>".humanDate($row['slay_date'])."</td><td>".
           $row['animal_id']."</td><td>".$row['name']."<td>".
           $row['animal_group']."</td><td>".
           humanDate($row['birthdate'])."</td><td>".$row['sale_tag'].
           "</td><td>".$row['weight']." lbs. (".$row['estimated'].")</td><td>";
      $gain = number_format((float) $row['weight'] / $row['age'], 2, '.', '');
      $tgain += $gain;
      echo $gain." lbs./day</td><td>".
           $row['slay_house']."</td><td>".$row['hauler']."</td><td>".
           $row['haul_equip']."</td><td>$".$row['fees']."</td><td>".
           $row['comments']."</td>";
      if ($_SESSION['admin']) {
         echo "<td>";
         echo "<a href='#slay_edit' class='ui-btn' onclick='slay_edit_init(".
         $row['id'].");'>Edit</a>";
         echo "</td><td>";
         echo "<a href='#slay_delete' class='ui-btn' onclick='slay_delete(".
         $row['id'].");'>Delete</a>";
         echo "</td>";
      }
      echo "</tr>";
   } while ($row = $res->fetch(PDO::FETCH_ASSOC));
   echo "</tbody></table></div>";
   echo "<div>&nbsp;</div>";

   echo "<h2>Summary</h2>";
   echo "<div class='tablediv'>";
   echo "<table border data-role='table' class='ui-responsive'>";
   echo "<thead><tr><th>Animals Slaughtered</th><th>Total Fees</th>";
   if ($group != "%") {
      echo "<th>Average Weight</th><th>Average Gain</th>";
   }
   echo "</tr></thead><tbody>";
   echo "<tr><td>".$cnt."</td><td>$".number_format((float) $tfee, 2, ".", "").
        "</td>";
   if ($group != "%") {
      echo "<td>".number_format((float) $tweight/$cnt, 2, ".", "").
           " lbs.</td><td>".
           number_format((float) $tgain/$cnt, 2, ".", "")." lbs./day</td>";
   }
   echo "</tr>";
   echo "</tbody></table></div>";

   echo "<div>&nbsp;</div>";
   echo "<form name='form' method='POST' action='/animal/down.php'>";
   echo "<input type = \"hidden\" name = \"query\" value = \"".
      escapehtml($sql)."\">";
   echo '<input type="submit" name="submit" value="Download Report"'.
        ' id="slay_report_download" class="ui-btn" style="width:100%">';
   echo "</form>";
} else {
  echo "<h2>No slaughter records match specified parameters.</h2>";
}
echo "<script>";
echo "$('html,body').animate({scrollTop: $('#slay_report_scroll').offset().top });";
echo "</script>";

?>
