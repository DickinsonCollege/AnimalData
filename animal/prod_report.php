<?php
include $_SERVER['DOCUMENT_ROOT']."/animal/connection.php";

$fyear = $_POST['herd_prod_from_year'];
$fday = $_POST['herd_prod_from_day'];
$fmth = $_POST['herd_prod_from_month'];
$tyear = $_POST['herd_prod_to_year'];
$tday = $_POST['herd_prod_to_day'];
$tmth = $_POST['herd_prod_to_month'];

$sqlFrom = $fyear."-".$fmth."-".$fday;
$sqlTo = $tyear."-".$tmth."-".$tday;

$sql = "select animal_group from animal_group";
$grp = array();
$res = $dbcon->query($sql);
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   $grp[$row['animal_group']] = 0;
}

$sql = "select animal_group, count(*) as cnt from animal ".
       "where birthdate between '".$sqlFrom."' and '".$sqlTo.
       "' group by animal_group";
$born = $grp;
$res = $dbcon->query($sql);
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   $born[$row['animal_group']] = $row['cnt'];
}

$sold = $grp;
$total = $grp;
$sql = "select animal_group, count(*) as cnt from sale, animal ".
       "where animal.animal_id = sale.animal_id and sale_date between '".
        $sqlFrom."' and '".$sqlTo."' group by animal_group";
$res = $dbcon->query($sql);
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   $sold[$row['animal_group']] = $row['cnt'];
   $total[$row['animal_group']] += $row['cnt'];
}

$slay = $grp;
$sql = "select animal_group, count(*) as cnt from slaughter, animal ".
       "where animal.animal_id = slaughter.animal_id and slay_date between '".
        $sqlFrom."' and '".$sqlTo."' group by animal_group";
$res = $dbcon->query($sql);
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   $slay[$row['animal_group']] = $row['cnt'];
   $total[$row['animal_group']] += $row['cnt'];
}

$other = $grp;
$sql = "select animal_group, count(*) as cnt from other_remove, animal ".
       "where animal.animal_id = other_remove.animal_id and remove_date between '".
        $sqlFrom."' and '".$sqlTo."' group by animal_group";
$res = $dbcon->query($sql);
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   $other[$row['animal_group']] = $row['cnt'];
   $total[$row['animal_group']] += $row['cnt'];
}


$alive = $grp;
$sql = "select animal_group, count(*) as cnt from animal ".
       "where alive = 1 group by animal_group";
$res = $dbcon->query($sql);
while ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   $alive[$row['animal_group']] = $row['cnt'];
}


echo "<div id = 'herd_prod_scroll'>&nbsp;</div>";

echo "<h2>Animal Summary</h2>";
echo "<div class='tablediv'>";
echo "<table border data-role='table' class='ui-responsive'>";
echo "<thead><tr><th>Animal Group</th><th>Number Born</th>".
     "<th>Number Sold</th><th>Number Slaughtered</th>".
     "<th>Number Otherwise Removed</th><th>Total Number Removed</th>";
echo "<th>Total on Farm</th></tr></thead>";
foreach ($grp as $g => $n) {
   echo "<tr><td>".$g."</td><td>".$born[$g]."</td><td>".$sold[$g].
        "</td><td>".$slay[$g]."</td><td>".$other[$g]."</td><td>".$total[$g].
        "</td><td>".$alive[$g]."</td></tr>";
} 
echo "</table>";
echo "</div>";

echo "<div>&nbsp;</div>";
$sql = "select sum(number) as num from egg_log where coll_date between '".
       $sqlFrom."' and '".$sqlTo."'";
$res = $dbcon->query($sql);
if ($row = $res->fetch(PDO::FETCH_ASSOC)) {
   echo "<h2>Total Eggs Collected: ".$row['num']."</h2>";
} else {
   echo "<h2>No Eggs Collected</h2>";
}

echo "<script>";
echo "$('html,body').animate({scrollTop: $('#herd_prod_scroll').offset().top });";
echo "</script>";
?>
