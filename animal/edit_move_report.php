<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$fyear = $_POST['edit_move_report_from_year'];
$fday = $_POST['edit_move_report_from_day'];
$fmth = $_POST['edit_move_report_from_month'];
$tyear = $_POST['edit_move_report_to_year'];
$tday = $_POST['edit_move_report_to_day'];
$tmth = $_POST['edit_move_report_to_month'];
$group = escapehtml($_POST['edit_move_report_group']);
$subgroup = escapehtml($_POST['edit_move_report_subgroup']);
$paddock = escapehtml($_POST['edit_move_report_paddock']);

$sqlFrom = $fyear."-".$fmth."-".$fday;
$sqlTo = $tyear."-".$tmth."-".$tday;

$sql = "select * from move where move_date between '".  $sqlFrom."' and '".
       $sqlTo."' and animal_group like '".$group.  "' and sub_group like '".
       $subgroup."' and paddock_id like '".$paddock."' order by move_date";

echo "<div id = 'edit_move_report_scroll'>&nbsp;</div>";

$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo "<h2>Grazing Move Records</h2>";
   echo "<div class='tablediv'>";
   echo "<table border data-role='table' class='ui-responsive'>";
   echo "<thead><tr><th>Move Date</th><th>Animal Group</th><th>Subgroup</th>".
        "<th>Move</th><th>Paddock ID</th><th>Forage</th>".
        "<th>Forage Height (inches)</th>".
        "<th>Forage Density</th><th>Comments</th><th>Edit</th><th>Delete</th>";
   echo "</tr></thead>";
   do {
      echo "<tr><td>";
      echo humanDate($row['move_date']);
      echo "</td><td>";
      echo $row['animal_group'];
      echo "</td><td>";
      echo $row['sub_group'];
      echo "</td><td>";
      if ($row['move_to'] == 1) {
         echo "TO";
      } else {
         echo "FROM";
      }
      echo "</td><td>";
      echo $row['paddock_id'];
      echo "</td><td>";
      echo $row['forage'];
      echo "</td><td>";
      echo $row['height'];
      echo "</td><td>";
      switch ($row['density']) {
         case 1:
            echo "1: BARE SPOTS";
            break;
         case 2:
            echo "2: THIN";
            break;
         case 3:
            echo "3: MODERATE";
            break;
         case 4:
            echo "4: FULL";
            break;
         case 5:
            echo "5: LUSH";
            break;
         default:
            echo "Error: invalid value";
            break;
      }
      echo "</td><td>";
      echo $row['comments'];
      echo "</td>";
      echo "<td>";
      echo "<a href='#edit_move_edit' class='ui-btn' onclick='edit_move_edit_init(".
         $row['id'].");'>Edit</a>";
      echo "</td><td>";
      echo "<a href='#edit_move_delete' class='ui-btn' onclick='edit_move_delete(".
         $row['id'].");'>Delete</a>";
      echo "</td>";
      echo "</tr>";
   } while ($row = $res->fetch(PDO::FETCH_ASSOC));
   echo "</table>";
   echo "</div>";
   echo "<div>&nbsp;</div>";
} else {
  echo "<h2>No grazing move records match specified parameters.</h2>";
} 
echo "<script>";
echo "$('html,body').animate({scrollTop: $('#edit_move_report_scroll').offset().top });";
echo "</script>";
?>
