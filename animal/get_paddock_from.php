<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$move = $_GET['move'];
if ($move == 1) {
   $sql = "select paddock_id from paddock where active = 1";
} else {
   $group = escapehtml($_GET['group']);
   $subgroup = escapehtml($_GET['subgroup']);
   $sql = "select paddock_id from move as moveto where move_to = 1 and ".
          "animal_group = '".$group."' and sub_group = '".$subgroup."' and ".
          "not exists (select paddock_id from move where move_to = 0 and ".
          "animal_group = '".$group."' and sub_group = '".$subgroup."' and ".
          "paddock_id = moveto.paddock_id and move_date >= moveto.move_date)".
          " order by paddock_id";
}

$result = $dbcon->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   echo "<option value='".$row['paddock_id']."'>".
      $row['paddock_id']."</option>";
}

?>
