<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$user = strtolower(escapehtml($_POST['add_user_name']));
$admin = $_POST['add_user_admin'];

if ($user == "") {
   echo "Error: enter a username.";
   die();
}

$dbcon->beginTransaction();
$sql = "insert into users(username, admin, active) values (:user, :admin, 1)";
try {
   $stmt = $dbcon->prepare($sql);
   $stmt->bindParam(':user', $user, PDO::PARAM_STR);
   $stmt->bindParam(':admin', $admin, PDO::PARAM_INT);
   $stmt->execute();
} catch (PDOException $p) {
   echo $p->getMessage();
   $dbcon->rollBack();
   die();
}

if ($_SERVER["HTTP_HOST"] != "farmdata.dickinson.edu" &&
    $_SERVER["HTTP_HOST"] != "farmdatadev.dickinson.edu") {
   $pass1 = escapehtml($_POST['add_user_pass1']);
   $pass2 = escapehtml($_POST['add_user_pass2']);

   if ($pass1 == "") {
      echo "Error: enter a password.";
      die();
   }

   if ($pass2 == "") {
      echo "Error: enter password twice.";
      die();
   }

   if ($pass1 != $pass2) {
      echo "Error: passwords do not match.";
      die();
   }

   $cpass = escapehtml(crypt($pass1, '123salt'));
   $sql = "insert into ext_users(username, passwd) values (:user, :cpass)";
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

$dbcon->commit();
echo "success!";
?>
