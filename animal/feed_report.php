<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$fyear = $_POST['feed_report_from_year'];
$fday = $_POST['feed_report_from_day'];
$fmth = $_POST['feed_report_from_month'];
$tyear = $_POST['feed_report_to_year'];
$tday = $_POST['feed_report_to_day'];
$tmth = $_POST['feed_report_to_month'];

$sqlFrom = $fyear."-".$fmth."-".$fday;
$sqlTo = $tyear."-".$tmth."-".$tday;

$group = escapehtml($_POST['feed_report_group']);
$vendor = escapehtml($_POST['feed_report_vendor']);
$type = escapehtml($_POST['feed_report_type']);
$subtype = escapehtml($_POST['feed_report_subtype']);

$sql = "select * from feed_purchase where animal_group like '".$group.
       "' and vendor like '".$vendor."' and type like '".$type.
       "' and subtype like '".$subtype."' and purch_date between '".$sqlFrom.
       "' and '".$sqlTo."' order by purch_date";

echo "<div id = 'feed_report_scroll'>&nbsp;</div>";
$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo "<h2>Feed Purchase Report</h2>";
   echo "<div class='tablediv'>";
   echo "<table border data-role='table' class='ui-responsive'>";
   echo "<thead><tr><th>Date</th><th>Feed Major Type</th>".
        "<th>Feed Type Details</th><th>For</th><th>Vendor</th>".
        "<th>Units Purchased</th><th>Unit Price</th><th>Total Price</th>".
        "<th>Unit Weight</th><th>Total Weight</th><th>Comments</th>";
   if ($_SESSION['admin']) {
      echo "<th>Edit</th><th>Delete</th>";
   }
   echo "</tr></thead><tbody>";
   $tprice = 0;
   $tweight = 0;
   do {
      echo "<tr><td>".humanDate($row['purch_date'])."</td><td>".
         $row['type']."</td><td>".$row['subtype']."<td>".
         $row['animal_group'].  "</td><td>".$row['vendor'].
         "</td><td>".$row['purchased']." ".$row['unit']."(s)</td><td>$";
      $uprice = number_format((float) $row['price_unit'], 2, '.', '');
      echo $uprice."</td><td>$";
      $price = number_format((float) ($row['price_unit'] * 
                     $row['purchased']), 2, '.', '');
      $tprice += $price;
      echo $price."</td><td>";
      $uweight = number_format((float) $row['weight_unit'], 2, '.', '');
      echo $uweight." lbs./".$row['unit']."</td><td>";
      $weight = number_format((float) ($row['weight_unit'] * 
                    $row['purchased']), 2, '.', '');
      $tweight += $weight;
      echo $weight." lbs.</td><td>".$row['comments']."</td>";
      if ($_SESSION['admin']) {
         echo "<td>";
         echo "<a href='#feed_edit' class='ui-btn' onclick='feed_edit_init(".
         $row['id'].");'>Edit</a>";
         echo "</td><td>";
         echo "<a href='#feed_delete' class='ui-btn' onclick='feed_delete(".
         $row['id'].");'>Delete</a>";
         echo "</td>";
      }
      echo "</tr>";
   } while ($row = $res->fetch(PDO::FETCH_ASSOC));
   echo "</tbody></table></div>";
   echo "<div>&nbsp;</div>";
   echo "<h2>Total Price: $".number_format((float) $tprice, 2, '.', '')."</h2>";
   echo "<h2>Total Weight: ".number_format((float) $tweight, 2, '.', '')." lbs.</h2>";
   echo "<div>&nbsp;</div>";
   $tsql = "select type, subtype, unit, avg(price_unit) as avprice, ".
          "avg(price_unit / weight_unit) as app from feed_purchase ".
          "where animal_group like '".$group.
          "' and vendor like '".$vendor."' and type like '".$type.
          "' and subtype like '".$subtype."' and purch_date between '".$sqlFrom.
          "' and '".$sqlTo."' group by type, subtype, unit";

   echo "<div class='tablediv'>";
   echo "<table border data-role='table' class='ui-responsive'>";
   echo "<thead><tr><th>Feed Major Type</th><th>Feed Type Details</th>".
        "<th>Average Price/Unit</th><th>Average Price/lb.</th>".
        "</tr></thead><tbody>";
   $res = $dbcon->query($tsql);
   while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
      echo "<tr><td>".$row['type']."</td><td>".$row['subtype']."</td><td>$".
           number_format((float) $row['avprice'], 2, '.', '').
           "/".$row['unit']."</td><td>$".
           number_format((float) $row['app'], 2, '.', '')."/lb.</td></tr>";
   }
   echo "</tbody></table></div>";
   echo "<div>&nbsp;</div>";
   echo "<form name='form' method='POST' action='/animal/down.php'>";
   echo "<input type = \"hidden\" name = \"query\" value = \"".
      escapehtml($sql)."\">";
   echo '<input type="submit" name="submit" value="Download Report"'.
        ' id="feed_report_download" class="ui-btn" style="width:100%">';
   echo "</form>";
} else {
  echo "<h2>No feed purchase records match specified parameters.</h2>";
}
echo "<script>";
echo "$('html,body').animate({scrollTop: $('#feed_report_scroll').offset().top });";
echo "</script>";

?>
