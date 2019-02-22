<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$medication = strtoupper(escapehtml($_POST['medication']));
$dosage = strtoupper(escapehtml($_POST['dosage']));

if ($medication == "") {
   echo "Error: enter a medication name.";
   die();
}

if ($dosage == "") {
   echo "Error: enter a dosage.";
   die();
}

$dbcon->beginTransaction();
$sql = "insert into medication(medication, dosage) values ".
       "(:medication, :dosage)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':medication', $medication, PDO::PARAM_STR);
   $stmt->bindParam(':dosage', $dosage, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

$dbcon->commit();
echo "success!";
?>
