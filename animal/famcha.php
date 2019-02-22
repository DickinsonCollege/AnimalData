<?php
   $sql = "select min(care_date) as mind, max(care_date) as maxd ".
           "from sheep_care, animal where care_date between '".$sqlFrom.
           "' and '".$sqlTo."' and sheep_care.animal_id = animal.animal_id".
           " and animal_group like '".$group."'";
   $res = $dbcon->query($sql);
   $row = $res->fetch(PDO::FETCH_ASSOC);
   $mind = $row['mind'];
   if ($mind == "") {
      echo "<h2>No Periodic Care Records</h2>";
   } else {
      $maxd = $row['maxd'];
      $care_dates = array($mind);
      $next = new DateTime($mind);
      date_add($next, new DateInterval('P7D'));
      $done = new DateTime($maxd);
      $nd = $next->format('Y-m-d');
      while (true) {
         $sql = "select min(care_date) as mind from sheep_care, animal ".
                "where care_date between '".$nd.  "' and '".$sqlTo.
                 "' and sheep_care.animal_id = animal.animal_id".
                 " and animal_group like '".$group."'";
         $res = $dbcon->query($sql);
         $row = $res->fetch(PDO::FETCH_ASSOC);
         $md = $row['mind'];
         if ($md != "") {
            array_push($care_dates, $md);
            $nt = new DateTime($md);
            date_add($nt, new DateInterval('P7D'));
            $nd = $nt->format('Y-m-d');
         } else {
            break;
         }
      }
   
      // FAMCHA eye distribution
      $dist = array(-1 => array(), 1 => array(), 2 => array(), 3 => array(),
                     4 => array(), 5 => array());
      $ind = 0;
      $ticks="[";
      foreach ($care_dates as $cd) {
         $sql = "select count(*) as tot from sheep_care, animal ".
                "where sheep_care.animal_id = animal.animal_id and ".
                "care_date between '".$cd."' and DATE_ADD('".$cd."', INTERVAL 7 DAY)".
                " and animal_group like '".$group."'";
         $res = $dbcon->query($sql);
         $row = $res->fetch(PDO::FETCH_ASSOC);
         $tot = $row['tot'];
         for($i = -1; $i < 6; $i++) {
            $dist[$i][$ind] = 0;
            if ($i == -1) {
               $i++;
            }
         }
         $sql = "select eye, (count(*) * 100) / ".$tot." as perc, ".
                "count(*) as cnt from sheep_care, animal ".
                "where sheep_care.animal_id = animal.animal_id and ".
                "care_date between '".$cd."' and DATE_ADD('".$cd."', INTERVAL 7 DAY)".
                " and animal_group like '".$group."' group by eye order by eye";
         $res = $dbcon->query($sql);
         $sum = 0;
         $vt = $tot;
         while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $eye = $row['eye'];
            $cnt = (int) $row['cnt'];
            $dist[$eye][$ind] = (int) $row['perc'];
            if ((int) $eye > 0) {
               $sum += (int) $eye * (int) $cnt;
            } else {
               $vt -= $cnt;
            }
         }
         $ticks .= "'".humanDate($cd)."\\nn=".$tot;
         if ($vt > 0) {
            $av = number_format((float) ($sum / $vt), 1, '.', '');
            $ticks .= ",m=".$av;
         }
         $ticks .= "',";
         $ind++;
      }
      $ticks = rtrim($ticks, ",")."]";
      $graphData = "[";
      foreach ($dist as $eye) {
         $graphData .= "[";
         foreach ($eye as $ed) {
            $graphData .= $ed.",";
         }
         $graphData = rtrim($graphData, ",")."],";
      }
      $graphData = rtrim($graphData, ",")."]";
   
      $label="[{label:'N/A'},{label:'1: RED'},{label:'2: RED-PINK'},".
             "{label:'3: PINK'},{label:'4: PINK-WHITE'},{label:'5: WHITE'}]";
      $color = "['black', 'red', 'palevioletred', 'hotpink', 'pink', 'white']";
      
      echo "<div>&nbsp;</div>";
      echo "<div class='tablediv'>";
      echo "<div id='".$eye_chart."'></div>";
      echo "</div>";
      echo "<div>&nbsp;</div>";
      echo "<script>";
      echo "
         var plot;
         $(document).ready(function(){
           plot = barChart('".$eye_chart."', 'FAMCHA ".$crits.
              " Eye Condition Distribution', ".$graphData.
              ", ".$ticks.", ".$label.", ".$color.");
         });";
      echo "</script>";
      echo "<input type='button' class='ui-btn' style='width:100%', onclick='replot(plot);'".
           " value='Refresh Graph'>";
   
      // Body Condition Distribution
      $dist = array(-1 => array(), 1 => array(), 2 => array(), 3 => array(),
                     4 => array(), 5 => array());
      $ind = 0;
      $ticks="[";
      foreach ($care_dates as $cd) {
         $sql = "select count(*) as tot from sheep_care, animal ".
                "where sheep_care.animal_id = animal.animal_id and ".
                "care_date between '".$cd."' and DATE_ADD('".$cd."', INTERVAL 7 DAY)".
                " and animal_group like '".$group."'";
         $res = $dbcon->query($sql);
         $row = $res->fetch(PDO::FETCH_ASSOC);
         $tot = $row['tot'];
         for($i = -1; $i < 6; $i++) {
            $dist[$i][$ind] = 0;
            if ($i == -1) {
               $i++;
            }
         }
         $sql = "select body, (count(*) * 100) / ".$tot." as perc, ".
                "count(*) as cnt from sheep_care, animal ".
                "where sheep_care.animal_id = animal.animal_id and ".
                "care_date between '".$cd."' and DATE_ADD('".$cd."', INTERVAL 7 DAY)".
                " and animal_group like '".$group."' group by body order by body";
         $res = $dbcon->query($sql);
         $sum = 0;
         $vt = $tot;
         while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
            $body = $row['body'];
            $cnt = (int) $row['cnt'];
            $dist[$body][$ind] = (int) $row['perc'];
            if ((int) $body > 0) {
               $sum += (int) $body * (int) $cnt;
            } else {
               $vt -= $cnt;
            }
         }
         $ticks .= "'".humanDate($cd)."\\nn=".$tot;
         if ($vt > 0) {
            $av = number_format((float) ($sum / $vt), 1, '.', '');
            $ticks .= ",m=".$av;
         }
         $ticks .= "',";
         $ind++;
      }
      $ticks = rtrim($ticks, ",")."]";
      $graphData = "[";
      foreach ($dist as $bod) {
         $graphData .= "[";
         foreach ($bod as $bc) {
            $graphData .= $bc.",";
         }
         $graphData = rtrim($graphData, ",")."],";
      }
      $graphData = rtrim($graphData, ",")."]";
   
      $label="[{label:'N/A'},{label:'1: VERY THIN'},{label:'2: THIN'},".
             "{label:'3: IDEAL'},{label:'4: CHUBBY'},{label:'5: OBESE'}]";
      $color = "['black', 'yellow', 'blue', 'green', 'purple', 'red']";
      
      echo "<div>&nbsp;</div>";
      echo "<div class='tablediv'>";
      echo "<div id='".$body_chart."'></div>";
      echo "</div>";
      echo "<div>&nbsp;</div>";
      echo "<script>";
      echo "
         var plot2;
         $(document).ready(function(){
           plot2 = barChart('".$body_chart."', 
              '".$crits." Body Condition Distribution', ".
              $graphData.  ", ".$ticks.", ".$label.", ".$color.");
         });";
      echo "</script>";
      echo "<input type='button' class='ui-btn' style='width:100%', onclick='replot(plot2);'".
           " value='Refresh Graph'>";
  }
?>
