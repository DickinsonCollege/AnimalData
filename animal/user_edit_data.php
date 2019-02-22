<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$arr = array();

$sql = "select username from users";
$res = $dbcon->query($sql);
$con = "";
$first = true;
$firstuser = "";
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   $user = $row['username'];
   if ($first) {
      $firstuser = $user;
      $first = false;
   }
   $con .= "<option value='".$user."'>".$user."</option>";
}

$arr['usernames'] = $con;

$sql = "select * from users where username = '".$firstuser."'";
$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   $arr['user'] = $row;
   echo json_encode($arr);
} else {
   echo "Error: no such user.";
}
?>
