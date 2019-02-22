<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$id = escapehtml($_POST['vet_report_id']);
$fyear = $_POST['vet_date_from_year'];
$fday = $_POST['vet_date_from_day'];
$fmth = $_POST['vet_date_from_month'];
$tyear = $_POST['vet_date_to_year'];
$tday = $_POST['vet_date_to_day'];
$tmth = $_POST['vet_date_to_month'];
$med = escapehtml($_POST['vet_report_med']);
$reason = escapehtml($_POST['vet_report_reason']);

$sqlFrom = $fyear."-".$fmth."-".$fday;
$sqlTo = $tyear."-".$tmth."-".$tday;

$sql = "select * from vet";
if ($med != "%") {
   $sql .= ", medication, meds_given";
}
$sql .= " where animal_id like '".$id."' and reason like '".$reason."' ".
        "and care_date between '".$sqlFrom."' and '".$sqlTo."' ";
if ($med != "%") {
   $sql .= "and meds_given.id = vet.id and meds_given.medication = ".
           "medication.medication and medication.medication like '".$med."'";
}
$sql .= " order by care_date";

$sqlDown = "select * from vet";
$sqlDown .= " left outer join (select meds_given.*, medication.dosage".
        " from medication, meds_given where".
        " medication.medication = meds_given.medication) as meds on (".
        "vet.id = meds.id and meds.medication like '".$med."')";
$sqlDown .= " where animal_id like '".$id."' and reason like '".$reason."' ".
        "and care_date between '".$sqlFrom."' and '".$sqlTo."' ";


echo "<div id = 'vet_report_scroll'>&nbsp;</div>";

$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo "<h2>Vet Report</h2>";
   echo "<div class='tablediv'>";
   echo "<table border data-role='table' class='ui-responsive'>";
   echo "<thead><tr><th>Date</th><th>Animal ID</th><th>Reason for Care</th>";
   echo "<th>Symptoms</th><th>Temperature</th><th>Care Given</th>";
   echo "<th>Estimated Weight</th><th>Vet/Advisor</th><th>Contact</th>";
   echo "<th>Assistants</th><th>Comments</th><th>Medication Given</th>";
   if ($_SESSION['admin']) {
      echo "<th>User</th><th>Edit</th><th>Delete</th>";
   }
   echo "</tr></thead>";
   $totMed = 0;
   do {
      echo "<tr><td>";
      echo humanDate($row['care_date']);
      echo "</td><td>";
      echo $row['animal_id'];
      echo "</td><td>";
      echo $row['reason'];
      echo "</td><td>";
      echo $row['symptoms'];
      echo "</td><td>";
      echo $row['temperature'];
      echo "</td><td>";
      echo $row['care'];
      echo "</td><td>";
      echo $row['weight'];
      echo "</td><td>";
      echo $row['vet'];
      echo "</td><td>";
      echo $row['contact'];
      echo "</td><td>";
      echo $row['assistants'];
      echo "</td><td>";
      echo $row['comments'];
      echo "</td><td>";
      $msql = "select meds_given.*, dosage from meds_given, medication where ".
              "meds_given.medication = medication.medication and id = ".
              $row['id'];
      $mres = $dbcon->query($msql);
      if ($mrow = $mres->fetch(PDO::FETCH_ASSOC)) {
         echo "<table border data-role='table' class='ui-responsive'>";
         echo "<thead><th>Medication</th><th>Dosage</th><th>Given</th>".
              "</thead><tbody>";
         do {
            $given = number_format((float) $mrow['units_given'], 2, '.', '');
            echo "<tr><td>".$mrow['medication']."</td><td>".$mrow['dosage'].
                 "</td><td>".$given." ".$mrow['units']."</td></tr>";
         } while ($mrow = $mres->fetch(PDO::FETCH_ASSOC));
         echo "</tbody></table>";
      } else {
         echo "None";
      }
      echo "</td>";
      if ($_SESSION['admin']) {
         echo "<td>";
         echo $row['userid'];
         echo "</td><td>";
         echo "<a href='#vet_edit' class='ui-btn' onclick='vet_edit_init(".
            $row['id'].");'>Edit</a>";
         echo "</td><td>";
         echo "<a href='#vet_delete' class='ui-btn' onclick='vet_delete(".
            $row['id'].");'>Delete</a>";
         echo "</td>";
      }
      echo "</tr>";
   } while ($row = $res->fetch(PDO::FETCH_ASSOC));
   echo "</table>";
   echo "</div>";
   echo "<div>&nbsp;</div>";
   if ($med != "%") {
      echo "<h2>Total ".$med." Given</h2>";
      $sql = "select medication, sum(units_given) as given, units ";
      $sql .= "from vet, meds_given where vet.id = meds_given.id and ";
      $sql .= "animal_id like '".$id."' and reason like '".$reason."' ".
              "and care_date between '".$sqlFrom."' and '".$sqlTo."' and ";
      $sql .= "medication like '".$med."' group by medication, units";
      echo "<table border data-role='table' class='ui-responsive'>";
      echo "<thead><th>Medication</th><th>Total Units Given</th>".
           "</thead><tbody>";
      $res = $dbcon->query($sql);
      while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
         $given = number_format((float) $row['given'], 2, '.', '');
         echo "<tr><td>".$row['medication']."</td><td>".$given." ".
               $row['units']."</td></tr>";
      }
      echo "</tbody></table>";
      echo "<div>&nbsp;</div>";
   }
   echo "<form name='form' method='POST' action='/animal/down.php'>";
   echo "<input type = \"hidden\" name = \"query\" value = \"".
      escapehtml($sqlDown)."\">";
   echo '<input type="submit" name="submit" value="Download Report"'.
        ' id="vet_report_download" class="ui-btn" style="width:100%">';
   echo "</form>";
} else {
  echo "<h2>No vet records match specified parameters.</h2>";
} 
echo "<script>";
echo "$('html,body').animate({scrollTop: $('#vet_report_scroll').offset().top });";
echo "</script>";
?>
