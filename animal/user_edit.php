<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$user = escapehtml($_POST['edit_user_name']);
$admin = $_POST['edit_user_admin'];
$active = $_POST['edit_user_active'];
$pass1 = escapehtml($_POST['edit_user_pass1']);
$pass2 = escapehtml($_POST['edit_user_pass2']);

$dbcon->beginTransaction();
$sql = "update users set active=:active, admin=:admin where username=:user";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':user', $user, PDO::PARAM_STR);
   $stmt->bindParam(':active', $active, PDO::PARAM_INT);
   $stmt->bindParam(':admin', $admin, PDO::PARAM_INT);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}


if ($_SERVER["HTTP_HOST"] != "farmdata.dickinson.edu" &&
    $_SERVER["HTTP_HOST"] != "farmdatadev.dickinson.edu") {
   if ($pass1 != "") {
      if ($pass1 != $pass2) {
         echo "Error: passwords do not match.";
         die();
      } else {
         $cpass = escapehtml(crypt($pass1, '123salt'));
         $sql = "update ext_users set passwd=:cpass where username=:user";
         try {
            $stmt = $dbcon->prepare($sql);
            $stmt->bindParam(':user', $user, PDO::PARAM_STR);
            $stmt->bindParam(':cpass', $cpass, PDO::PARAM_STR);
            $stmt->execute();
         } catch (PDOException $p) {
            echo $p->getMessage();
            $dbcon->rollBack();
            die();
         }
      }
   }
}

$dbcon->commit();
echo "success!";
?>
