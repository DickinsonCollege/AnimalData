<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$fyear = $_POST['sheep_care_report_from_year'];
$fday = $_POST['sheep_care_report_from_day'];
$fmth = $_POST['sheep_care_report_from_month'];
$tyear = $_POST['sheep_care_report_to_year'];
$tday = $_POST['sheep_care_report_to_day'];
$tmth = $_POST['sheep_care_report_to_month'];
$group = escapehtml($_POST['sheep_care_report_group']);

$sqlFrom = $fyear."-".$fmth."-".$fday;
$sqlTo = $tyear."-".$tmth."-".$tday;

$sql = "select sheep_care.*, name, animal_group from sheep_care, animal where ".
       "sheep_care.animal_id = animal.animal_id and care_date between '".
       $sqlFrom."' and '".$sqlTo."' and animal_group like '".$group.
       "' order by care_date";

echo "<div id = 'sheep_care_report_scroll'>&nbsp;</div>";

$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   $crits = $group;
   if ($group == "%") {
       $crits = "Sheep/Goat";
   } else if ($group == "SHEEP") {
       $crits = "Sheep";
   } else if ($group == "GOATS") {
       $crits = "Goat";
   }
   echo "<h2>".$crits." Care Report</h2>";
   echo "<div class='tablediv'>";
   echo "<table border data-role='table' class='ui-responsive'>";
   echo "<thead><tr><th>Care Date</th><th>Animal ID</th><th>Name</th>".
        "<th>Animal Group</th><th>Eye (FAMCHA)</th><th>Body Condition</th>".
        "<th>Tail</th><th>Nose</th><th>Coat</th><th>Bottle Jaw</th>".
        "<th>Wormer Given</th><th>Hoof Condition</th><th>Trim?</th>".
        "<th>Weight</th><th>Comments</th>";
   if ($_SESSION['admin']) {
      echo "<th>Edit</th><th>Delete</th>";
   }
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
      if ($_SESSION['admin']) {
         echo "<td>";
         echo "<a href='#sheep_care_edit' class='ui-btn' onclick='sheep_care_edit_init(".
            $row['id'].");'>Edit</a>";
         echo "</td><td>";
         echo "<a href='#sheep_care_delete' class='ui-btn' onclick='sheep_care_delete(".
            $row['id'].");'>Delete</a>";
         echo "</td>";
      }
      echo "</tr>";
   } while ($row = $res->fetch(PDO::FETCH_ASSOC));
   echo "</table>";
   echo "</div>";
   echo "<div>&nbsp;</div>";
   $eye_chart = "eye_chart";
   $body_chart = "body_chart";

   include $_SERVER['DOCUMENT_ROOT']."/animal/famcha.php";

   echo "<form name='form' method='POST' action='/animal/down.php'>";
   echo "<input type = \"hidden\" name = \"query\" value = \"".
      escapehtml($sql)."\">";
   echo '<input type="submit" name="submit" value="Download Report"'.
        ' id="sheep_care_report_download" class="ui-btn" style="width:100%">';
   echo "</form>";
} else {
  echo "<h2>No sheep/goat care records match specified parameters.</h2>";
} 
echo "<script>";
echo "$('html,body').animate({scrollTop: $('#sheep_care_report_scroll').offset().top });";
echo "</script>";
?>
