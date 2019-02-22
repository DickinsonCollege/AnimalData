<?php
function escapehtml($input){
   return htmlspecialchars($input, ENT_QUOTES);
}

function escapeescapehtml($input) {
   return htmlspecialchars_decode($input, ENT_QUOTES);
}

function encodeURIComponent($str) {
$revert = array('%21'=>'!', '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')');
return strtr(rawurlencode($str), $revert);
}

function humanDate($dt) {
   $dtArr = explode("-", $dt);
   $sqlDate = $dtArr[1]."/".$dtArr[2]."/".$dtArr[0];
   return $sqlDate;
}

function upload($id) {
   $newfile = "";
   if (!isset($_FILES[$id])) {
      return "success!";
   } else if (isset($_FILES[$id]) && isset($_FILES[$id]['error']) &&
              $_FILES[$id]['error'] == 1) {
      return "File too large to upload!";
   } else if (isset($_FILES[$id]) && isset($_FILES[$id]['tmp_name']) &&
              $_FILES[$id]['tmp_name'] != "") {
      $fname = 'files/'.$_FILES[$id]['name'];
      if (file_exists($fname)) {
         return "File ".$fname." already exists - try a different file name.";
      }
      if (!move_uploaded_file($_FILES[$id]['tmp_name'], $fname)) {
         return "Error uploading file.";
      }
      return "success!";
   } else {
      return "success!";
   }
}

function print_name($id, $name) {
   $res = $id;
   if ($name != "") {
      $res .= " (".$name.")";
   }
   return $res;
}

?>
