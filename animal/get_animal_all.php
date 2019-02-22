<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$id = escapehtml($_GET['id']);

$sql = "select * from animal where animal_id = '".$id."'";

echo "<div id = 'animal_report_scroll'>&nbsp;</div>";
$group = "";

$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo "<div class='tablediv'>";
   echo "<h2>Basic Animal Data</h2>";
   echo "<table border data-role='table' class='ui-responsive'>";
   echo "<thead><tr><th>Animal ID</th><th>Animal Group</th><th>Breed</th>".
        "<th>Subgroup</th><th>Gender</th>";
   echo "<th>Birthdate</th><th>Origin</th><th>Mother</th><th>Father</th>";
   echo "<th>Name</th><th>Color &amp; Markings</th>".
        "<th>Comments</th><th>Alive/On Farm</th><th>Picture</th>";
   echo "</tr></thead>";
   echo "<tr><td>";
   echo $row['animal_id'];
   echo "</td><td>";
   $group = $row['animal_group'];
   echo $group;
   echo "</td><td>";
   echo $row['breed'];
   echo "</td><td>";
   echo $row['sub_group'];
   echo "</td><td>";
   $gen =  $row['gender'];
   echo $gen;
   echo "</td><td>";
   echo humanDate($row['birthdate']);
   echo "</td><td>";
   echo $row['origin'];
   echo "</td><td>";
   echo $row['mother'];
   echo "</td><td>";
   echo $row['father'];
   echo "</td><td>";
   echo $row['name'];
   echo "</td><td>";
   echo $row['markings'];
   echo "</td><td>";
   echo $row['comments'];
   echo "</td><td>";
   if ($row['alive']) {
      echo "Yes";
   } else {
      echo "No";
   }
   echo "</td><td>";
   $filename = $row['filename'];
   if ($filename == "") {
      echo "&nbsp;";
   } else {
      $width = "100";
      $pos = strrpos($filename, ".");
      $ext = substr($filename, $pos + 1);
      echo '<img style="width:'.$width.'px" src="'.$filename.'"/>';
   }
   echo "</td></tr>";
   echo "</table></div>";

   // offspring
   $sql = "select * from animal where ";
   if ($gen == "F") {
      $sql .= "mother ";
   } else {
      $sql .= "father ";
   }
   $sql .= "= '".$id."' order by animal_id";
   $res = $dbcon->query($sql);
   if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
      echo "<div>&nbsp;</div>";
      echo "<h2>Offspring</h2>";
      echo "<div class='tablediv'>";
      echo "<table border data-role='table' class='ui-responsive'>";
      echo "<thead><tr><th>Birth Date</th><th>Animal ID</th><th>Name</th>";
      echo "<th>Sale/Slaughter Weight</th><th>Comments</th></tr></thead>";
      do {
         echo "<tr><td>";
         echo $row['birthdate'];
         echo "</td><td>";
         echo $row['animal_id'];
         echo "</td><td>";
         echo $row['name'];
         echo "</td><td>";
         $ssql = "select weight, estimated from sale where animal_id = '".
                 $row['animal_id']."'";
         $sres = $dbcon->query($ssql);
         if ($srow = $sres->fetch(PDO::FETCH_ASSOC)) {
            echo $srow['weight']." lbs. (".$srow['estimated'].")";
         } else {
            $ssql = "select weight, estimated from slaughter where animal_id = '".
                    $row['animal_id']."'";
            $sres = $dbcon->query($ssql);
            if ($srow = $sres->fetch(PDO::FETCH_ASSOC)) {
               echo $srow['weight']." lbs. (".$srow['estimated'].")";
            } else {
               echo "&nbsp;";
            }
         }
         echo "</td><td>";
         echo $row['comments'];
         echo "</td></tr>";
      } while ($row = $res->fetch(PDO::FETCH_ASSOC));
      echo "</table></div>";
   } else {
      echo "<h2>No offspring for this animal.</h2>";
   } 

   // vet

   $sql = "select * from vet";
   $sql .= " where animal_id = '".$id."' order by care_date";

   $res = $dbcon->query($sql);
   if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
      echo "<div>&nbsp;</div>";
      echo "<h2>Vet Records</h2>";
      echo "<div class='tablediv'>";
      echo "<table border data-role='table' class='ui-responsive'>";
      echo "<thead><tr><th>Date</th><th>Animal ID</th><th>Reason for Care</th>";
      echo "<th>Symptoms</th><th>Temperature</th><th>Care Given</th>";
      echo "<th>Estimated Weight</th><th>Vet/Advisor</th><th>Contact</th>";
      echo "<th>Assistants</th><th>Comments</th><th>Medication Given</th>";
      echo "</tr></thead>";
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
         echo "</tr>";
      } while ($row = $res->fetch(PDO::FETCH_ASSOC));
      echo "</table>";
      echo "</div>";
      echo "<div>&nbsp;</div>";
   } else {
     echo "<h2>No vet records for this animal.</h2>";
   } 

   // care
   // START - include cattle when cattle care is available.  Use $group to
   // distinguish

   $sql = "select sheep_care.*, name, animal_group from sheep_care, animal".
          " where sheep_care.animal_id = animal.animal_id and ".
          "animal.animal_id = '".$id."' order by care_date";

   $res = $dbcon->query($sql);
   if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
      echo "<h2>Animal Care Records</h2>";
      echo "<div class='tablediv'>";
      echo "<table border data-role='table' class='ui-responsive'>";
      echo "<thead><tr><th>Care Date</th><th>Animal ID</th><th>Name</th>".
           "<th>Animal Group</th><th>Eye (FAMCHA)</th><th>Body Condition</th>".
           "<th>Tail</th><th>Nose</th><th>Coat</th><th>Bottle Jaw</th>".
           "<th>Wormer Given</th><th>Hoof Condition</th><th>Trim?</th>".
           "<th>Weight</th><th>Comments</th>";
      echo "</tr></thead>";
      do {
         echo "<tr><td>";
         echo humanDate($row['care_date']);
         echo "</td><td>";
         echo $row['animal_id'];
         echo "</td><td>";
         echo $row['name'];
         echo "</td><td>";
         echo $row['animal_group'];
         echo "</td><td>";
         switch ($row['eye']) {
            case -1:
               echo "N/A";
               break;
            case 1:
               echo "1: RED";
               break;
            case 2:
               echo "2: RED-PINK";
               break;
            case 3:
               echo "3: PINK";
               break;
            case 4:
               echo "4: PINK-WHITE";
               break;
            case 5:
               echo "5: WHITE";
               break;
            default:
               echo "Error: invalid value";
               break;
         }
         echo "</td><td>";
         switch ($row['body']) {
            case -1:
               echo "N/A";
               break;
            case 1:
               echo "1: VERY THIN";
               break;
            case 2:
               echo "2: THIN";
               break;
            case 3:
               echo "3: IDEAL";
               break;
            case 4:
               echo "4: CHUBBY";
               break;
            case 5:
               echo "5: OBESE";
               break;
            default:
               echo "Error: invalid value";
               break;
         }
         echo "</td><td>";
         echo $row['tail'];
         echo "</td><td>";
         echo $row['nose'];
         echo "</td><td>";
         echo $row['coat'];
         echo "</td><td>";
         switch ($row['jaw']) {
            case 0:
               echo "0: NONE";
               break;
            case 1:
               echo "1: MINOR";
               break;
            case 2:
               echo "2: MAJOR";
               break;
            default:
               echo "Error: invalid value";
               break;
         }
         echo "</td><td>";
         echo $row['wormer'];
         $qnt = $row['wormer_quantity'];
         if ($qnt != "" && $qnt != "N/A") {
            echo " (".$qnt.")";
         }
         echo "</td><td>";
         echo $row['hoof'];
         echo "</td><td>";
         echo $row['trim'];
         echo "</td><td>";
         echo $row['weight']." lbs (".$row['estimated'].")";
         echo "</td><td>";
         echo $row['comments'];
         echo "</td>";
         echo "</tr>";
      } while ($row = $res->fetch(PDO::FETCH_ASSOC));
      echo "</table>";
      echo "</div>";
   } else {
     echo "<h2>No care records for this animal.</h2>";
   } 

   // sale

   $sql = "select sale.*, name, animal_group, birthdate, ".
          "DATEDIFF(sale_date, birthdate) as age from sale, animal ".
          "where sale.animal_id = animal.animal_id and sale.animal_id = '".
          $id."'";
   $res = $dbcon->query($sql);
   if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
      echo "<h2>Sale Record</h2>";
      echo "<div class='tablediv'>";
      echo "<table border data-role='table' class='ui-responsive'>";
      echo "<thead><tr><th>Sale Date</th><th>Animal ID</th><th>Name</th>".
           "<th>Date of Birth</th><th>Sale Tag</th><th>Destination</th>".
           "<th>Weight</th><th>Average Gain/Day</th><th>Price/Lb.</th>".
           "<th>Fees</th><th>Net Return</th><th>Comments</th>";
      echo "</tr></thead><tbody>";
      echo "<tr><td>".humanDate($row['sale_date'])."</td><td>".
           $row['animal_id']."</td><td>".$row['name']."<td>".
           humanDate($row['birthdate'])."</td><td>".$row['sale_tag'].
           "</td><td>".$row['destination']."</td><td>".$row['weight'].
           " lbs. (".$row['estimated'].")</td><td>".number_format((float)
           $row['weight'] / $row['age'], 2, '.', '')." lbs./day</td><td>$".
           $row['price_lb']."</td><td>$".$row['fees']."</td><td>$".
           number_format((float) $row['weight'] * $row['price_lb'] - 
              $row['fees'], 2, '.', '')."</td><td>".$row['comments']."</td>";
      echo "</tr>";
      echo "</tbody></table></div>";
      echo "<div>&nbsp;</div>";
   }

   // slaughter

   $sql = "select slaughter.*, name, animal_group, birthdate, ".
          "DATEDIFF(slay_date, birthdate) as age from slaughter, animal ".
          "where slaughter.animal_id = animal.animal_id and animal.animal_id = '".
          $id."'";
   
   $res = $dbcon->query($sql);
   if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
      echo "<h2>Slaughter Record</h2>";
      echo "<div class='tablediv'>";
      echo "<table border data-role='table' class='ui-responsive'>";
      echo "<thead><tr><th>Slaughter Date</th><th>Animal ID</th><th>Name</th>".
           "<th>Animal Group</th><th>Date of Birth</th><th>Sale Tag</th>".
           "<th>Weight)</th><th>Average Gain/Day</th>".
           "<th>Slaughter House</th>".
           "<th>Hauler</th><th>Hauling Equipment<th>Fees</th><th>Comments</th>";
      echo "</tr></thead><tbody>";
      echo "<tr><td>".humanDate($row['slay_date'])."</td><td>".
           $row['animal_id']."</td><td>".$row['name']."<td>".
           $row['animal_group']."</td><td>".
           humanDate($row['birthdate'])."</td><td>".$row['sale_tag'].
           "</td><td>".$row['weight']." lbs. (".$row['estimated'].")</td><td>";
      $gain = number_format((float) $row['weight'] / $row['age'], 2, '.', '');
      echo $gain." lbs./day</td><td>".
           $row['slay_house']."</td><td>".$row['hauler']."</td><td>".
           $row['haul_equip']."</td><td>$".$row['fees']."</td><td>".
           $row['comments']."</td>";
      echo "</tr>";
      echo "</tbody></table></div>";
      echo "<div>&nbsp;</div>";
   }

} else {
  echo "<h2>No records for this animal.</h2>";
}
echo "<script>";
echo "$('html,body').animate({scrollTop: $('#animal_report_scroll').offset().top });";
echo "</script>";
?>
