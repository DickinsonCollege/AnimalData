<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$fyear = $_POST['move_report_from_year'];
$fday = $_POST['move_report_from_day'];
$fmth = $_POST['move_report_from_month'];
$tyear = $_POST['move_report_to_year'];
$tday = $_POST['move_report_to_day'];
$tmth = $_POST['move_report_to_month'];
$group = escapehtml($_POST['move_report_group']);
$subgroup = escapehtml($_POST['move_report_subgroup']);
$paddock = escapehtml($_POST['move_report_paddock']);

$sqlFrom = $fyear."-".$fmth."-".$fday;
$sqlTo = $tyear."-".$tmth."-".$tday;

$sql = "select moveto.move_date as mtd, movefrom.move_date as mfd, ".
       "moveto.paddock_id, moveto.animal_group, moveto.sub_group, ".
       "movefrom.forage, ".  "(moveto.height - movefrom.height) as dif ".
       "from move as moveto, ".  "move as movefrom ".
       "where moveto.move_to ".
       "= 1 and movefrom.move_to = 0 and moveto.paddock_id = ".
       "movefrom.paddock_id and moveto.paddock_id like '".$paddock."' and ".
       "moveto.animal_group like '".$group."' and moveto.sub_group like '".
       $subgroup."' and moveto.animal_group = movefrom.animal_group and ".
       "moveto.sub_group = movefrom.sub_group and (moveto.move_date between '".
       $sqlFrom."' and '".$sqlTo."' or movefrom.move_date between '".$sqlFrom.
       "' and '".$sqlTo."') and movefrom.move_date >= ".
       "moveto.move_date and not exists (select * from move where animal_group ".
       "= moveto.animal_group and sub_group = moveto.sub_group and paddock_id ".
       " = moveto.paddock_id and move_to = ".
       "0 and move_date between moveto.move_date and movefrom.move_date and ".
       "move_date < movefrom.move_date)";

$sqlStill = "select * from move as moveto where move_to = 1 and animal_group ".
            "like '".$group."' and sub_group like '".$subgroup."' and ".
            "paddock_id like '".$paddock."' and ".
            "move_date between '".$sqlFrom."' and '".$sqlTo."' and not exists ".
            "(select * from move where move_to = 0 and animal_group = moveto.".
            "animal_group and sub_group = moveto.sub_group and paddock_id = ".
            "moveto.paddock_id and move_date >= moveto.move_date)";

$sqlDown = "select moveto.*, movefrom.move_date as movefrom_date, ".
       "movefrom.height as movefrom_height, movefrom.density as ".
       "movefrom_density from move as moveto, ".
       "move as movefrom where moveto.move_to ".
       "= 1 and movefrom.move_to = 0 and moveto.paddock_id = ".
       "movefrom.paddock_id and moveto.paddock_id like '".$paddock."' and ".
       "moveto.animal_group like '".$group."' and moveto.sub_group like '".
       $subgroup."' and moveto.animal_group = movefrom.animal_group and ".
       "moveto.sub_group = movefrom.sub_group and (moveto.move_date between '".
       $sqlFrom."' and '".$sqlTo."' or movefrom.move_date between '".$sqlFrom.
       "' and '".$sqlTo."') and movefrom.move_date >= ".
       "moveto.move_date and not exists (select * from move where animal_group ".
       "= moveto.animal_group and sub_group = moveto.sub_group and paddock_id ".
       " = moveto.paddock_id and move_to = ".
       "0 and move_date between moveto.move_date and movefrom.move_date and ".
       "move_date < movefrom.move_date) union ".
       "select moveto.*, null, null, null from move as moveto where ".
          "move_to = 1 and animal_group like '".$group."' and sub_group like ".
          "'".$subgroup."' and paddock_id like '".$paddock."' and ".
          "move_date between '".$sqlFrom."' and '".$sqlTo."' and not exists ".
          "(select * from move where move_to = 0 and animal_group = moveto.".
          "animal_group and sub_group = moveto.sub_group and paddock_id = ".
          "moveto.paddock_id and move_date > moveto.move_date)";

$sqlPad = "select distinct paddock_id from move where move_date between '".
          $sqlFrom."' and '".$sqlTo."' and animal_group like '".$group.
          "' and sub_group like '".$subgroup."' and paddock_id like '".
          $paddock."' order by paddock_id";
$pads = array();
$res = $dbcon->query($sqlPad);
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   $pads[$row['paddock_id']] = array();
}

echo "<div id = 'move_report_scroll'>&nbsp;</div>";

