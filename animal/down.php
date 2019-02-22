<?php
include $_SERVER['DOCUMENT_ROOT'].'/animal/connection.php';
header("Content-type: application/octet-stream");
header("Content-disposition: attachment;filename=\"report.csv\"");
try {
  $result=$dbcon->query($_POST['query']);
} catch (PDOException $p) {
   die($p->getMessage());
}
$first=true;
while ($row1 =  $result->fetch(PDO::FETCH_ASSOC)){
   if ($first) {
      $first=false;
      $head=array_keys($row1);
      for ($i=0;$i<count($head);$i=$i + 1) {
           echo "\"".$head[$i]."\"".",";
      }

      echo "\n";
   }
   for ($i=0;$i<count($head);$i=$i+1) {
//      echo "\"".str_replace("\r", ";", str_replace("<br>", ";", 
//         htmlspecialchars_decode($row1[$head[$i]], ENT_QUOTES)))."\"".",";
      echo "\"".preg_replace("/\r<br>|<br>\r|\r|<br>/", ";", 
         htmlspecialchars_decode($row1[$head[$i]], ENT_QUOTES))."\"".",";
   }
   
   echo "\n";
}

?>
