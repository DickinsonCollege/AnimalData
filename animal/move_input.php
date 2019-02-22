<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$mth = $_POST['move_input_date_month'];
$day = $_POST['move_input_date_day'];
$year = $_POST['move_input_date_year'];
$group = escapehtml($_POST['move_input_group']);
$subgroup = escapehtml($_POST['move_input_subgroup']);
$moveto = $_POST['move_input_move'];
$paddock = escapehtml($_POST['move_input_paddock']);
$height = $_POST['move_input_height'];
$density = $_POST['move_input_density'];
$comments = escapehtml($_POST['move_input_comments']);

$sqlDate = $year."-".$mth."-".$day;

$dbcon->beginTransaction();
$sql = "select forage from paddock where paddock_id = '".$paddock."'";
$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   $forage = $row['forage'];
} else {
   echo "Error: invalid paddock!";
   $dbcon->rollBack();
   die();
}

$sql = "insert into move(move_to, move_date, animal_group, sub_group, ".
       "paddock_id, forage, height, density, comments) values (:move, :dt, ".
       ":group, :subgroup, :paddock, :forage, :height, :density,:comments)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':move', $moveto, PDO::PARAM_INT);
   $stmt->bindParam(':dt', $sqlDate, PDO::PARAM_STR);
   $stmt->bindParam(':group', $group, PDO::PARAM_STR);
   $stmt->bindParam(':subgroup', $subgroup, PDO::PARAM_STR);
   $stmt->bindParam(':paddock', $paddock, PDO::PARAM_STR);
   $stmt->bindParam(':forage', $forage, PDO::PARAM_STR);
   $stmt->bindParam(':height', $height, PDO::PARAM_INT);
   $stmt->bindParam(':density', $density, PDO::PARAM_INT);
   $stmt->bindParam(':comments', $comments, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}
$dbcon->commit();
echo "success!";
?>
