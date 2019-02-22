<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$mth = $_POST['notes_date_month'];
$day = $_POST['notes_date_day'];
$year = $_POST['notes_date_year'];
$note = escapehtml($_POST['notes_note']);
$file = "";
if (isset($_FILES['notes_file']) && isset($_FILES['notes_file']['name']) &&
    $_FILES['notes_file']['name'] != "") {
   $file = "files/".$_FILES['notes_file']['name'];
}

$fres = upload('notes_file');
if ($fres != "success!") {
   echo $fres;
   die();
} 

$sqlDate = $year."-".$mth."-".$day;

$dbcon->beginTransaction();
$sql = "insert into notes(note_date, note, userid, filename) ".
       "values (:dt, :note, :user, :file)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':dt', $sqlDate, PDO::PARAM_STR);
   $stmt->bindParam(':note', $note, PDO::PARAM_STR);
   $stmt->bindParam(':user', $_SESSION['user'], PDO::PARAM_STR);
   $stmt->bindParam(':file', $file, PDO::PARAM_STR);
   $stmt->execute();
} catch (PDOException $p) {
   if ($file != "") {
      unlink($file);
   }
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}
$dbcon->commit();
echo "success!";
?>
