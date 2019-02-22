<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$group = escapehtml($_POST['edit_subgroups_group']);
$subgroup = escapehtml($_POST['edit_subgroups_subgroup']);

$sql = "select distinct animal_group, sub_group from animal where alive = 1 ".
       "and animal_group like '".$group."' and sub_group like '".$subgroup."'".
       " order by animal_group, sub_group";
$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo "<div id='edit_subgroups_scroll'></div>";
   echo "<form id ='update_all_subgroups_form' method='POST'>";
   echo "<div class='tablediv'>";
   $num = 1;
   do {
      $grp = $row['animal_group'];
      $subgrp = $row['sub_group'];
      echo "<div>&nbsp;</div>";
      echo "<h2>Group: ".$grp." Subgroup: ".$subgrp."</h2>";
      $gsql = "select * from sub_group where animal_group = '".$grp."' ".
              "order by sub_group";
      $gres = $dbcon->query($gsql);
      $subs = array();
      while ($grow = $gres->fetch(PDO::FETCH_ASSOC)) {
         array_push($subs, $grow['sub_group']);
      }

      $startnum = $num;
      $asql = "select * from animal where animal_group = '".$grp."' and ".
              "sub_group = '".$subgrp."' order by animal_id";
      $ares = $dbcon->query($asql);
      echo "<table border data-role='table' class='ui-responsive'>";
      echo "<thead><tr><th>Animal ID</th><th>Name</th><th>Subgroup</th></tr>".
           "</thead><tbody>";
      while ($arow = $ares->fetch(PDO::FETCH_ASSOC)) {
         echo "<tr><td>".$arow['animal_id']."</td><td>".$arow['name']."</td>";
         echo "<td><input type='hidden' name='sg_edit_id".$num.
              "' id='sg_edit_id".$num."' value='".$arow['id']."'>";
         $sg = $arow['sub_group'];
         echo "<select name='sg_edit".$num."' id='sg_edit".$num."' class='edit_".
              $grp."_".$subgrp."'>";
         //echo $arow['sub_group'];
         foreach ($subs as $sub) {
            echo "<option value='".$sub."'>".$sub."</option>";
         }
         echo "</select>";
         echo "<script>";
         echo "$('#sg_edit".$num."').selectmenu();";
         echo "$('#sg_edit".$num."').val('".$sg."');";
         echo "$('#sg_edit".$num."').selectmenu('refresh');";
         echo "</script>";
         echo "</td></tr>";
         $num++;
      }
      echo "</tbody></table>";

      echo '<div class="ui-field-contain">';
      echo '<label for="move_subgroup_subgroup'.$num.'">Move All To:</label>';
      echo '<select id="move_subgroup_subgroup'.$num.
           '" name="move_subgroup_subgroup'.$num.
           '" onchange="change_subgroup(\'move_subgroup_subgroup'.$num.'\', '.
           $startnum.', '.($num - 1).');">';
      foreach ($subs as $sub) {
         echo "<option value='".$sub."'>".$sub."</option>";
      }
      echo '</select>';
      echo '</div>';
      echo "<script>";
      echo "$('#move_subgroup_subgroup".$num."').selectmenu();";
      echo "$('#move_subgroup_subgroup".$num."').val('".$subgrp."');";
      echo "$('#move_subgroup_subgroup".$num."').selectmenu('refresh');";
      echo "</script>";
      echo '</div>';
   } while ($row = $res->fetch(PDO::FETCH_ASSOC));
   echo "</div>";
   echo "<div>&nbsp;</div>";
   echo "<input type='submit' class='submitbutton ui-btn' style='width:100%' ".
        "value='Update All Subgroups' id='update_subgroups_submit' ".
        "onclick='update_all_subgroups();'>";
   echo "<input type='hidden' name='update_subgroups_rows' ".
        "id='update_subgroups_rows' value='".($num - 1)."'>";
   echo "</form>";
   echo "<script>";
   echo "$('html,body').animate({scrollTop: $('#edit_subgroups_scroll').offset().top });";
   echo "</script>";
} else {
   echo "<h2>No animal records match specified parameters.</h2>";
}

?>
