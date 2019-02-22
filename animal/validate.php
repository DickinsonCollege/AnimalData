<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'].'/animal/util.php';
$dbcon = new PDO('mysql:host=localhost;dbname=critterdb', 'critter', 'critterpass',
    array(PDO::MYSQL_ATTR_INIT_COMMAND => 'set sql_mode="TRADITIONAL"'));
$dbcon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pass = escapehtml(crypt($_POST['pass'], '123salt'));
$user = $_POST['username'];
$user = escapehtml($user);
$sql = "select users.*, passwd from users, ext_users ".
       "where users.username = ext_users.username and users.username = '".
       $user."'";
try {
  $result = $dbcon->query($sql);
} catch (PDOException $p) {
   die("Could not connect to database: ".$p->getMessage());
}

$ct = 0;
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $userpass = $row['passwd'];
   $admin = $row['admin'];
   $active = $row['active'];
   $ct = $ct + 1;
}
if ($ct == 0) {
    echo "No such user: ".$user;
    echo "<br clear = \"all\">";
    echo '<a href="extlogin.php">Try again</a>'; 
} else if ($ct == 1) {
echo "<br>";
    if ($pass == $userpass) { 
       if ($active) {
          $_SESSION['dbuser'] = "critter";
          $_SESSION['admin'] = $admin;
          $_SESSION['user'] = $user;
          $_SESSION['dbpass'] = "critterpass";
          $_SESSION['db'] = "critterdb";
          header("Location: home.php");
       } else {
          echo "<script>alert(\"Fatal error: user account is not active\");</script>";
       }
    } else {
       echo "Invalid password for: ".$user;
       echo "<br clear = \"all\">";
       echo '<a href="extlogin.php">Try again</a>'; 
    }
} else {
    echo "<script>alert(\"Fatal error: duplicate username\");</script>";
}
?>
