<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$autoId = escapehtml($_POST['birth_edit_auto_id']);
$origId = escapehtml($_POST['birth_edit_orig_id']);
$origFile = escapehtml($_POST['birth_edit_orig_file']);
$curFile = escapehtml($_POST['birth_edit_current_picture']);
$id = escapehtml($_POST['birth_edit_id']);
$group = escapehtml($_POST['birth_edit_group']);
$subgroup = escapehtml($_POST['birth_edit_subgroup']);
$breed = escapehtml($_POST['birth_edit_breed']);
$gen = escapehtml($_POST['birth_edit_gender']);
$mth = $_POST['birth_edit_date_month'];
$day = $_POST['birth_edit_date_day'];
$year = $_POST['birth_edit_date_year'];
$orig = escapehtml($_POST['birth_edit_origin']);
$mom = escapehtml($_POST['birth_edit_mother']);
$dad = escapehtml($_POST['birth_edit_father']);
$name = escapehtml($_POST['name_edit']);
$mark = escapehtml($_POST['mark_edit']);
$comments = escapehtml($_POST['birth_edit_comments']);
$alive = $_POST['birth_edit_alive'];

$file = "";
if (isset($_FILES['birth_edit_file']) && 
    isset($_FILES['birth_edit_file']['name']) &&
    $_FILES['birth_edit_file']['name'] != "") {
   $file = "files/".$_FILES['birth_edit_file']['name'];
}

$sqlDate = $year."-".$mth."-".$day;

if ($id == "") {
   echo "Error: please enter an animal id.";
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
   $fres = upload('birth_edit_file');
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
$sql = "update animal set animal_id=:id, animal_group=:group, breed=:breed, ".
       "sub_group=:subgroup, gender=:gen, birthdate=:dt, origin=:orig, ".
       "mother=:mom, father=:dad,name=:name, markings=:mark, filename=:file, ".
       "alive=:alive, comments=:comments where id = ".$autoId;
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':id', $id, PDO::PARAM_STR);
   $stmt->bindParam(':group', $group, PDO::PARAM_STR);
   $stmt->bindParam(':subgroup', $subgroup, PDO::PARAM_STR);
   $stmt->bindParam(':breed', $breed, PDO::PARAM_STR);
   $stmt->bindParam(':gen', $gen, PDO::PARAM_STR);
   $stmt->bindParam(':dt', $sqlDate, PDO::PARAM_STR);
   $stmt->bindParam(':orig', $orig, PDO::PARAM_STR);
   $stmt->bindParam(':mom', $mom, PDO::PARAM_STR);
   $stmt->bindParam(':dad', $dad, PDO::PARAM_STR);
   $stmt->bindParam(':name', $name, PDO::PARAM_STR);
   $stmt->bindParam(':mark', $mark, PDO::PARAM_STR);
   $stmt->bindParam(':comments', $comments, PDO::PARAM_STR);
   $stmt->bindParam(':file', $newfile, PDO::PARAM_STR);
   $stmt->bindParam(':alive', $alive, PDO::PARAM_INT);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}
if ($origId != $id) {
   $sql = "update animal set mother=:id where mother = :origId";
   try {
      $stmt = $dbcon->prepare($sql);
      $stmt->bindParam(':id', $id, PDO::PARAM_STR);
      $stmt->bindParam(':origId', $origId, PDO::PARAM_STR);
      $stmt->execute();
   } catch (PDOException $p) {
      echo $p->getMessage();
      $dbcon->rollBack();
      die();
   }
   $sql = "update animal set father=:id where father = :origId";
   try {
      $stmt = $dbcon->prepare($sql);
      $stmt->bindParam(':id', $id, PDO::PARAM_STR);
      $stmt->bindParam(':origId', $origId, PDO::PARAM_STR);
      $stmt->execute();
   } catch (PDOException $p) {
      echo $p->getMessage();
      $dbcon->rollBack();
      die();
   }
}
$dbcon->commit();
echo "success!";
?>