if (count($pads) > 0) {
   $ftypes = array();
   $fsql = "select forage from forage";
   $fres = $dbcon->query($fsql);
   while ($frow = $fres->fetch(PDO::FETCH_ASSOC)) {
      array_push($ftypes, $frow['forage']);
   }
   $grid = array();
   $from = new DateTime($sqlFrom);
   $to = new DateTime($sqlTo);
   while ($from <= $to) {
      $dt = $from->format('m-d-Y');
      $np = $pads;
      $grid[$dt] = $np; 
      date_add($from, new DateInterval('P1D'));
   }
   
   $from = new DateTime($sqlFrom);
   $cons = $pads;
   foreach ($cons as $p => $v) {
      foreach ($ftypes as $ftype) {
         $cons[$p][$ftype] = 0;
      }
   }
   $res = $dbcon->query($sql);

   $allgroups = array();

   function abbrv($grp, $subgrp) {
      global $allgroups;
      if (array_key_exists($grp, $allgroups)) {
         $subs = $allgroups[$grp];
         if (!in_array($subgrp, $subs)) {
            array_push($subs, $subgrp);
            $allgroups[$grp] = $subs;
         }
      } else {
         $allgroups[$grp] = array($subgrp);
      }
      $grpa = substr($grp, 0, 1);
      $sga = explode(" ", $subgrp);
      foreach ($sga as $w) {
         $fst = strtolower(substr($w, 0, 1));
         if (ctype_alpha($fst)) {
            $grpa .= $fst;
         }
      }
      return $grpa;
   }

   while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
      $mf = new DateTime($row['mtd']);
      if ($mf < $from) {
         $mf = new DateTime($sqlFrom);
      }
      $mt = new DateTime($row['mfd']);
      if ($mt > $to) {
         $mt = new DateTime($sqlTo);
      }
      $grp = abbrv($row['animal_group'], $row['sub_group']);
      $pad = $row['paddock_id'];
      while ($mf <= $mt) {
         $dt = $mf->format('m-d-Y');
         if (!in_array($grp, $grid[$dt][$pad])) {
            array_push($grid[$dt][$pad], $grp); 
         }
         date_add($mf, new DateInterval('P1D'));
      }
      $cons[$pad][$row['forage']] += $row['dif'];
   }
   
   $to = new DateTime($sqlTo);
   $res = $dbcon->query($sqlStill);
   while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
      $from = new DateTime($row['move_date']);
      $grp = abbrv($row['animal_group'], $row['sub_group']);
      $pad = $row['paddock_id'];
      while ($from <= $to) {
         $dt = $from->format('m-d-Y');
         if (!in_array($grp, $grid[$dt][$pad])) {
            array_push($grid[$dt][$pad], $grp); 
         }
         date_add($from, new DateInterval('P1D'));
      }
   }
   echo "<h2>Grazing Move Report</h2>";
   echo "<div class='tablediv'>";

   echo "<table border data-role='table' class='ui-responsive'>";
   echo "<caption>Key</caption>";
   echo "<thead><tr><th>Abbreviation</th><th>Animal Group</th>";
   echo "<th>Subgroup</th></tr><tbody>";
   foreach ($allgroups as $g => $sga) {
      foreach ($sga as $sg) {
         echo "<tr><td>".abbrv($g, $sg)."</td><td>".$g."</td><td>".$sg.
              "</td></tr>";
      }
   }
   echo "</tbody></table><div>&nbsp;</div>";
   
   echo "<table border data-role='table' class='ui-responsive'>";
   echo "<thead><tr><th>Date</th>";
   foreach ($pads as $p => $v) {
      echo "<th>".$p."</th>";
   }
   echo "</tr></thead><tbody>";
   foreach ($grid as $dt => $aa) {
      echo "<tr><td>".$dt."</td>";
      foreach ($aa as $a) {
         sort($a);
         echo "<td>";
         echo implode('', $a);
         echo "</td>";
      }
      echo "</tr>";
   }
   echo "</tbody></table></div>";
   echo "<div>&nbsp;</div>";
   echo "<div class='tablediv'>";
   echo "<h2>Total Forage Consumption</h2>";
   echo "<table border data-role='table' class='ui-responsive'>";
   echo "<thead><tr><th>Paddock</th><th>Forage</th><th>Height ".
        "Consumed (inches)</th><th>Weight Consumed (lbs)</th></tr>".
        "</thead><tbody>";
   $dens = array();
   $fsql = "select * from forage";
   $fres = $dbcon->query($fsql);
   while ($frow = $fres->fetch(PDO::FETCH_ASSOC)) {
      $dens[$frow['forage']] = $frow['density'];
   }
   $size = array();
   $ssql = "select * from paddock";
   $sres = $dbcon->query($ssql);
   while ($srow = $sres->fetch(PDO::FETCH_ASSOC)) {
      $size[$srow['paddock_id']] = $srow['size'];
   }
   foreach ($cons as $p => $in) {
      foreach ($in as $ftype => $ht) {
         if ($ht > 0) {
            echo "<tr><td>".$p."</td><td>".$ftype."</td><td>".$ht.
                 "</td><td>";
            $wt = number_format((float) ($ht * $dens[$ftype] * $size[$p]), 2,
                                '.', '');
            echo $wt."</td></tr>";
         }
      }
   }
   echo "</tbody></table>";
   echo "</div>";
   echo "<div>&nbsp;</div>";
   echo "<form name='form' method='POST' action='/animal/down.php'>";
   echo "<input type = \"hidden\" name = \"query\" value = \"".
      escapehtml($sqlDown)."\">";
   echo '<input type="submit" name="submit" value="Download Report"'.
        ' id="move_report_download" class="ui-btn" style="width:100%">';
   echo "</form>";
} else {
  echo "<h2>No grazing move records match specified parameters.</h2>";
}
echo "<script>";
echo "$('html,body').animate({scrollTop: $('#move_report_scroll').offset().top });";
echo "</script>";

?>
