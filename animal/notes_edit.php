<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$autoId = escapehtml($_POST['notes_edit_auto_id']);
$origFile = escapehtml($_POST['notes_edit_orig_file']);
$curFile = escapehtml($_POST['notes_edit_current_picture']);
$mth = $_POST['notes_edit_date_month'];
$day = $_POST['notes_edit_date_day'];
$year = $_POST['notes_edit_date_year'];
$note = escapehtml($_POST['notes_edit_note']);

$file = "";
if (isset($_FILES['notes_edit_file']) && 
    isset($_FILES['notes_edit_file']['name']) &&
    $_FILES['notes_edit_file']['name'] != "") {
   $file = "files/".$_FILES['notes_edit_file']['name'];
}

$sqlDate = $year."-".$mth."-".$day;

if ($note == "") {
   echo "Error: please enter a note.";
   die();
}

if ($origFile != "" && $curFile == "") {
   // user deleted picture
   unlink($origFile);
   $newfile = "";
} else {
   $newfile = $file;
   if ($file == "" && $origFile != "") {
      $newfile = $origFile;
   }
}

if ($file != "" && $file != $origFile) {
   $fres = upload('notes_edit_file');
   if ($fres != "success!") {
      echo $fres;
      die();
   } 
   if ($origFile != "") {
      // deleting replaced file
      unlink($origFile);
   }
}

$dbcon->beginTransaction();
$sql = "update notes set note_date=:dt, note=:note, filename=:file ".
       "where id = :autoId";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':dt', $sqlDate, PDO::PARAM_STR);
   $stmt->bindParam(':note', $note, PDO::PARAM_STR);
   $stmt->bindParam(':file', $newfile, PDO::PARAM_STR);
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
