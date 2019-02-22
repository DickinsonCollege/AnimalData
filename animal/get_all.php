<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$group = escapehtml($_GET['group']);
$any = escapehtml($_GET['any']);
$arr = array();

$sql = "select breed from breed where animal_group like '".$group.
       "' and active = 1";
$result = $dbcon->query($sql);
$con = "";
if ($any != "false") {
   $con .= "<option value='%'>ALL</option>";
}
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['breed']."'>".$row['breed']."</option>";
}

$arr['breed'] = $con;

$sql = "select animal_id, name from animal where animal_group like '".$group.
       "' and gender = 'F' and alive = 1 order by animal_id";
$result = $dbcon->query($sql);
$con = "";
if ($any != "false") {
   $con .= "<option value='%'>ALL</option>";
}
$con .= "<option value='N/A'>N/A</option>";
$con .= "<optgroup label='On Farm'>";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['animal_id']."'>".
        print_name($row['animal_id'], $row['name'])."</option>";
}
$con .= "</optgroup'>";

$sql = "select animal_id, name from animal where animal_group like '".$group.
       "' and gender = 'F' and alive = 0 order by animal_id";
$result = $dbcon->query($sql);
$con .= "<optgroup label='Not On Farm'>";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['animal_id']."'>".
        print_name($row['animal_id'], $row['name'])."</option>";
}
$con .= "</optgroup'>";

$arr['mother'] = $con;

$sql = "select animal_id, name from animal where animal_group like '".$group.
       "' and gender = 'M' and alive = 1 order by animal_id";
$result = $dbcon->query($sql);
$con = "";
if ($any != "false") {
   $con .= "<option value='%'>ALL</option>";
}
$con .= "<option value='N/A'>N/A</option>";
$con .= "<optgroup label='On Farm'>";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['animal_id']."'>".
        print_name($row['animal_id'], $row['name'])."</option>";
}
$con .= "</optgroup'>";

$sql = "select animal_id, name from animal where animal_group like '".$group.
       "' and gender = 'M' and alive = 0 order by animal_id";
$result = $dbcon->query($sql);
$con .= "<optgroup label='Not On Farm'>";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['animal_id']."'>".
        print_name($row['animal_id'], $row['name'])."</option>";
}
$con .= "</optgroup'>";

$arr['father'] = $con;

$con = "";
$sql = "select animal_id, name from animal where animal_group like '".$group.
   "' and alive = 1 order by animal_id";
$result = $dbcon->query($sql);
$con .= "<option value='%'>ALL</option>";
$con .= "<optgroup label='On Farm'>";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['animal_id']."'>".
        print_name($row['animal_id'], $row['name'])."</option>";
}
$con .= "</optgroup'>";
$sql = "select animal_id, name from animal where animal_group like '".$group.
   "' and alive = 0 order by animal_id";
$result = $dbcon->query($sql);
$con .= "<optgroup label='Not On Farm'>";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['animal_id']."'>".
        print_name($row['animal_id'], $row['name'])."</option>";
}
$con .= "</optgroup'>";

$arr['id'] = $con;

$sql = "select origin from origin where active = 1";
$result = $dbcon->query($sql);
$con = "";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['origin']."'>".$row['origin']."</option>";
}

$arr['origin'] = $con;

$sql = "select distinct sub_group from sub_group where animal_group like '".
       $group."' and active = 1";
$con = "";
if ($any != "false") {
   $con .= "<option value='%'>ALL</option>";
}
$result = $dbcon->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
   $con .= "<option value='".$row['sub_group']."'>".
        $row['sub_group']."</option>";
}

$arr['sub_group'] = $con;

echo json_encode($arr);


?>
