<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

echo "<div class='tablediv'>";
echo "<table border data-role='table' class='ui-responsive'>";
echo "<thead><tr><th>Paddock ID</th><th>Size</th><th>Active</th><th>Forage".
     "</th><th>Latest Height</th><th>Current Occupants</th></tr></thead><tbody>";
$sql = "select * from paddock order by paddock_id";
$result = $dbcon->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $pad = $row['paddock_id'];
   echo "<tr><td>".$pad."</td><td>".$row['size']." acres</td><td>";
   if ($row['active'] == 1) {
      echo "Yes";
   } else {
      echo "No";
   }
   echo "</td><td>".$row['forage']."</td><td>";
   $hsql = "select * from move where paddock_id = '".$pad.
           "' and move_date >= all ".
              "(select move_date from move where paddock_id = '".$pad."')";
   $hresult = $dbcon->query($hsql);
   if ($hrow = $hresult->fetch(PDO::FETCH_ASSOC)) {
      echo $hrow['height']." in.";
   } else {
      echo "&nbsp;";
   }
   echo "</td><td>";
   $gsql = "select * from move as moveto where paddock_id = '".$pad.
           "' and move_to = 1 and not exists ".
              "(select * from move where move_to = 0 and ".
                "paddock_id = '".$pad."' and move_date > moveto.move_date)";
   $gresult = $dbcon->query($gsql);
   if ($grow = $gresult->fetch(PDO::FETCH_ASSOC)) {
      do {
         echo $grow['animal_group']." (".$grow['sub_group'].")<br>";
      } while ($grow = $gresult->fetch(PDO::FETCH_ASSOC));
   } else {
      echo "&nbsp;";
   }
   echo "</td></tr>";
}
echo "</tbody></table></div>";

?>
