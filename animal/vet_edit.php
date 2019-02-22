<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$autoId = $_POST['vet_edit_auto_id'];
$mth = $_POST['vet_edit_date_month'];
$day = $_POST['vet_edit_date_day'];
$year = $_POST['vet_edit_date_year'];
$id = escapehtml($_POST['vet_edit_id']);
$reason = escapehtml($_POST['vet_edit_reason']);
$symptoms = escapehtml($_POST['symptoms_edit']);
$temperature = escapehtml($_POST['temperature_edit']);
$care = escapehtml($_POST['care_edit']);
$weight = escapehtml($_POST['vet_edit_weight']);
$vet = escapehtml($_POST['vet_edit_advisor']);
$contact = escapehtml($_POST['vet_edit_contact']);
$assist = escapehtml($_POST['vet_edit_assist']);
$comments = escapehtml($_POST['vet_edit_comments']);

$sqlDate = $year."-".$mth."-".$day;

$dbcon->beginTransaction();
$sql = "update vet set care_date=:date, animal_id=:id, reason=:reason, ".
        "symptoms=:symptoms, temperature=:temperature, care=:care, ".
        "weight=:weight, vet=:vet, contact=:contact, assistants=:assist, ".
        "comments=:comments where id=:autoId";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':date', $sqlDate, PDO::PARAM_STR);
   $stmt->bindParam(':id', $id, PDO::PARAM_STR);
   $stmt->bindParam(':reason', $reason, PDO::PARAM_STR);
   $stmt->bindParam(':symptoms', $symptoms, PDO::PARAM_STR);
   $stmt->bindParam(':temperature', $temperature, PDO::PARAM_STR);
   $stmt->bindParam(':care', $care, PDO::PARAM_STR);
   $stmt->bindParam(':weight', $weight, PDO::PARAM_STR);
   $stmt->bindParam(':vet', $vet, PDO::PARAM_STR);
   $stmt->bindParam(':contact', $contact, PDO::PARAM_STR);
   $stmt->bindParam(':assist', $assist, PDO::PARAM_STR);
   $stmt->bindParam(':comments', $comments, PDO::PARAM_STR);
   $stmt->bindParam(':autoId', $autoId, PDO::PARAM_INT);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$sql = "delete from meds_given where id = ".$autoId;
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$rows = $_POST['num_med_edit_rows'];
if ($rows > 0) {
   $sql = "insert into meds_given(id, medication, units, units_given) values ".
          "(:id, :med, :units, :given)";
   try {
      $stmt = $dbcon->prepare($sql);
      for ($i = 1; $i <= $rows; $i++) {
         $stmt->bindParam(':id', $autoId, PDO::PARAM_INT);
         $med = escapehtml($_POST['med_edit_table_med_'.$i]);
         $stmt->bindParam(':med', $med, PDO::PARAM_STR);
         $units = escapehtml($_POST['med_edit_table_unit_'.$i]);
         $stmt->bindParam(':units', $units, PDO::PARAM_STR);
         $given = escapehtml($_POST['med_edit_table_given_'.$i]);
         if ($given == "") {
            echo "Error: units given not entered in row ".$i;
            $dbcon->rollBack();
            die();
         }
         if (floatval($given) < 0) {
            echo "Error: invalid units given in row ".$i;
            $dbcon->rollBack();
            die();
         }
         $stmt->bindParam(':given', $given, PDO::PARAM_STR);
         $stmt->execute();
      }
   } catch (PDOException $p) {
      echo $p->getMessage();
      $dbcon->rollBack();
      die();
   }
}
$dbcon->commit();
echo "success!";
?>
