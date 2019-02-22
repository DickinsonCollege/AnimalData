<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$id = escapehtml($_POST['birth_id']);
$group = escapehtml($_POST['birth_group']);
$breed = escapehtml($_POST['birth_breed']);
$subgroup = escapehtml($_POST['birth_subgroup']);
$gen = escapehtml($_POST['birth_gender']);
$mth = $_POST['birth_date_month'];
$day = $_POST['birth_date_day'];
$year = $_POST['birth_date_year'];
$orig = escapehtml($_POST['birth_origin']);
$mom = escapehtml($_POST['birth_mother']);
$dad = escapehtml($_POST['birth_father']);
$name = escapehtml($_POST['name']);
$mark = escapehtml($_POST['mark']);
$comments = escapehtml($_POST['birth_comments']);
$file = "";
if (isset($_FILES['birth_file']) && isset($_FILES['birth_file']['name']) &&
    $_FILES['birth_file']['name'] != "") {
   $file = "files/".$_FILES['birth_file']['name'];
}

$fres = upload('birth_file');
if ($fres != "success!") {
   echo $fres;
   die();
} 

$sqlDate = $year."-".$mth."-".$day;

$dbcon->beginTransaction();
$sql = "insert into animal(animal_id, animal_group, breed, sub_group, ".
       "gender, birthdate, origin, mother, father, name, markings, ".
       "comments, filename, alive) ".
       "values (:id, :group, :breed, :subgroup, :gen, :dt, :orig, :mom, :dad, ".
        ":name, :mark, :comments, :file, 1)";
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
