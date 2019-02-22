<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$autoId = $_POST['sale_edit_auto_id'];
$id = escapehtml($_POST['sale_edit_id']);
$origId = escapehtml($_POST['sale_edit_orig_id']);
$mth = $_POST['sale_edit_date_month'];
$day = $_POST['sale_edit_date_day'];
$year = $_POST['sale_edit_date_year'];
$tag = escapehtml($_POST['sale_edit_tag']);
$dest = escapehtml($_POST['sale_edit_dest']);
$weight = $_POST['sale_edit_weight'];
$estimated = escapehtml($_POST['sale_edit_estimated']);
$price = $_POST['sale_edit_price'];
$fee = $_POST['sale_edit_fee'];
$comments = escapehtml($_POST['sale_edit_comments']);

$sqlDate = $year."-".$mth."-".$day;

$dbcon->beginTransaction();
$sql = "update sale set animal_id=:id, sale_date=:dt, sale_tag=:tag, ".
       "destination=:dest, weight=:weight, estimated=:estimated, ".
       "price_lb=:price, fees=:fee, comments=:comments where id=:autoId";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':id', $id, PDO::PARAM_STR);
   $stmt->bindParam(':dt', $sqlDate, PDO::PARAM_STR);
   $stmt->bindParam(':tag', $tag, PDO::PARAM_STR);
   $stmt->bindParam(':dest', $dest, PDO::PARAM_STR);
   $stmt->bindParam(':weight', $weight, PDO::PARAM_STR);
   $stmt->bindParam(':estimated', $estimated, PDO::PARAM_STR);
   $stmt->bindParam(':price', $price, PDO::PARAM_STR);
   $stmt->bindParam(':fee', $fee, PDO::PARAM_STR);
   $stmt->bindParam(':comments', $comments, PDO::PARAM_STR);
   $stmt->bindParam(':autoId', $autoId, PDO::PARAM_INT);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

if ($id != $origId) {
   // kill new animal
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

   // resurrect old animal
   $sql = "update animal set alive=1 where animal_id = :origId";
   try {
      $stmt = $dbcon->prepare($sql);
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
