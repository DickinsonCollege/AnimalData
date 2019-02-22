<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$autoId = $_POST['edit_move_edit_auto_id'];
$mth = $_POST['edit_move_date_month'];
$day = $_POST['edit_move_date_day'];
$year = $_POST['edit_move_date_year'];
$group = escapehtml($_POST['edit_move_group']);
$subgroup = escapehtml($_POST['edit_move_subgroup']);
$move_to = $_POST['edit_move_move'];
$paddock = escapehtml($_POST['edit_move_paddock']);
$forage = escapehtml($_POST['edit_move_forage']);
$height = escapehtml($_POST['edit_move_height']);
$density = escapehtml($_POST['edit_move_density']);
$comments = escapehtml($_POST['edit_move_comments']);

$sqlDate = $year."-".$mth."-".$day;

$dbcon->beginTransaction();
$sql = "update move set move_date=:date, move_to=:move_to, ".
        "animal_group=:group, sub_group=:subgroup, paddock_id=:paddock, ".
        "forage=:forage, height=:height, density=:density, comments=:comments ".
        "where id=:autoId";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':date', $sqlDate, PDO::PARAM_STR);
   $stmt->bindParam(':move_to', $move_to, PDO::PARAM_INT);
   $stmt->bindParam(':group', $group, PDO::PARAM_STR);
   $stmt->bindParam(':subgroup', $subgroup, PDO::PARAM_STR);
   $stmt->bindParam(':paddock', $paddock, PDO::PARAM_STR);
   $stmt->bindParam(':forage', $forage, PDO::PARAM_STR);
   $stmt->bindParam(':height', $height, PDO::PARAM_INT);
   $stmt->bindParam(':density', $density, PDO::PARAM_INT);
   $stmt->bindParam(':comments', $comments, PDO::PARAM_STR);
   $stmt->bindParam(':autoId', $autoId, PDO::PARAM_INT);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
