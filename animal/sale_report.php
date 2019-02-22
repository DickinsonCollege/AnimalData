<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$fyear = $_POST['sale_report_from_year'];
$fday = $_POST['sale_report_from_day'];
$fmth = $_POST['sale_report_from_month'];
$tyear = $_POST['sale_report_to_year'];
$tday = $_POST['sale_report_to_day'];
$tmth = $_POST['sale_report_to_month'];

$sqlFrom = $fyear."-".$fmth."-".$fday;
$sqlTo = $tyear."-".$tmth."-".$tday;

$group = escapehtml($_POST['sale_report_group']);
$dest = escapehtml($_POST['sale_report_dest']);

$sql = "select sale.*, name, animal_group, birthdate, ".
       "DATEDIFF(sale_date, birthdate) as age from sale, animal ".
       "where sale.animal_id = animal.animal_id and animal_group like '".
       $group."' and destination like '".$dest."' and sale_date between '".
       $sqlFrom."' and '".$sqlTo."'";

echo "<div id = 'sale_report_scroll'>&nbsp;</div>";
$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo "<h2>Sale Report</h2>";
   echo "<div class='tablediv'>";
   echo "<table border data-role='table' class='ui-responsive'>";
   echo "<thead><tr><th>Sale Date</th><th>Animal ID</th><th>Name</th>".
        "<th>Animal Group</th><th>Date of Birth</th><th>Sale Tag</th>".
        "<th>Destination</th><th>Weight</th><th>Average Gain/Day</th>".
        "<th>Price/Lb.</th><th>Fees</th><th>Net Return</th><th>Comments</th>";
   if ($_SESSION['admin']) {
      echo "<th>Edit</th><th>Delete</th>";
   }
   echo "</tr></thead><tbody>";
   $cnt = 0;
   $tfee = 0;
   $tnet = 0;
   $tweight = 0;
   $tprice = 0;
   $tgain = 0;
   do {
      $cnt++;
      $tfee += $row['fees'];
      $tweight += $row['weight'];
      $tprice += $row['price_lb'];
      echo "<tr><td>".humanDate($row['sale_date'])."</td><td>".
           $row['animal_id']."</td><td>".$row['name']."<td>".
           $row['animal_group']."</td><td>".
           humanDate($row['birthdate'])."</td><td>".$row['sale_tag'].
           "</td><td>".$row['destination']."</td><td>".$row['weight'].
           " lbs. (".$row['estimated'].")</td><td>";
      $gain = number_format((float) $row['weight'] / $row['age'], 2, '.', '');
      echo $gain." lbs./day</td><td>$".$row['price_lb']."</td><td>$".
           $row['fees']."</td><td>$";
      $net = number_format((float) $row['weight'] * $row['price_lb'] - 
              $row['fees'], 2, '.', '');
      echo $net."</td><td>".$row['comments']."</td>";
      $tnet += $net;
      $tgain += $gain;
      if ($_SESSION['admin']) {
         echo "<td>";
         echo "<a href='#sale_edit' class='ui-btn' onclick='sale_edit_init(".
         $row['id'].");'>Edit</a>";
         echo "</td><td>";
         echo "<a href='#sale_delete' class='ui-btn' onclick='sale_delete(".
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
   echo "<thead><tr><th>Animals Sold</th><th>Total Fees</th>".
        "<th>Total Net Return</th>";
   if ($group != "%") {
      echo "<th>Average Weight</th><th>Average Gain</th><th>Average Price</th>";
   }
   echo "</tr></thead><tbody>";
   echo "<tr><td>".$cnt."</td><td>$".number_format((float) $tfee, 2, ".", "").
        "</td><td>$".number_format((float) $tnet, 2, ".", "")."</td>";
   if ($group != "%") {
      echo "<td>".number_format((float) $tweight/$cnt, 2, ".", "").
           " lbs.</td><td>".
           number_format((float) $tgain/$cnt, 2, ".", "")." lbs./day</td><td>$".
           number_format((float) $tprice/$cnt, 2, ".", "")."</td><td>";
           
   }
   echo "</tr>";
   echo "</tbody></table></div>";
   echo "<div>&nbsp;</div>";
   echo "<form name='form' method='POST' action='/animal/down.php'>";
   echo "<input type = \"hidden\" name = \"query\" value = \"".
      escapehtml($sql)."\">";
   echo '<input type="submit" name="submit" value="Download Report"'.
        ' id="sale_report_download" class="ui-btn" style="width:100%">';
   echo "</form>";
} else {
  echo "<h2>No sale records match specified parameters.</h2>";
}
echo "<script>";
echo "$('html,body').animate({scrollTop: $('#sale_report_scroll').offset().top });";
echo "</script>";

?>
