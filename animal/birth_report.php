<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$id = escapehtml($_POST['birth_report_id']);
$group = escapehtml($_POST['birth_report_group']);
$breed = escapehtml($_POST['birth_report_breed']);
$subgroup = escapehtml($_POST['birth_report_subgroup']);
$gen = escapehtml($_POST['birth_report_gender']);
$fyear = $_POST['birth_date_from_year'];
$fday = $_POST['birth_date_from_day'];
$fmth = $_POST['birth_date_from_month'];
$tyear = $_POST['birth_date_to_year'];
$tday = $_POST['birth_date_to_day'];
$tmth = $_POST['birth_date_to_month'];
$mom = escapehtml($_POST['birth_report_mother']);
$dad = escapehtml($_POST['birth_report_father']);
$alive = escapehtml($_POST['birth_report_alive']);

$sqlFrom = $fyear."-".$fmth."-".$fday;
$sqlTo = $tyear."-".$tmth."-".$tday;

$from = " from animal where animal_id like '".$id."' and animal_group like '";
$where = "' and breed like '".$breed."' and sub_group like '".$subgroup.
   "' and gender like '".$gen."' and mother like '".$mom.
   "' and father like '".$dad."' and alive like '".$alive."'";
if (!isset($_POST['birth_date_all'])) {
   $where .= " and birthdate between '".$sqlFrom."' and '".$sqlTo."'";
}
$where .= " order by animal_group, sub_group, animal_id";

$fromWhere = $from.$group.$where;

$weightSql = "(select weight from sheep_care where sheep_care.".
                "animal_id = animal.animal_id and weight != 'N/A' and care_date ".
                ">= all (select care_date from sheep_care where animal_id = animal.".
                "animal_id and weight != 'N/A') limit 1) as weight";

$sqlDown = "select *, ".$weightSql.$fromWhere;

echo "<div id = 'birth_report_scroll'>&nbsp;</div>";

$groups = array();
$sql = "select distinct animal_group".$fromWhere;
$res = $dbcon->query($sql);
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   array_push($groups, $row['animal_group']);
}

if (count($groups) == 0) {
   echo "<h2>No animal records match specified parameters.</h2>";
}

foreach ($groups as $agroup) {
   echo "<h2>".$agroup."</h2>";
   $sql = "select *, ".$weightSql.$from.$agroup.$where;
   echo "<div class='tablediv'>";
   echo "<table border data-role='table' class='ui-responsive'>";
   echo "<thead><tr><th>Animal ID</th><th>Breed</th><th>Subgroup</th>".
        "<th>Gender</th>";
   echo "<th>Birthdate</th><th>Origin</th><th>Mother</th><th>Father</th>";
   echo "<th>Name</th><th>Color &amp; Markings</th><th>Last Weight</th>".
        "<th>Comments</th><th>Alive/On Farm</th><th>Picture</th>";
   if ($_SESSION['admin']) {
      echo "<th>Edit</th><th>Delete</th>";
   }
   echo "</tr></thead>";
   $tot = 0;
   $male = 0;
   $female = 0;
   $twght = 0;
   $tnum = 0;
   $res = $dbcon->query($sql);
   while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
      $tot++;
      echo "<tr><td>";
      echo $row['animal_id'];
      echo "</td><td>";
      echo $row['breed'];
      echo "</td><td>";
      echo $row['sub_group'];
      echo "</td><td>";
      $gen =  $row['gender'];
      echo $gen;
      if ($gen == "F") {
         $female++;
      } else {
         $male++;
      }
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
      $wt = $row['weight'];
      if ($wt != "") {
         echo $wt." lbs.";
         $twght += $wt;
         $tnum++;
      } else {
         echo "&nbsp;";
      } 
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
      echo "</td>";
      if ($_SESSION['admin']) {
         echo "<td>";
         echo "<a href='#birth_edit' class='ui-btn' onclick='birth_edit_init(".
            $row['id'].");'>Edit</a>";
         echo "</td><td>";
         echo "<a href='#birth_delete' class='ui-btn' onclick='birth_delete(".
            $row['id'].");'>Delete</a>";
         echo "</td>";
      }
      echo "</tr>";
   }
   echo "</table>";
   echo "</div>";
   echo "<div>&nbsp;</div>";
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
}
if (count($groups) > 0) {
   echo "<form name='form' method='POST' action='/animal/down.php'>";
   echo "<input type = \"hidden\" name = \"query\" value = \"".
      escapehtml($sqlDown)."\">";
   echo '<input type="submit" name="submit" value="Download Report"'.
        ' id="birth_report_download" class="ui-btn" style="width:100%">';
   echo "</form>";
}
echo "<script>";
echo "$('html,body').animate({scrollTop: $('#birth_report_scroll').offset().top });";
echo "</script>";
?>
