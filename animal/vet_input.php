<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$mth = $_POST['vet_date_month'];
$day = $_POST['vet_date_day'];
$year = $_POST['vet_date_year'];
$id = escapehtml($_POST['vet_id']);
$reason = escapehtml($_POST['vet_reason']);
$symptoms = escapehtml($_POST['symptoms']);
$temperature = escapehtml($_POST['temperature']);
$care = escapehtml($_POST['careGiven']);
$weight = escapehtml($_POST['vet_weight']);
$vet = escapehtml($_POST['vet_advisor']);
$contact = escapehtml($_POST['vet_contact']);
$assist = escapehtml($_POST['vet_assist']);
$comments = escapehtml($_POST['vet_comments']);

$sqlDate = $year."-".$mth."-".$day;

$dbcon->beginTransaction();
$sql = "insert into vet(care_date, animal_id, reason, symptoms, temperature, ".
       "care, weight, vet, contact, assistants, comments, userid) values (".
       ":date, :id, :reason, :symptoms, :temperature, :care, :weight, :vet, ".
       ":contact, :assist, :comments, :userid)";
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
   $stmt->bindParam(':userid', $_SESSION['user'], PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}
$autoID = $dbcon->lastInsertId();
$rows = $_POST['num_med_rows'];
if ($rows > 0) {
   $sql = "insert into meds_given(id, medication, units, units_given) values ".
          "(:id, :med, :units, :given)";
   try {
      $stmt = $dbcon->prepare($sql);
      for ($i = 1; $i <= $rows; $i++) {
         $stmt->bindParam(':id', $autoID, PDO::PARAM_INT);
         $med = escapehtml($_POST['med_table_med_'.$i]);
         $stmt->bindParam(':med', $med, PDO::PARAM_STR);
         $units = escapehtml($_POST['med_table_unit_'.$i]);
         $stmt->bindParam(':units', $units, PDO::PARAM_STR);
         $given = escapehtml($_POST['med_table_given_'.$i]);
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
