<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$id = escapehtml($_POST['slay_input_id']);
$mth = $_POST['slay_input_date_month'];
$day = $_POST['slay_input_date_day'];
$year = $_POST['slay_input_date_year'];
$tag = escapehtml($_POST['slay_input_tag']);
$weight = $_POST['slay_input_weight'];
$estimated = escapehtml($_POST['slay_input_estimated']);
$house = escapehtml($_POST['slay_input_house']);
$hauler = escapehtml($_POST['slay_input_hauler']);
$haul_equip = escapehtml($_POST['slay_input_haul_equip']);
$fee = $_POST['slay_input_fee'];
$comments = escapehtml($_POST['slay_input_comments']);

$sqlDate = $year."-".$mth."-".$day;

$dbcon->beginTransaction();
$sql = "insert into slaughter(animal_id, slay_date, sale_tag, weight, ".
       "estimated, slay_house, hauler, haul_equip, fees, comments) values ".
       "(:id, :dt, :tag, :weight, :estimated, :house, :hauler, :haul_equip, ".
       ":fee, :comments)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':id', $id, PDO::PARAM_STR);
   $stmt->bindParam(':dt', $sqlDate, PDO::PARAM_STR);
   $stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
   $stmt->bindParam(':weight', $weight, PDO::PARAM_STR);
   $stmt->bindParam(':estimated', $estimated, PDO::PARAM_STR);
   $stmt->bindParam(':house', $house, PDO::PARAM_STR);
   $stmt->bindParam(':hauler', $hauler, PDO::PARAM_STR);
   $stmt->bindParam(':haul_equip', $haul_equip, PDO::PARAM_STR);
   $stmt->bindParam(':comments', $comments, PDO::PARAM_STR);
   $stmt->bindParam(':fee', $fee, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$sql = "update animal set alive=0 where animal_id = :id";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':id', $id, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
