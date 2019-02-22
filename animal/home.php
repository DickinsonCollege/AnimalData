<?php
if($_SERVER["HTTPS"] != "on") {
header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
exit();
}
// HTTPSOFF
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Dickinson AnimalData</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!--
<link href="apple-touch-icon.png" rel="apple-touch-icon"/>
-->

<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>

<script type="text/javascript" src="jqplot/jquery.jqplot.js"></script>
<script type="text/javascript" src="jqplot/plugins/jqplot.barRenderer.js"></script>
<script type="text/javascript" src="jqplot/plugins/jqplot.categoryAxisRenderer.js"></script>
<script type="text/javascript" src="jqplot/plugins/jqplot.pointLabels.js"></script>
<script type="text/javascript" src="jqplot/plugins/jqplot.dateAxisRenderer.js"></script>
<script type="text/javascript" src="jqplot/plugins/jqplot.canvasTextRenderer.js"></script>
<script type="text/javascript" src="jqplot/plugins/jqplot.canvasAxisTickRenderer.js"></script>
<script type="text/javascript" src="jqplot/plugins/jqplot.canvasAxisLabelRenderer.js"></script>
<link rel="stylesheet" type="text/css" href="jqplot/jquery.jqplot.css" />

<script src="functions.js"></script>
<link rel="stylesheet" href="style.css">
<script>
$.jqplot.config.enablePlugins = true;
</script>
</head>
<body>

<div data-role="page" id="home">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
   <?php
   if ($_SESSION['admin']) {
      echo '<li> <a href="#admin" data-icon="gear">Admin</a>';
   }
   ?>
   <li> <a href="#logout" data-icon="power" onclick="logout();">Log Out</a>
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<h1>Dickinson College AnimalData System, version 0.1</h1>
<div>&nbsp;</div>
<center>
<div id="home_div">
<img src="animaldata.png" style="max-width:100%;height:auto;">
</div>
</center>
</div>
</div>

<div data-role="page" id="animal">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#birth">Add</a>
      <li> <a href="#death">Remove</a>
      </ul>
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<?php
if ($_SESSION['admin']) {
   echo '
<div data-role="page" id="admin">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_add">Add</a>
      <li> <a href="#admin_edit">Edit</a>
      <li> <a href="#admin_view">View</a>
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="admin_view">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_view">View</a>
         <ul> 
         <li> <a href="#admin_view_animal" onclick="init_animal_record();">
              Animal</a>
         <li> <a href="#admin_view_prod" onclick="init_prod_record();">
              Productivity</a>
         <li> <a href="#admin_view_health" onclick="init_health_record();">
              Health</a>
         <li> <a href="#admin_view_paddock" onclick="init_paddock_record();">
              Paddock</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="admin_view_prod">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_view">View</a>
         <ul> 
         <li> <a href="#admin_view_prod" onclick="init_prod_record();">
              Productivity</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="herd_prod_form">
<h1>Productivity</h1>

<div class="ui-field-contain">
<label for="herd_prod_from">From:</label>
<fieldset class="ui-grid-b" id="herd_prod_from">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="herd_prod_to">To:</label>
<fieldset class="ui-grid-b" id="herd_prod_to">
</fieldset>
</div>

<div>&nbsp;</div>
<input type="submit" value="Submit" id="submit_herd_prod">

<div id="herd_prod_table"></div>
</form>
</div>
</div>

<div data-role="page" id="admin_view_health">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_view">View</a>
         <ul> 
         <li> <a href="#admin_view_health" onclick="init_health_record();">
              Health</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="herd_health_form">
<h1>Herd Health</h1>

<div class="ui-field-contain">
<label for="herd_health_from">From:</label>
<fieldset class="ui-grid-b" id="herd_health_from">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="herd_health_to">To:</label>
<fieldset class="ui-grid-b" id="herd_health_to">
</fieldset>
</div>

<input type="submit" value="Submit" id="submit_herd_health">

<div id="herd_health_table"></div>
</form>
</div>
</div>

<div data-role="page" id="admin_view_paddock">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_view">View</a>
         <ul> 
         <li> <a href="#admin_view_paddock" onclick="init_paddock_record();">
              Paddock</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<h1>Paddocks</h1>
<div id="paddock_table"></div>
</div>
</div>

<div data-role="page" id="admin_view_animal">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_view">View</a>
         <ul> 
         <li> <a href="#admin_view_animal" onclick="init_animal_record();">
              Animal</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">

<form id="animal_record_form">

<h1>Animal Record</h1>

<div class="ui-field-contain">
<label for="animal_record_id">Animal ID:</label>
<select id="animal_record_id" name="animal_record_id"
        onchange="show_animal_record();">
</select>
</div>

<div>&nbsp;</div>
<div id="animal_record_div"></div>

</form>
</div>
</div>

<div data-role="page" id="admin_edit">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_animal">Animal</a>
         <li> <a href="#edit_vet">Care</a>
         <li> <a href="#edit_feed">Feed</a>
         <li> <a href="#edit_other">Other</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="edit_feed">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_feed">Feed</a>
            <ul> 
            <li> <a href="#edit_feed_type" onclick="init_edit_feed_type();">
                  Type</a>
            <li> <a href="#edit_feed_subtype" onclick="init_edit_feed_subtype();">
                  Type Details</a>
            <li> <a href="#edit_feed_vendor" onclick="init_edit_vendor();">
                  Vendor</a>
            <li> <a href="#edit_feed_unit" onclick="init_edit_feed_unit();">
                  Unit</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="edit_feed_unit">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_feed">Feed</a>
            <ul> 
            <li> <a href="#edit_feed_unit" onclick="init_edit_feed_unit();">
                  Unit</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="edit_feed_unit_form">

<h1>Feed Unit Edit Form</h1>

<div class="ui-field-contain">
<label for="edit_feed_unit_unit">Feed Unit:</label>
<select id="edit_feed_unit_unit" name="edit_feed_unit_unit"
        onchange="update_feed_unit_active(\'edit_feed_unit\');">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_feed_unit_newunit">New Feed Unit Name
  (leave blank if no change):
</label>
<input type="text" id="edit_feed_unit_newunit" name="edit_feed_unit_newunit">
</div>

<div class="ui-field-contain">
<label for="edit_feed_unit_active">Active:</label>
<select id="edit_feed_unit_active" name="edit_feed_unit_active">
<option value="1">Yes</option>
<option value="0">No</option>
</select>
</div>

<div id="edit_feed_unit_notification">&nbsp;</div>
<input type="submit" value="Submit" id="submit_edit_feed_unit">
<input type="button" value="Cancel" onclick="cancel(\'#edit_feed\');">
</form>

</div>
</div>

<div data-role="page" id="edit_feed_vendor">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_feed">Feed</a>
            <ul> 
            <li> <a href="#edit_feed_vendor" onclick="init_edit_vendor();">
                  Vendor</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="edit_feed_vendor_form">

<h1>Feed Vendor Edit Form</h1>

<div class="ui-field-contain">
<label for="edit_vendor_vendor">Vendor:</label>
<select id="edit_vendor_vendor" name="edit_vendor_vendor"
        onchange="update_vendor_active(\'edit_vendor\');">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_vendor_newvendor">New Vendor Name
  (leave blank if no change):
</label>
<input type="text" id="edit_vendor_newvendor" name="edit_vendor_newvendor">
</div>

<div class="ui-field-contain">
<label for="edit_vendor_active">Active:</label>
<select id="edit_vendor_active" name="edit_vendor_active">
<option value="1">Yes</option>
<option value="0">No</option>
</select>
</div>

<div id="edit_vendor_notification">&nbsp;</div>
<input type="submit" value="Submit" id="submit_edit_vendor">
<input type="button" value="Cancel" onclick="cancel(\'#edit_feed\');">
</form>

</div>
</div>

<div data-role="page" id="edit_feed_type">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_feed">Feed</a>
            <ul> 
            <li> <a href="#edit_feed_type" onclick="init_edit_feed_type();">
                  Type</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="edit_feed_type_form">

<h1>Feed Major Type Edit Form</h1>

<div class="ui-field-contain">
<label for="edit_feed_type_type">Feed Major Type:</label>
<select id="edit_feed_type_type" name="edit_feed_type_type"
        onchange="update_feed_type_active(\'edit_feed_type\');">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_feed_type_newfeed_type">New Major Feed Type Name
  (leave blank if no change):
</label>
<input type="text" id="edit_feed_type_newfeed_type" name="edit_feed_type_newfeed_type">
</div>

<div class="ui-field-contain">
<label for="edit_feed_type_active">Active:</label>
<select id="edit_feed_type_active" name="edit_feed_type_active">
<option value="1">Yes</option>
<option value="0">No</option>
</select>
</div>

<div id="edit_feed_type_notification">&nbsp;</div>
<input type="submit" value="Submit" id="submit_edit_feed_type">
<input type="button" value="Cancel" onclick="cancel(\'#edit_feed\');">
</form>

</div>
</div>

<div data-role="page" id="edit_feed_subtype">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_feed">Feed</a>
            <ul> 
            <li> <a href="#edit_feed_subtype" onclick="init_edit_feed_subtype();">
                  Type Details</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="edit_feed_subtype_form">

<h1>Feed Type Details Edit Form</h1>

<div class="ui-field-contain">
<label for="edit_feed_subtype_type">Feed Major Type:</label>
<select id="edit_feed_subtype_type" name="edit_feed_subtype_type"
        onchange="update_feed_subtype(\'edit_feed_subtype\');
                  update_feed_subtype_active(\'edit_feed_subtype\');">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_feed_subtype_subtype">Feed Type Details:</label>
<select id="edit_feed_subtype_subtype" name="edit_feed_subtype_subtype"
        onchange="update_feed_subtype_active(\'edit_feed_subtype\');">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_feed_subtype_newfeed_subtype">New Feed Type Details Name
  (leave blank if no change):
</label>
<input type="text" id="edit_feed_subtype_newfeed_subtype" 
       name="edit_feed_subtype_newfeed_subtype">
</div>

<div class="ui-field-contain">
<label for="edit_feed_subtype_active">Active:</label>
<select id="edit_feed_subtype_active" name="edit_feed_subtype_active">
<option value="1">Yes</option>
<option value="0">No</option>
</select>
</div>

<div id="edit_feed_subtype_notification">&nbsp;</div>
<input type="submit" value="Submit" id="submit_edit_feed_subtype">
<input type="button" value="Cancel" onclick="cancel(\'#edit_feed\');">
</form>

</div>
</div>

<div data-role="page" id="edit_vet">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_vet">Care</a>
            <ul> 
            <li> <a href="#edit_reason" onclick="init_edit_reason();">
                  Reason</a>
            <li> <a href="#edit_med" onclick="init_edit_med();">
                 Medication</a>
            <li> <a href="#edit_wormer" onclick="init_edit_wormer();">
                 Wormer</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="edit_reason">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_vet">Care</a>
            <ul> 
            <li> <a href="#edit_reason" onclick="init_edit_reason();">
                  Reason</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="edit_reason_form">

<h1>Reason Edit Form</h1>

<div class="ui-field-contain">
<label for="edit_reason_reason">Reason:</label>
<select id="edit_reason_reason" name="edit_reason_reason"
        onchange="update_reason_active(\'edit_reason\');">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_reason_newreason">New Reason Name (leave blank if no change):
</label>
<input type="text" id="edit_reason_newreason" name="edit_reason_newreason">
</div>

<div class="ui-field-contain">
<label for="edit_reason_active">Active:</label>
<select id="edit_reason_active" name="edit_reason_active">
<option value="1">Yes</option>
<option value="0">No</option>
</select>
</div>

<div id="edit_reason_notification">&nbsp;</div>
<input type="submit" value="Submit" id="submit_edit_reason">
<input type="button" value="Cancel" onclick="cancel(\'#edit_vet\');">

</form>
</div>
</div>

<div data-role="page" id="edit_med">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_vet">Care</a>
            <ul> 
            <li> <a href="#edit_med" onclick="init_edit_med();">
                  Medication</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="edit_med_form">

<h1>Medication Edit Form</h1>

<div class="ui-field-contain">
<label for="edit_med_med">Medication:</label>
<select id="edit_med_med" name="edit_med_med"
        onchange="update_edit_med();">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_med_newmed">New Medication Name (leave blank if no change):
</label>
<input type="text" id="edit_med_newmed" name="edit_med_newmed">
</div>

<div class="ui-field-contain">
<label for="edit_med_dose">Dosage:
</label>
<input type="text" id="edit_med_dose" name="edit_med_dose">
</div>

<div class="ui-field-contain">
<label for="edit_med_active">Active:</label>
<select id="edit_med_active" name="edit_med_active">
<option value="1">Yes</option>
<option value="0">No</option>
</select>
</div>

<div id="edit_med_notification">&nbsp;</div>
<input type="submit" value="Submit" id="submit_edit_med">
<input type="button" value="Cancel" onclick="cancel(\'#edit_vet\');">

</form>
</div>
</div>

<div data-role="page" id="edit_wormer">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_vet">Care</a>
            <ul> 
            <li> <a href="#edit_wormer" onclick="init_edit_wormer();">
                  Wormer</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="edit_wormer_form">

<h1>Wormer Edit Form</h1>

<div class="ui-field-contain">
<label for="edit_wormer_wormer">Wormer:</label>
<select id="edit_wormer_wormer" name="edit_wormer_wormer"
        onchange="update_edit_wormer();">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_wormer_newwormer">New Wormer Name (leave blank if no change):
</label>
<input type="text" id="edit_wormer_newwormer" name="edit_wormer_newwormer">
</div>

<div class="ui-field-contain">
<label for="edit_wormer_active">Active:</label>
<select id="edit_wormer_active" name="edit_wormer_active">
<option value="1">Yes</option>
<option value="0">No</option>
</select>
</div>

<div id="edit_wormer_notification">&nbsp;</div>
<input type="submit" value="Submit" id="submit_edit_wormer">
<input type="button" value="Cancel" onclick="cancel(\'#edit_vet\');">

</form>
</div>
</div>

<div data-role="page" id="edit_other">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_other">Other</a>
            <ul> 
            <li> <a href="#edit_paddock" onclick="init_edit_paddock();">
                  Paddock</a>
            <li> <a href="#edit_forage" onclick="init_edit_forage();">
                 Forage</a>
            <li> <a href="#edit_task" onclick="init_edit_task();">
                 Task</a>
            <li> <a href="#edit_move_report" 
                    onclick="move_report_init(\'edit_move_report\');">
                 Move</a>
            <li> <a href="#edit_user" onclick="edit_user_init();"> User</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="edit_user">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_other">Other</a>
            <ul> 
            <li> <a href="#edit_user" onclick="edit_user_init();"> User</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="edit_user_form">

<h1>Edit User</h1>

<div class="ui-field-contain">
<label for="edit_user_name">Username:</label>
<select id="edit_user_name" name="edit_user_name"
        onchange="update_user();">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_user_admin">Admin:</label>
<select id="edit_user_admin" name="edit_user_admin">
<option value="0">No</option>
<option value="1">Yes</option>
</select>
</div>

<div class="ui-field-contain">
<label for="edit_user_active">Active:</label>
<select id="edit_user_active" name="edit_user_active">
<option value="1">Yes</option>
<option value="0">No</option>
</select>
</div>';

if ($_SERVER["HTTP_HOST"] != "farmdata.dickinson.edu" &&
    $_SERVER["HTTP_HOST"] != "farmdatadev.dickinson.edu") {
   echo '
      <div class="ui-field-contain">
      <label for="edit_user_pass1">New Password (leave blank if no change):
      </label>
      <input type="password" id="edit_user_pass1" name="edit_user_pass1">
      </div>';

   echo '
      <div class="ui-field-contain">
      <label for="edit_user_pass2">Retype Password (leave blank if no change):
      </label>
      <input type="password" id="edit_user_pass2" name="edit_user_pass2">
      </div>';
}
echo '
<div id="edit_user_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_edit_user">
</form>
</div>
</div>

<div data-role="page" id="edit_move_report">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_other">Other</a>
            <ul> 
            <li> <a href="#edit_move_report" 
                  onclick="move_report_init(\'edit_move_report\');">
                 Move</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="edit_move_report_form">
<h1>Edit Grazing Move Record</h1>

<div class="ui-field-contain">
<label for="edit_move_report_from">From:</label>
<fieldset class="ui-grid-b" id="edit_move_report_from">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="edit_move_report_to">To:</label>
<fieldset class="ui-grid-b" id="edit_move_report_to">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="edit_move_report_group">Animal Group:</label>
<select id="edit_move_report_group" name="edit_move_report_group" 
   onchange="init_all(\'edit_move_report\', true);">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_move_report_subgroup">Subgroup:</label>
<select id="edit_move_report_subgroup" name="edit_move_report_subgroup">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_move_report_paddock">Paddock ID::</label>
<select id="edit_move_report_paddock" name="edit_move_report_paddock">
</select>
</div>

<div>&nbsp;</div>
<input type="submit"  value="Submit" id="submit_edit_move_report">

<div id="edit_move_table">&nbsp;</div>

</form>
</div>
</div>

<div data-role="page" id="edit_move">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_other">Other</a>
            <ul> 
            <li> <a href="#edit_move_report" 
                    onclick="move_report_init(\'edit_move_report\');">
                 Move</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>

<div data-role="main" class="ui-content">
<form id="edit_move_form">
<h1>Grazing Move Edit Form</h1>

<input type="hidden" id="edit_move_edit_auto_id"
       name="edit_move_edit_auto_id">

<div class="ui-field-contain">
<label for="edit_move_date">Date:</label>
<fieldset class="ui-grid-b" id="edit_move_date">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="edit_move_group">Animal Group:</label>
<select name="edit_move_group" id="edit_move_group"
        onchange="update_subgroup(\'edit_move\', true);">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_move_subgroup">Subgroup:</label>
<select name="edit_move_subgroup" id="edit_move_subgroup"
        onchange="update_move_paddock(\'edit_move\');">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_move_move">Move:</label>
<select name="edit_move_move" id="edit_move_move"
        onchange="update_move_paddock(\'edit_move\');">
<option value="1">To</option>
<option value="0">From</option>
</select>
</div>

<div class="ui-field-contain">
<label for="edit_move_paddock">Paddock ID:</label>
<select name="edit_move_paddock" id="edit_move_paddock">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_move_forage">Forage:</label>
<select name="edit_move_forage" id="edit_move_forage">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_move_height">Forage Height (inches):</label>
<select name="edit_move_height" id="edit_move_height">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_move_density">Forage Density:</label>
<select name="edit_move_density" id="edit_move_density">
<option value="1">1: BARE SPOTS</option>
<option value="2">2: THIN</option>
<option value="3">3: MODERATE</option>
<option value="4">4: FULL</option>
<option value="5">5: LUSH</option>
</select>
</div>

<div class="ui-field-contain" id="edit_move_comments_div">
<label for="edit_move_comments">Comments:</label>
<textarea id="edit_move_comments" name="edit_move_comments">
</textarea>
</div>

<div id="edit_move_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_edit_move">

</form>
</div>
</div>

<div data-role="page" id="edit_forage">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_other">Other</a>
            <ul> 
            <li> <a href="#edit_forage" onclick="init_edit_forage();">
                 Forage</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="edit_forage_form">

<h1>Forage Edit Form</h1>

<div class="ui-field-contain">
<label for="edit_forage_forage">Forage:</label>
<select id="edit_forage_forage" name="edit_forage_forage"
        onchange="update_edit_forage();">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_forage_newforage">New Forage Name (leave blank if no change):
</label>
<input type="text" id="edit_forage_newforage" name="edit_forage_newforage">
</div>

<div class="ui-field-contain">
<label for="edit_forage_density">Density (lbs. per acre-inch):</label>
<input type="number" min="0.01" step="0.01" id="edit_forage_density" 
       name="edit_forage_density">
</div>

<div class="ui-field-contain">
<label for="edit_forage_active">Active:</label>
<select id="edit_forage_active" name="edit_forage_active">
<option value="1">Yes</option>
<option value="0">No</option>
</select>
</div>

<div id="edit_forage_notification">&nbsp;</div>
<input type="submit" value="Submit" id="submit_edit_forage">
<input type="button" value="Cancel" onclick="cancel(\'#edit_other\');">

</form>
</div>
</div>

<div data-role="page" id="edit_paddock">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_other">Other</a>
            <ul> 
            <li> <a href="#edit_paddock" onclick="init_edit_paddock();">
                 Paddock</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="edit_paddock_form">

<h1>Paddock Edit Form</h1>

<div class="ui-field-contain">
<label for="edit_paddock_paddock">Paddock ID:</label>
<select id="edit_paddock_paddock" name="edit_paddock_paddock"
        onchange="update_edit_paddock();">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_paddock_newpaddock">New Paddock ID (leave blank if no change):
</label>
<input type="text" id="edit_paddock_newpaddock" name="edit_paddock_newpaddock">
</div>

<div class="ui-field-contain">
<label for="edit_paddock_forage">Forage:</label>
<select id="edit_paddock_forage" name="edit_paddock_forage">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_paddock_size">Size (acres):</label>
<input type="number" min="0.01" step="0.01" id="edit_paddock_size" 
       name="edit_paddock_size">
</div>

<div class="ui-field-contain">
<label for="edit_paddock_active">Active:</label>
<select id="edit_paddock_active" name="edit_paddock_active">
<option value="1">Yes</option>
<option value="0">No</option>
</select>
</div>

<div id="edit_paddock_notification">&nbsp;</div>
<input type="submit" value="Submit" id="submit_edit_paddock">
<input type="button" value="Cancel" onclick="cancel(\'#edit_other\');">

</form>
</div>
</div>

<div data-role="page" id="edit_task">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_other">Other</a>
            <ul> 
            <li> <a href="#edit_task" onclick="init_edit_task();">
                 Task</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="edit_task_form">

<h1>Task Edit Form</h1>

<div class="ui-field-contain">
<label for="edit_task_task">Task:</label>
<select id="edit_task_task" name="edit_task_task"
        onchange="update_edit_task();">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_task_newtask">New Task Name (leave blank if no change):
</label>
<input type="text" id="edit_task_newtask" name="edit_task_newtask">
</div>

<div class="ui-field-contain">
<label for="edit_task_active">Active:</label>
<select id="edit_task_active" name="edit_task_active">
<option value="1">Yes</option>
<option value="0">No</option>
</select>
</div>

<div id="edit_task_notification">&nbsp;</div>
<input type="submit" value="Submit" id="submit_edit_task">
<input type="button" value="Cancel" onclick="cancel(\'#edit_other\');">

</form>
</div>
</div>

<div data-role="page" id="edit_animal">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_animal">Animal</a>
            <ul> 
            <li> <a href="#edit_subgroups"
                    onclick="init_group(\'edit_subgroups\', true, null);">
                    Subgroups</a>
            <li> <a href="#edit_add">Add</a>
            <li> <a href="#edit_remove">Remove</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="edit_add">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_animal">Animal</a>
            <ul> 
            <li> <a href="#edit_add">Add</a>
               <ul> 
               <li> <a href="#edit_breed"
                     onclick="init_edit_breed();">Breed</a>
               <li> <a href="#edit_subgroup"
                     onclick="init_edit_subgroup();">Subgroup</a>
               <li> <a href="#edit_origin" onclick="init_origin();">
                     Origin</a>
               </ul> 
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="edit_remove">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_animal">Animal</a>
            <ul> 
            <li> <a href="#edit_remove">Remove</a>
               <ul> 
               <li> <a href="#edit_slayhouse" onclick="init_slayhouse();">
                    Slaughter House</a>
               <li> <a href="#edit_dest_sale" onclick="init_sale_dest();">
                    Sale Dest</a>
               <li> <a href="#edit_dest_other" onclick="init_other_dest();">
                    Other Dest</a>
               <li> <a href="#edit_reason_other" onclick="init_edit_other_reason();">
                    Reason</a>
               </ul> 
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="edit_reason_other">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_animal">Animal</a>
            <ul> 
            <li> <a href="#edit_remove">Remove</a>
               <ul> 
               <li> <a href="#edit_reason_other" onclick="init_edit_other_reason();">
                    Reason</a>
               </ul> 
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="edit_reason_other_form">

<h1>Reason Edit Form</h1>

<div class="ui-field-contain">
<label for="edit_reason_other_reason">Reason:</label>
<select id="edit_reason_other_reason" name="edit_reason_other_reason"
        onchange="update_other_reason_active(\'edit_reason_other\');">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_reason_other_newreason">New Reason Name (leave blank if no change):
</label>
<input type="text" id="edit_reason_other_newreason" 
    name="edit_reason_other_newreason">
</div>

<div class="ui-field-contain">
<label for="edit_reason_other_active">Active:</label>
<select id="edit_reason_other_active" name="edit_reason_other_active">
<option value="1">Yes</option>
<option value="0">No</option>
</select>
</div>

<div id="edit_reason_other_notification">&nbsp;</div>
<input type="submit" value="Submit" id="submit_edit_other_reason">
<input type="button" value="Cancel" onclick="cancel(\'#edit_animal\');">

</form>
</div>
</div>

<div data-role="page" id="edit_slayhouse">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_animal">Animal</a>
            <ul> 
            <li> <a href="#edit_remove">Remove</a>
               <ul> 
               <li> <a href="#edit_slayhouse" onclick="init_slayhouse();">
                     Slaughter House</a>
               </ul> 
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="edit_slayhouse_form">

<h1>Edit Slaughter House</h1>

<div class="ui-field-contain">
<label for="edit_slayhouse_house">Slaughter House:</label>
<select id="edit_slayhouse_house" name="edit_slayhouse_house"
        onchange="update_slayhouse_active(\'edit_slayhouse\');">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_slayhouse_newhouse">New Slaughter House Name 
 (leave blank if no change):
</label>
<input type="text" id="edit_slayhouse_newhouse" name="edit_slayhouse_newhouse">
</div>

<div class="ui-field-contain">
<label for="edit_slayhouse_active">Active:</label>
<select id="edit_slayhouse_active" name="edit_slayhouse_active">
<option value="1">Yes</option>
<option value="0">No</option>
</select>
</div>

<div id="edit_slayhouse_notification">&nbsp;</div>
<input type="submit" value="Submit" id="submit_edit_slayhouse">
<input type="button" value="Cancel" onclick="cancel(\'#edit_remove\');">
</form>
</div>
</div>

<div data-role="page" id="edit_dest_sale">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_animal">Animal</a>
            <ul> 
            <li> <a href="#edit_remove">Remove</a>
               <ul> 
               <li> <a href="#edit_dest_sale" onclick="init_sale_dest();">
                     Sale Dest</a>
               </ul> 
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="edit_dest_sale_form">

<h1>Edit Sale Destination</h1>

<div class="ui-field-contain">
<label for="edit_dest_sale_dest">Destination:</label>
<select id="edit_dest_sale_dest" name="edit_dest_sale_dest"
        onchange="update_dest_sale_active(\'edit_dest_sale\');">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_dest_sale_newdest">New Destination Name 
 (leave blank if no change):
</label>
<input type="text" id="edit_dest_sale_newdest" name="edit_dest_sale_newdest">
</div>

<div class="ui-field-contain">
<label for="edit_dest_sale_active">Active:</label>
<select id="edit_dest_sale_active" name="edit_dest_sale_active">
<option value="1">Yes</option>
<option value="0">No</option>
</select>
</div>

<div id="edit_dest_sale_notification">&nbsp;</div>
<input type="submit" value="Submit" id="submit_edit_dest_sale">
<input type="button" value="Cancel" onclick="cancel(\'#edit_remove\');">
</form>
</div>
</div>

<div data-role="page" id="edit_dest_other">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_animal">Animal</a>
            <ul> 
            <li> <a href="#edit_remove">Remove</a>
               <ul> 
               <li> <a href="#edit_dest_other" onclick="init_other_dest();">
                     Other Dest</a>
               </ul> 
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="edit_dest_other_form">

<h1>Edit Other Removal Destination</h1>

<div class="ui-field-contain">
<label for="edit_dest_other_dest">Destination:</label>
<select id="edit_dest_other_dest" name="edit_dest_other_dest"
        onchange="update_other_dest_active(\'edit_dest_other\');">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_dest_other_newdest">New Destination Name 
 (leave blank if no change):
</label>
<input type="text" id="edit_dest_other_newdest" name="edit_dest_other_newdest">
</div>

<div class="ui-field-contain">
<label for="edit_dest_other_active">Active:</label>
<select id="edit_dest_other_active" name="edit_dest_other_active">
<option value="1">Yes</option>
<option value="0">No</option>
</select>
</div>

<div id="edit_dest_other_notification">&nbsp;</div>
<input type="submit" value="Submit" id="submit_edit_dest_other">
<input type="button" value="Cancel" onclick="cancel(\'#edit_remove\');">
</form>
</div>
</div>

<div data-role="page" id="edit_subgroups">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_animal">Animal</a>
            <ul> 
            <li> <a href="#edit_subgroups"
                    onclick="init_group(\'edit_subgroups\', true, null);">
                    Subgroups</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="edit_subgroups_form">

<h1>Animal Subgroups Edit Form</h1>

<div class="ui-field-contain">
<label for="edit_subgroups_group">Animal Group:</label>
<select id="edit_subgroups_group" name="edit_subgroups_group" 
   onchange="init_all(\'edit_subgroups\', true);">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_subgroups_subgroup">Subgroup:</label>
<select id="edit_subgroups_subgroup" name="edit_subgroups_subgroup">
</select>
</div>

<div>&nbsp;</div>
<input type="submit"  value="Submit" id="submit_edit_subgroups">
<div>&nbsp;</div>
</form>
<div id="subgroups_edit_div">&nbsp;</div>
</div>
</div>

<div data-role="page" id="edit_breed">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_animal">Animal</a>
            <ul> 
            <li> <a href="#edit_add">Add</a>
            <ul> 
               <li> <a href="#edit_breed"
                             onclick="init_edit_breed();">Breed</a>
               </ul> 
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="edit_breed_form">

<h1>Edit Breed</h1>

<div class="ui-field-contain">
<label for="edit_breed_group">Animal Group:</label>
<select id="edit_breed_group" name="edit_breed_group" onchange=
        "update_breed(\'edit_breed\', false);update_breed_active(\'edit_breed\');">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_breed_breed">Breed:</label>
<select id="edit_breed_breed" name="edit_breed_breed"
        onchange="update_breed_active(\'edit_breed\');">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_breed_newbreed">New Breed Name (leave blank if no change):
</label>
<input type="text" id="edit_breed_newbreed" name="edit_breed_newbreed">
</div>

<div class="ui-field-contain">
<label for="edit_breed_active">Active:</label>
<select id="edit_breed_active" name="edit_breed_active">
<option value="1">Yes</option>
<option value="0">No</option>
</select>
</div>

<div id="edit_breed_notification">&nbsp;</div>
<input type="submit" value="Submit" id="submit_edit_breed">
<input type="button" value="Cancel" onclick="cancel(\'#edit_add\');">
</form>
</div>
</div>

<div data-role="page" id="edit_subgroup">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_animal">Animal</a>
            <ul> 
            <li> <a href="#edit_add">Add</a>
               <ul> 
               <li> <a href="#edit_subgroup"
                             onclick="init_edit_subgroup();">Subgroup</a>
               </ul> 
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="edit_subgroup_form">

<h1>Edit Subgroup</h1>

<div class="ui-field-contain">
<label for="edit_subgroup_group">Animal Group:</label>
<select id="edit_subgroup_group" name="edit_subgroup_group" onchange=
        "update_subgroup(\'edit_subgroup\', false);update_subgroup_active(\'edit_subgroup\');">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_subgroup_subgroup">Subgroup:</label>
<select id="edit_subgroup_subgroup" name="edit_subgroup_subgroup"
        onchange="update_subgroup_active(\'edit_subgroup\');">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_subgroup_newsubgroup">New Subgroup Name (leave blank if no change):
</label>
<input type="text" id="edit_subgroup_newsubgroup" name="edit_subgroup_newsubgroup">
</div>

<div class="ui-field-contain">
<label for="edit_subgroup_active">Active:</label>
<select id="edit_subgroup_active" name="edit_subgroup_active">
<option value="1">Yes</option>
<option value="0">No</option>
</select>
</div>

<div id="edit_subgroup_notification">&nbsp;</div>
<input type="submit" value="Submit" id="submit_edit_subgroup">
<input type="button" value="Cancel" onclick="cancel(\'#edit_add\');">
</form>
</div>
</div>

<div data-role="page" id="edit_origin">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_edit">Edit</a>
         <ul> 
         <li> <a href="#edit_animal">Animal</a>
            <ul> 
            <li> <a href="#edit_add">Add</a>
               <ul> 
               <li> <a href="#edit_origin" onclick="init_origin();">
                     Origin</a>
               </ul> 
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="edit_origin_form">

<h1>Edit Origin</h1>

<div class="ui-field-contain">
<label for="edit_origin_origin">Origin:</label>
<select id="edit_origin_origin" name="edit_origin_origin"
        onchange="update_origin_active(\'edit_origin\');">
</select>
</div>

<div class="ui-field-contain">
<label for="edit_origin_neworigin">New Origin Name (leave blank if no change):
</label>
<input type="text" id="edit_origin_neworigin" name="edit_origin_neworigin">
</div>

<div class="ui-field-contain">
<label for="edit_origin_active">Active:</label>
<select id="edit_origin_active" name="edit_origin_active">
<option value="1">Yes</option>
<option value="0">No</option>
</select>
</div>

<div id="edit_origin_notification">&nbsp;</div>
<input type="submit" value="Submit" id="submit_edit_origin">
<input type="button" value="Cancel" onclick="cancel(\'#edit_add\');">
</form>
</div>
</div>

<div data-role="page" id="admin_add">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_add">Add</a>
         <ul> 
         <li> <a href="#add_group" onclick="check_con();">Animal Group</a>
         <li> <a href="#add_paddock" 
               onclick="update_forage(\'add_paddock\', true);">
              Paddock</a>
         <li> <a href="#add_user" onclick="check_con();">User</a>
         <li> <a href="#add_task_list">Task List</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="add_task_list">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_add">Add</a>
         <ul> 
         <li> <a href="#add_task_list">Task List</a>
            <ul> 
               <li> <a href="#add_task_daily" 
                       onclick="init_daily_task();">Daily</a>
               <li> <a href="#add_task_recur"
                       onclick="load_recur_task();">Recurring</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="add_task_recur">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_add">Add</a>
         <ul> 
         <li> <a href="#add_task_list">Task List</a>
            <ul> 
               <li> <a href="#add_task_recur"
                       onclick="load_recur_task();">Recurring</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="recur_task_form">
<h1>Create/Edit Recurring Task List</h1>

<input type=hidden name="num_recur_task_rows" id="num_recur_task_rows" value="0">

<div>&nbsp;</div>
<div class="tablediv">
<table border name="recur_task_table" id="recur_task_table" style="width:100%">
<thead>
<tr><th>Start Date</th><th>Task</th><th>Comments</th><th>Animal<br>Group</th><th>Subgroup</th>
    <th>Workers<br>(Default)</th><th>Default<br>Time<br>(Minutes)<th>Occurs</th><th>Remove</th></tr>
</thead>
<tbody>
</tbody>
</table>
</div>
<input type="button" class="ui-btn" value="Add Task Entry" 
   onclick="add_recur_task_row();">
<input type="button" class="ui-btn" value="Add New Task" 
   onclick="add_task(\'add_task_recur\');">

<div id="add_recur_task_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_add_recur_task">
</form>
</div>
</div>

<div data-role="page" id="add_task_daily">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_add">Add</a>
         <ul> 
         <li> <a href="#add_task_list">Task List</a>
            <ul> 
               <li> <a href="#add_task_daily" 
                       onclick="init_daily_task();">Daily</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="daily_task_form">
<h1>Create/Edit Daily Task List</h1>

<div class="ui-field-contain">
<label for="add_daily_task_date">Date:</label>
<fieldset class="ui-grid-b" id="add_daily_task_date">
</fieldset>
</div>

<input type="button" class="ui-btn" value="Update Task List Date" 
   onclick="update_daily_task_list();">

<input type=hidden name="num_daily_task_rows" id="num_daily_task_rows" value="0">

<div>&nbsp;</div>
<div class="tablediv">
<table border name="daily_task_table" id="daily_task_table" style="width:100%">
<thead>
<tr><th>Task</th><th>Comments</th><th>Animal Group</th><th>Subgroup</th>
    <th>Remove</th></tr>
</thead>
<tbody>
</tbody>
</table>
</div>
<input type="button" class="ui-btn" value="Add Task Entry" 
   onclick="add_daily_task_row(false);">
<input type="button" class="ui-btn" value="Add New Task" 
   onclick="add_task(\'add_task_daily\');">

<div id="add_daily_task_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_add_daily_task">
</form>
</div>
</div>

<div data-role="page" id="add_task">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_add">Add</a>
         <ul> 
         <li> <a href="#add_task_list">Task List</a>
            <ul> 
               <li> <a href="#add_task_daily" 
                       onclick="init_daily_task();">Daily</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="add_task_form">

<h1>Add Task</h1>

<div class="ui-field-contain">
<label for="add_task_task">Task:</label>
<input type="text" id="add_task_task" name="add_task_task">
</div>

<div id="add_task_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_add_task">
<input type="button" value="Cancel" onclick="cancel(\'#add_task_daily\');">
</form>
</div>
</div>

<div data-role="page" id="add_group">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_add">Add</a>
         <ul> 
         <li> <a href="#add_group" onclick="check_con();">Animal Group</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="add_group_form">

<h1>Add Animal Group</h1>

<div class="ui-field-contain">
<label for="add_animal_group">Animal Group:</label>
<input type="text" id="add_animal_group" name="add_animal_group">
</div>

<div id="add_group_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_add_animal_group">
</form>
</div>
</div>

<div data-role="page" id="add_user">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_add">Add</a>
         <ul> 
         <li> <a href="#add_user" onclick="check_con();">User</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="add_user_form">

<h1>Add User</h1>

<div class="ui-field-contain">
<label for="add_user_name">Username:</label>
<input type="text" id="add_user_name" name="add_user_name">
</div>

<div class="ui-field-contain">
<label for="add_user_admin">Admin:</label>
<select id="add_user_admin" name="add_user_admin">
<option value="0">No</option>
<option value="1">Yes</option>
</select>
</div>';

if ($_SERVER["HTTP_HOST"] != "farmdata.dickinson.edu" &&
    $_SERVER["HTTP_HOST"] != "farmdatadev.dickinson.edu") {
   echo '
<div class="ui-field-contain">
<label for="add_user_pass1">Password:</label>
<input type="password" id="add_user_pass1" name="add_user_pass1">
</div>

<div class="ui-field-contain">
<label for="add_user_pass2">Retype Password:</label>
<input type="password" id="add_user_pass2" name="add_user_pass2">
</div>';
} 

echo '
<div id="add_user_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_add_user">
</form>
</div>
</div>

<div data-role="page" id="add_paddock">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_add">Add</a>
         <ul> 
         <li> <a href="#add_paddock" 
               onclick="update_forage(\'add_paddock\', true);">
              Paddock</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="add_paddock_form">

<h1>Add Paddock</h1>

<div class="ui-field-contain">
<label for="add_paddock_id">Paddock ID:</label>
<input type="text" id="add_paddock_id" name="add_paddock_id">
</div>

<div class="ui-field-contain">
<label for="add_paddock_size">Paddock Size (Acres):</label>
<input type="number" min="0.01" step="0.01" id="add_paddock_size" 
       name="add_paddock_size">
</div>

<div class="ui-field-contain">
<label for="add_paddock_forage">Forage:</label>
<select id="add_paddock_forage" name="add_paddock_forage">
</select>
</div>

<input type="button" class="ui-btn" value="Add Forage" 
   onclick="add_forage(\'#add_paddock\', \'add_paddock\');">

<div id="add_paddock_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_add_paddock">
</form>
</div>
</div>

<div data-role="page" id="add_forage">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#admin" data-icon="gear">Admin</a>
      <ul> 
      <li> <a href="#admin_add">Add</a>
         <ul> 
         <li> <a href="#add_paddock" 
               onclick="update_forage(\'add_paddock\', true);">
              Paddock</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="add_forage_form">

<h1>Add Forage</h1>

<div class="ui-field-contain">
<label for="add_forage_forage">Forage:</label>
<input type="text" id="add_forage_forage" name="add_forage_forage">
</div>

<div class="ui-field-contain">
<label for="add_forage_density">Density (lbs. per acre-inch):</label>
<input type="number" min="0.01" step="0.01" id="add_forage_density" 
       name="add_forage_density">
</div>

<div id="add_forage_notification">&nbsp;</div>
<input type="submit" value="Submit" id="submit_add_forage">
<input type="button" value="Cancel" onclick="cancel(\'#add_paddock\');">
</form>
</div>
</div>

<div data-role="page" id="add_breed">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
   <ul> 
      <li> <a href="#birth">Add</a>
         <ul> 
         <li> <a href="#birth_input" onclick="birth_input_init();">Input Form</a>
         </ul> 
    </ul>
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="add_breed_form">

<h1>Add Breed</h1>

<div class="ui-field-contain">
<label for="add_breed_group">Animal Group:</label>
<select id="add_breed_group" name="add_breed_group">
</select>
</div>

<div class="ui-field-contain">
<label for="add_breed_breed">Breed:</label>
<input type="text" id="add_breed_breed" name="add_breed_breed">
</div>

<div id="add_breed_notification">&nbsp;</div>
<input type="submit" value="Submit" id="submit_add_breed">
<input type="button" value="Cancel" onclick="cancel(\'#birth_input\');">
</form>
</div>
</div>

<div data-role="page" id="add_subgroup">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#birth">Add</a>
         <ul> 
         <li> <a href="#birth_input" onclick="birth_input_init();">Input Form</a>
         </ul> 
       </ul>
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="add_subgroup_form">

<h1>Add Subgroup</h1>

<div class="ui-field-contain">
<label for="add_subgroup_group">Animal Group:</label>
<select id="add_subgroup_group" name="add_subgroup_group">
</select>
</div>

<div class="ui-field-contain">
<label for="add_subgroup_subgroup">Subgroup:</label>
<input type="text" id="add_subgroup_subgroup" name="add_subgroup_subgroup">
</div>

<div id="add_subgroup_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_add_subgroup">
<input type="button" value="Cancel" onclick="cancel(\'#birth_input\');">
</form>
</div>
</div>

<div data-role="page" id="add_dest">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#death">Remove</a>
         <ul> 
         <li> <a href="#sale">Sale</a>
            <ul> 
            <li> <a href="#sale_input" 
                  onclick="init_sale_input(\'sale_input\');">Input Form</a>
            </ul>
         </ul>
      </ul>
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="add_dest_form">

<h1>Add Animal Destination</h1>

<div class="ui-field-contain">
<label for="add_animal_dest">Destination:</label>
<input type="text" id="add_animal_dest" name="add_animal_dest">
</div>

<div id="add_animal_dest_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_add_animal_dest">
<input type="button" value="Cancel" onclick="cancel(\'#sale_input\');">
</form>
</div>
</div>

<div data-role="page" id="add_other_reason">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#death">Remove</a>
         <ul> 
         <li> <a href="#other">Other</a>
            <ul> 
            <li> <a href="#other_input" 
                  onclick="init_other_input(\'other_input\');">Input Form</a>
            </ul>
         </ul>
      </ul>
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="add_other_reason_form">

<h1>New Reason Input Form</h1>

<div class="ui-field-contain">
<label for="other_reason">Reason:</label>
<input type="text" id="other_reason" name="other_reason">
</div>

<div id="other_reason_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_other_reason">
<input type="button" value="Cancel" onclick="cancel(\'#other_input\');">

</form>
</div>
</div>

<div data-role="page" id="add_other_dest">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#death">Remove</a>
         <ul> 
         <li> <a href="#other">Other</a>
            <ul> 
            <li> <a href="#other_input" 
                  onclick="init_other_input(\'other_input\');">Input Form</a>
            </ul>
         </ul>
      </ul>
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="add_other_dest_form">

<h1>Add Animal Other Removal Destination</h1>

<div class="ui-field-contain">
<label for="add_other_dest">Destination:</label>
<input type="text" id="add_other_dest" name="add_other_dest">
</div>

<div id="add_other_dest_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_add_other_dest">
<input type="button" value="Cancel" onclick="cancel(\'#other_input\');">
</form>
</div>
</div>

<div data-role="page" id="add_slayhouse">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#death">Remove</a>
         <ul> 
         <li> <a href="#slaughter">Slaughter</a>
            <ul> 
            <li> <a href="#slay_input" 
                  onclick="init_slay_input(\'slay_input\');">Input Form</a>
            </ul>
         </ul>
      </ul>
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="add_slay_form">

<h1>Add Slaughter House</h1>

<div class="ui-field-contain">
<label for="add_animal_slay">Slaughter House:</label>
<input type="text" id="add_animal_slay" name="add_animal_slay">
</div>

<div id="add_animal_slay_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_add_animal_slay">
<input type="button" value="Cancel" onclick="cancel(\'#slay_input\');">
</form>
</div>
</div>

<div data-role="page" id="add_origin">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#birth">Add</a>
         <ul> 
         <li> <a href="#birth_input" onclick="birth_input_init();">Input Form</a>
         </ul> 
       </ul>
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="add_origin_form">

<h1>Add Animal Origin</h1>

<div class="ui-field-contain">
<label for="add_animal_origin">Origin:</label>
<input type="text" id="add_animal_origin" name="add_animal_origin">
</div>

<div id="add_animal_origin_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_add_animal_origin">
<input type="button" value="Cancel" onclick="cancel(\'#birth_input\');">
</form>
</div>
</div>';
}
?>

<div data-role="page" id="death">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
   <ul> 
      <li> <a href="#death">Remove</a>
         <ul> 
         <li> <a href="#sale">Sale</a>
         <li> <a href="#slaughter">Slaughter</a>
         <li> <a href="#other">Other</a>
         </ul>
      </ul>
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="other">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#death">Remove</a>
         <ul> 
         <li> <a href="#other">Other</a>
            <ul> 
            <li> <a href="#other_input" 
                    onclick="init_other_input('other_input');">Input Form</a>
            <li> <a href="#other_report" onclick="init_other_report();">Report</a>
            </ul>
         </ul>
      </ul>
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="other_report">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#death">Remove</a>
         <ul> 
         <li> <a href="#other">Other</a>
            <ul> 
            <li> <a href="#other_report" onclick="init_other_report();">Report</a>
            </ul>
         </ul>
      </ul>
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="other_report_form">

<h1>Other Removal Report</h1>

<div class="ui-field-contain">
<label for="other_report_from">From:</label>
<fieldset class="ui-grid-b" id="other_report_from">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="other_report_to">To:</label>
<fieldset class="ui-grid-b" id="other_report_to">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="other_report_group">Animal Group:</label>
<select id="other_report_group" name="other_report_group">
</select>
</div>

<div class="ui-field-contain">
<label for="other_report_reason">Reason:</label>
<select id="other_report_reason" name="other_report_reason">
</select>
</div>

<div>&nbsp;</div>
<input type="submit" value="Submit" id="other_report_submit">
<div>&nbsp;</div>
<div id="other_report_table"></div>

</form>
</div>
</div>

<div data-role="page" id="other_input">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#death">Remove</a>
         <ul> 
         <li> <a href="#other">Other</a>
            <ul> 
            <li> <a href="#other_input" 
                    onclick="init_other_input('other_input');">Input Form</a>
            </ul>
         </ul>
      </ul>
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">

<form id="other_input_form">

<h1>Other Removal Input Form</h1>

<div class="ui-field-contain">
<label for="other_input_date">Date:</label>
<fieldset class="ui-grid-b" id="other_input_date">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="other_input_id">Animal ID:</label>
<select id="other_input_id" name="other_input_id" onchange="animal_update('other_input');">
</select>
</div>

<div class="ui-field-contain">
<label for="other_input_group">Animal Group:</label>
<input type="text" id="other_input_group" name="other_input_group" readonly>
</div>

<div class="ui-field-contain">
<label for="other_input_name">Name:</label>
<input type="text" id="other_input_name" name="other_input_name" readonly>
</div>

<div class="ui-field-contain">
<label for="other_input_mark">Color &amp; Markings:</label>
<input type="text" id="other_input_mark" name="other_input_mark" readonly>
</div>

<div id="other_input_picture_div"></div>

<div class="ui-field-contain">
<label for="other_input_wt">Final Weight (lbs.):</label>
<input type="text" id="other_input_wt" name="other_input_wt" 
       value="N/A">
</div>

<div class="ui-field-contain">
<label for="other_input_reason">Reason for Removal:</label>
<select id="other_input_reason" name="other_input_reason">
</select>
</div>

<input type="button" class="ui-btn" value="Add Reason"
<?php  if ($_SESSION['admin']) {
   echo "onclick=\"add_other_reason('#other_input', 'other_input');\"";
} else {
   echo "onclick=\"nonAdmin();\"";
}
?>
>

<div class="ui-field-contain">
<label for="other_input_dest">Destination:</label>
<select id="other_input_dest" name="other_input_dest">
</select>
</div>

<input type="button" class="ui-btn" value="Add Destination"
<?php  if ($_SESSION['admin']) {
   echo "onclick=\"add_other_dest('#other_input', 'other_input');\"";
} else {
   echo "onclick=\"nonAdmin();\"";
}
?>
>

<div class="ui-field-contain" id="other_input_comments_div" style="display:none">
<label for="other_input_comments">Comments:</label>
<textarea id="other_input_comments" name="other_input_comments">
</textarea>
</div>

<input type="button" class="ui-btn" value="Show Comments" 
   id="other_input_comments_btn" onclick="toggle('other_input_comments', 'Comments');">

<div id="other_input_notification">&nbsp;</div>

<input type="submit" value="Submit" id="other_input_submit">

</form>

</div>
</div>

<div data-role="page" id="other_edit">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#death">Remove</a>
         <ul> 
         <li> <a href="#other">Other</a>
            <ul> 
            <li> <a href="#other_report" 
                    onclick="init_other_report();">Report</a>
            </ul>
         </ul>
      </ul>
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">

<form id="other_edit_form">

<h1>Other Removal Edit Form</h1>

<input type=hidden name="other_edit_auto_id" id= "other_edit_auto_id">
<input type=hidden name="other_edit_orig_id" id= "other_edit_orig_id">

<div class="ui-field-contain">
<label for="other_edit_date">Date:</label>
<fieldset class="ui-grid-b" id="other_edit_date">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="other_edit_id">Animal ID:</label>
<select id="other_edit_id" name="other_edit_id">
</select>
</div>

<div class="ui-field-contain">
<label for="other_edit_wt">Final Weight (lbs.):</label>
<input type="text" id="other_edit_wt" name="other_edit_wt" 
       value="N/A">
</div>

<div class="ui-field-contain">
<label for="other_edit_reason">Reason for Removal:</label>
<select id="other_edit_reason" name="other_edit_reason">
</select>
</div>

<input type="button" class="ui-btn" value="Add Reason"
<?php  if ($_SESSION['admin']) {
   echo "onclick=\"add_other_reason('#other_edit', 'other_edit');\"";
} else {
   echo "onclick=\"nonAdmin();\"";
}
?>
>

<div class="ui-field-contain">
<label for="other_edit_dest">Destination:</label>
<select id="other_edit_dest" name="other_edit_dest">
</select>
</div>

<input type="button" class="ui-btn" value="Add Destination"
<?php  if ($_SESSION['admin']) {
   echo "onclick=\"add_other_dest('#other_edit', 'other_edit');\"";
} else {
   echo "onclick=\"nonAdmin();\"";
}
?>
>

<div class="ui-field-contain" id="other_edit_comments_div">
<label for="other_edit_comments">Comments:</label>
<textarea id="other_edit_comments" name="other_edit_comments">
</textarea>
</div>

<div id="other_edit_notification">&nbsp;</div>

<input type="submit" value="Submit" id="other_edit_submit">

</form>
</div>
</div>

<div data-role="page" id="sale">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#death">Remove</a>
         <ul> 
         <li> <a href="#sale">Sale</a>
            <ul> 
            <li> <a href="#sale_input" 
                    onclick="init_sale_input('sale_input');">Input Form</a>
            <li> <a href="#sale_report" onclick="init_sale_report();">Report</a>
            </ul>
         </ul>
      </ul>
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="sale_report">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#death">Remove</a>
         <ul> 
         <li> <a href="#sale">Sale</a>
            <ul> 
            <li> <a href="#sale_report" onclick="init_sale_report();">Report</a>
            </ul>
         </ul>
      </ul>
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">

<form id="sale_report_form">

<h1>Sale Report</h1>

<div class="ui-field-contain">
<label for="sale_report_from">From:</label>
<fieldset class="ui-grid-b" id="sale_report_from">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="sale_report_to">To:</label>
<fieldset class="ui-grid-b" id="sale_report_to">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="sale_report_group">Animal Group:</label>
<select id="sale_report_group" name="sale_report_group">
</select>
</div>

<div class="ui-field-contain">
<label for="sale_report_dest">Destination:</label>
<select id="sale_report_dest" name="sale_report_dest">
</select>
</div>

<div>&nbsp;</div>
<input type="submit" value="Submit" id="submit_sale_report">
<div>&nbsp;</div>
<div id="sale_report_table"></div>

</form>
</div>
</div>

<div data-role="page" id="sale_input">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#death">Remove</a>
         <ul> 
         <li> <a href="#sale">Sale</a>
            <ul> 
            <li> <a href="#sale_input"
                    onclick="init_sale_input('sale_input');">Input Form</a>
            </ul>
         </ul>
      </ul>
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="sale_input_form">

<h1>Sale Input Form</h1>

<div class="ui-field-contain">
<label for="sale_input_date">Date:</label>
<fieldset class="ui-grid-b" id="sale_input_date">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="sale_input_id">Animal ID:</label>
<select id="sale_input_id" name="sale_input_id" onchange="animal_update('sale_input');">
</select>
</div>

<div class="ui-field-contain">
<label for="sale_input_group">Animal Group:</label>
<input type="text" id="sale_input_group" name="sale_input_group" readonly>
</div>

<div class="ui-field-contain">
<label for="sale_input_name">Name:</label>
<input type="text" id="sale_input_name" name="sale_input_name" readonly>
</div>

<div class="ui-field-contain">
<label for="sale_input_mark">Color &amp; Markings:</label>
<input type="text" id="sale_input_mark" name="sale_input_mark" readonly>
</div>

<div id="sale_input_picture_div"></div>

<div class="ui-field-contain">
<label for="sale_input_tag">Sale Tag:</label>
<input type="text" id="sale_input_tag" name="sale_input_tag" value="N/A">
</div>

<div class="ui-field-contain">
<label for="sale_input_dest">Destination:</label>
<select id="sale_input_dest" name="sale_input_dest">
</select>
</div>

<input type="button" class="ui-btn" value="Add Destination"
<?php  if ($_SESSION['admin']) {
   echo "onclick=\"add_dest('#sale_input', 'sale_input');\"";
} else {
   echo "onclick=\"nonAdmin();\"";
}
?>
>

<div class="ui-field-contain">
<label for="sale_input_weight">Weight (lbs.):</label>
<input type="number" min="1" id="sale_input_weight" name="sale_input_weight"
   oninput="update_total_price('sale_input');">
</div>

<div class="ui-field-contain">
<label for="sale_input_estimated">Weight:</label>
<select id="sale_input_estimated" name="sale_input_estimated">
<option value="MEASURED">MEASURED</option>
<option value="ESTIMATED">ESTIMATED</option>
</select>
</div>

<div class="ui-field-contain">
<label for="sale_input_price">Price per lb. ($):</label>
<input type="number" min="0" step="0.01" id="sale_input_price" 
   name="sale_input_price" oninput="update_total_price('sale_input');">
</div>

<div class="ui-field-contain">
<label for="sale_input_total_price">Sale Price ($):</label>
<input type="number" min="0" step="0.01" id="sale_input_total_price" 
   name="sale_input_total_price" readonly>
</div>

<div class="ui-field-contain">
<label for="sale_input_fee">Sale &amp; Hauling Fees ($):</label>
<input type="number" min="0" step="0.01" id="sale_input_fee" 
   name="sale_input_fee" oninput="update_net_price('sale_input');">
</div>

<div class="ui-field-contain">
<label for="sale_input_net_price">Net Price ($):</label>
<input type="number" min="0" step="0.01" id="sale_input_net_price" 
   name="sale_input_net_price" readonly>
</div>

<div class="ui-field-contain" id="sale_input_comments_div" style="display:none">
<label for="sale_input_comments">Comments:</label>
<textarea id="sale_input_comments" name="sale_input_comments">
</textarea>
</div>

<input type="button" class="ui-btn" value="Show Comments" 
   id="sale_input_comments_btn" onclick="toggle('sale_input_comments', 'Comments');">

<div id="sale_input_notification">&nbsp;</div>

<input type="submit" value="Submit" id="submit_sale_input">

</form>
</div>
</div>

<div data-role="page" id="sale_edit">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#death">Remove</a>
         <ul> 
         <li> <a href="#sale">Sale</a>
            <ul> 
            <li> <a href="#sale_report" onclick="init_sale_report();">Report</a>
            </ul>
         </ul>
      </ul>
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="sale_edit_form">

<h1>Sale Edit Form</h1>

<input type = hidden name="sale_edit_auto_id" id="sale_edit_auto_id">
<input type = hidden name="sale_edit_orig_id" id="sale_edit_orig_id">

<div class="ui-field-contain">
<label for="sale_edit_date">Date:</label>
<fieldset class="ui-grid-b" id="sale_edit_date">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="sale_edit_id">Animal ID:</label>
<select id="sale_edit_id" name="sale_edit_id">
</select>
</div>

<div class="ui-field-contain">
<label for="sale_edit_tag">Sale Tag:</label>
<input type="text" id="sale_edit_tag" name="sale_edit_tag">
</div>

<div class="ui-field-contain">
<label for="sale_edit_dest">Destination:</label>
<select id="sale_edit_dest" name="sale_edit_dest">
</select>
</div>

<input type="button" class="ui-btn" value="Add Destination"
<?php  if ($_SESSION['admin']) {
   echo "onclick=\"add_dest('#sale_edit', 'sale_edit');\"";
} else {
   echo "onclick=\"nonAdmin();\"";
}
?>
>

<div class="ui-field-contain">
<label for="sale_edit_weight">Weight (lbs.):</label>
<input type="number" min="1" id="sale_edit_weight" name="sale_edit_weight"
   oninput="update_total_price('sale_edit');">
</div>

<div class="ui-field-contain">
<label for="sale_edit_estimated">Weight:</label>
<select id="sale_edit_estimated" name="sale_edit_estimated">
<option value="MEASURED">MEASURED</option>
<option value="ESTIMATED">ESTIMATED</option>
</select>
</div>

<div class="ui-field-contain">
<label for="sale_edit_price">Price per lb. ($):</label>
<input type="number" min="0" step="0.01" id="sale_edit_price" 
   name="sale_edit_price" oninput="update_total_price('sale_edit');">
</div>

<div class="ui-field-contain">
<label for="sale_edit_fee">Sale &amp; Hauling Fees ($):</label>
<input type="number" min="0" step="0.01" id="sale_edit_fee" 
   name="sale_edit_fee" oninput="update_net_price('sale_edit');">
</div>

<label for="sale_edit_comments">Comments:</label>
<textarea id="sale_edit_comments" name="sale_edit_comments">
</textarea>

<div id="sale_edit_notification">&nbsp;</div>

<input type="submit" value="Submit" id="submit_sale_edit">

</form>
</div>
</div>

<div data-role="page" id="slaughter">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#death">Remove</a>
         <ul> 
         <li> <a href="#slaughter">Slaughter</a>
            <ul> 
            <li> <a href="#slay_input" 
                    onclick="init_slay_input('slay_input');">Input Form</a>
            <li> <a href="#slay_report" onclick="init_slay_report();">Report</a>
            </ul>
         </ul>
      </ul>
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="slay_input">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#death">Remove</a>
         <ul> 
         <li> <a href="#slaughter">Slaughter</a>
            <ul> 
            <li> <a href="#slay_input"
                    onclick="init_slay_input('slay_input');">Input Form</a>
            </ul>
         </ul>
      </ul>
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="slay_input_form">

<h1>Slaughter Input Form</h1>

<div class="ui-field-contain">
<label for="slay_input_date">Date:</label>
<fieldset class="ui-grid-b" id="slay_input_date">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="slay_input_id">Animal ID:</label>
<select id="slay_input_id" name="slay_input_id" onchange="animal_update('slay_input');">
</select>
</div>

<div class="ui-field-contain">
<label for="slay_input_group">Animal Group:</label>
<input type="text" id="slay_input_group" name="slay_input_group" readonly>
</div>

<div class="ui-field-contain">
<label for="slay_input_name">Name:</label>
<input type="text" id="slay_input_name" name="slay_input_name" readonly>
</div>

<div class="ui-field-contain">
<label for="slay_input_mark">Color &amp; Markings:</label>
<input type="text" id="slay_input_mark" name="slay_input_mark" readonly>
</div>

<div id="slay_input_picture_div"></div>

<div class="ui-field-contain">
<label for="slay_input_tag">Sale Tag:</label>
<input type="text" id="slay_input_tag" name="slay_input_tag" value="N/A">
</div>

<div class="ui-field-contain">
<label for="slay_input_weight">Live Weight (lbs.):</label>
<input type="number" min="1" id="slay_input_weight" name="slay_input_weight">
</div>

<div class="ui-field-contain">
<label for="slay_input_estimated">Weight:</label>
<select id="slay_input_estimated" name="slay_input_estimated">
<option value="MEASURED">MEASURED</option>
<option value="ESTIMATED">ESTIMATED</option>
</select>
</div>

<div class="ui-field-contain">
<label for="slay_input_house">Slaughter House:</label>
<select id="slay_input_house" name="slay_input_house">
</select>
</div>

<input type="button" class="ui-btn" value="Add Slaughter House"
<?php  if ($_SESSION['admin']) {
   echo "onclick=\"add_slayhouse('#slay_input', 'slay_input');\"";
} else {
   echo "onclick=\"nonAdmin();\"";
}
?>
>

<div class="ui-field-contain">
<label for="slay_input_hauler">Hauler:</label>
<input type="text" id="slay_input_hauler" name="slay_input_hauler">
</div>

<div class="ui-field-contain">
<label for="slay_input_haul_equip">Hauling Equipment:</label>
<input type="text" id="slay_input_haul_equip" name="slay_input_haul_equip">
</div>

<div class="ui-field-contain">
<label for="slay_input_fee">Fees ($):</label>
<input type="number" min="0" step="0.01" id="slay_input_fee" 
   name="slay_input_fee">
</div>

<div class="ui-field-contain" id="slay_input_comments_div" style="display:none">
<label for="slay_input_comments">Comments:</label>
<textarea id="slay_input_comments" name="slay_input_comments">
</textarea>
</div>

<input type="button" class="ui-btn" value="Show Comments" 
   id="slay_input_comments_btn" onclick="toggle('slay_input_comments', 'Comments');">

<div id="slay_input_notification">&nbsp;</div>

<input type="submit" value="Submit" id="submit_slay_input">

</form>
</div>
</div>

<div data-role="page" id="slay_edit">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#death">Remove</a>
         <ul> 
         <li> <a href="#slaughter">Slaughter</a>
            <ul> 
            <li> <a href="#slay_report"
                    onclick="init_slay_report();">Report</a>
            </ul>
         </ul>
      </ul>
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="slay_edit_form">

<h1>Slaughter Edit Form</h1>

<input type=hidden id="slay_edit_auto_id" name="slay_edit_auto_id">
<input type=hidden id="slay_edit_orig_id" name="slay_edit_orig_id">

<div class="ui-field-contain">
<label for="slay_edit_date">Date:</label>
<fieldset class="ui-grid-b" id="slay_edit_date">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="slay_edit_id">Animal ID:</label>
<select id="slay_edit_id" name="slay_edit_id">
</select>
</div>

<div class="ui-field-contain">
<label for="slay_edit_tag">Sale Tag:</label>
<input type="text" id="slay_edit_tag" name="slay_edit_tag">
</div>

<div class="ui-field-contain">
<label for="slay_edit_weight">Live Weight (lbs.):</label>
<input type="number" min="1" id="slay_edit_weight" name="slay_edit_weight">
</div>

<div class="ui-field-contain">
<label for="slay_edit_estimated">Weight:</label>
<select id="slay_edit_estimated" name="slay_edit_estimated">
<option value="MEASURED">MEASURED</option>
<option value="ESTIMATED">ESTIMATED</option>
</select>
</div>

<div class="ui-field-contain">
<label for="slay_edit_house">Slaughter House:</label>
<select id="slay_edit_house" name="slay_edit_house">
</select>
</div>

<input type="button" class="ui-btn" value="Add Slaughter House"
<?php  if ($_SESSION['admin']) {
   echo "onclick=\"add_slayhouse('#slay_edit', 'slay_edit');\"";
} else {
   echo "onclick=\"nonAdmin();\"";
}
?>
>

<div class="ui-field-contain">
<label for="slay_edit_hauler">Hauler:</label>
<input type="text" id="slay_edit_hauler" name="slay_edit_hauler">
</div>

<div class="ui-field-contain">
<label for="slay_edit_haul_equip">Hauling Equipment:</label>
<input type="text" id="slay_edit_haul_equip" name="slay_edit_haul_equip">
</div>

<div class="ui-field-contain">
<label for="slay_edit_fee">Fees ($):</label>
<input type="number" min="0" step="0.01" id="slay_edit_fee" 
   name="slay_edit_fee">
</div>

<div class="ui-field-contain" id="slay_edit_comments_div">
<label for="slay_edit_comments">Comments:</label>
<textarea id="slay_edit_comments" name="slay_edit_comments">
</textarea>
</div>

<div id="slay_edit_notification">&nbsp;</div>

<input type="submit" value="Submit" id="submit_slay_edit">

</form>
</div>
</div>

<div data-role="page" id="slay_report">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home">Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#death">Remove</a>
         <ul> 
         <li> <a href="#slaughter">Slaughter</a>
            <ul> 
            <li> <a href="#slay_report" onclick="init_slay_report();">Report</a>
            </ul>
         </ul>
      </ul>
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">

<form id="slay_report_form">

<h1>Slaughter Report</h1>

<div class="ui-field-contain">
<label for="slay_report_from">From:</label>
<fieldset class="ui-grid-b" id="slay_report_from">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="slay_report_to">To:</label>
<fieldset class="ui-grid-b" id="slay_report_to">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="slay_report_group">Animal Group:</label>
<select id="slay_report_group" name="slay_report_group">
</select>
</div>

<div class="ui-field-contain">
<label for="slay_report_house">Slaughter House:</label>
<select id="slay_report_house" name="slay_report_house">
</select>
</div>

<div>&nbsp;</div>
<input type="submit" value="Submit" id="submit_slay_report">
<div>&nbsp;</div>
<div id="slay_report_table"></div>

</form>
</div>
</div>

<div data-role="page" id="maintain">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#care_gen">Care</a>
      <li> <a href="#move">Move</a>
      <li> <a href="#feed">Feed Purchase</a>
      <li> <a href="#notes">Notes/Tasks</a>
      <li> <a href="#egg">Egg Log</a>
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="feed">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#feed">Feed Purchase</a>
         <ul> 
         <li> <a href="#feed_input" 
                 onclick="feed_input_init('feed_input')">Input Form</a>
         <li> <a href="#feed_report"
                 onclick="feed_report_init();">Report</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="feed_report">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#feed">Feed Purchase</a>
         <ul> 
         <li> <a href="#feed_report"
                 onclick="feed_report_init();">Report</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="feed_report_form">

<h1>Feed Purchase Report</h1>

<div class="ui-field-contain">
<label for="feed_report_from">From:</label>
<fieldset class="ui-grid-b" id="feed_report_from">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="feed_report_to">To:</label>
<fieldset class="ui-grid-b" id="feed_report_to">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="feed_report_type">Major Feed Type:</label>
<select name="feed_report_type" id="feed_report_type"
        onchange="update_feed_subtype('feed_report', true, true);">
</select>
</div>

<div class="ui-field-contain">
<label for="feed_report_subtype">Feed Type Details:</label>
<select name="feed_report_subtype" id="feed_report_subtype">
</select>
</div>

<div class="ui-field-contain">
<label for="feed_report_group">For Animal Group:</label>
<select name="feed_report_group" id="feed_report_group">
</select>
</div>

<div class="ui-field-contain">
<label for="feed_report_vendor">Vendor:</label>
<select name="feed_report_vendor" id="feed_report_vendor">
</select>
</div>

<div>&nbsp;</div>
<input type="submit" value="Submit" id="submit_feed_report">

<div id="feed_report_table">&nbsp;</div>

</form>
</div>
</div>

<div data-role="page" id="feed_input">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#feed">Feed Purchase</a>
         <ul> 
         <li> <a href="#feed_input" 
                 onclick="feed_input_init('feed_input')">Input Form</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="feed_input_form">
<h1>Feed Purchase Input Form</h1>

<div class="ui-field-contain">
<label for="feed_input_date">Date:</label>
<fieldset class="ui-grid-b" id="feed_input_date">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="feed_input_type">Feed Major Type:</label>
<select name="feed_input_type" id="feed_input_type"
        onchange="update_feed_subtype('feed_input', true, false);">
</select>
</div>

<input type="button" class="ui-btn" value="Add Major Type"
   onclick="add_feed_type('#feed_input', 'feed_input');">

<div class="ui-field-contain">
<label for="feed_input_subtype">Feed Type Details:</label>
<select name="feed_input_subtype" id="feed_input_subtype">
</select>
</div>

<input type="button" class="ui-btn" value="Add Feed Type Details"
   onclick="add_feed_subtype('#feed_input', 'feed_input');">

<div class="ui-field-contain">
<label for="feed_input_group">For:</label>
<select name="feed_input_group" id="feed_input_group">
</select>
</div>

<div class="ui-field-contain">
<label for="feed_input_vendor">Vendor:</label>
<select name="feed_input_vendor" id="feed_input_vendor">
</select>
</div>

<input type="button" class="ui-btn" value="Add Vendor"
   onclick="add_vendor('#feed_input', 'feed_input');">

<div class="ui-field-contain">
<label for="feed_input_unit">Feed Unit:</label>
<select name="feed_input_unit" id="feed_input_unit">
</select>
</div>

<input type="button" class="ui-btn" value="Add Unit"
   onclick="add_feed_unit('#feed_input', 'feed_input');">

<div class="ui-field-contain">
<label for="feed_input_purchased">Units Purchased:</label>
<input type="number" min="0.01" id="feed_input_purchased" 
       step="0.01" name="feed_input_purchased" 
       oninput="update_feed_total();update_feed_weight();">
</div>

<div class="ui-field-contain">
<label for="feed_input_price">Price per Unit ($):</label>
<input type="number" min="0.01" id="feed_input_price" 
       step="0.01" name="feed_input_price" oninput="update_feed_total();">
</div>

<div class="ui-field-contain">
<label for="feed_input_total">Total Price ($):</label>
<input type="number" min="0.01" id="feed_input_total" 
       step="0.01" name="feed_input_total" readonly>
</div>

<div class="ui-field-contain">
<label for="feed_input_unit_weight">Weight per Unit (lbs):</label>
<input type="number" min="0.01" id="feed_input_unit_weight" 
       step="0.01" name="feed_input_unit_weight" 
       oninput="update_feed_weight();">
</div>

<div class="ui-field-contain">
<label for="feed_input_total_weight">Total Weight (lbs.):</label>
<input type="number" min="0.01" id="feed_input_total_weight" 
       step="0.01" name="feed_input_total_weight" readonly>
</div>

<div class="ui-field-contain" id="feed_input_comments_div" style="display:none">
<label for="feed_input_comments">Comments:</label>
<textarea id="feed_input_comments" name="feed_input_comments">
</textarea>
</div>

<input type="button" class="ui-btn" value="Show Comments" 
   id="feed_input_comments_btn" onclick="toggle('feed_input_comments', 'Comments');">

<div id="feed_input_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_feed_input">

</form>
</div>
</div>

<div data-role="page" id="feed_edit">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#feed">Feed Purchase</a>
         <ul> 
         <li> <a href="#feed_report"
                 onclick="feed_report_init();">Report</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="feed_edit_form">
<h1>Feed Purchase Edit Form</h1>
<input type="hidden" name="feed_edit_auto_id" id="feed_edit_auto_id">

<div class="ui-field-contain">
<label for="feed_edit_date">Date:</label>
<fieldset class="ui-grid-b" id="feed_edit_date">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="feed_edit_type">Feed Major Type:</label>
<select name="feed_edit_type" id="feed_edit_type"
        onchange="update_feed_subtype('feed_edit', true, false);">
</select>
</div>

<input type="button" class="ui-btn" value="Add Major Type"
   onclick="add_feed_type('#feed_edit', 'feed_edit');">

<div class="ui-field-contain">
<label for="feed_edit_subtype">Feed Type Details:</label>
<select name="feed_edit_subtype" id="feed_edit_subtype">
</select>
</div>

<input type="button" class="ui-btn" value="Add Feed Type Details"
   onclick="add_feed_subtype('#feed_edit', 'feed_edit');">

<div class="ui-field-contain">
<label for="feed_edit_group">For:</label>
<select name="feed_edit_group" id="feed_edit_group">
</select>
</div>

<div class="ui-field-contain">
<label for="feed_edit_vendor">Vendor:</label>
<select name="feed_edit_vendor" id="feed_edit_vendor">
</select>
</div>

<input type="button" class="ui-btn" value="Add Vendor"
   onclick="add_vendor('#feed_edit', 'feed_edit');">

<div class="ui-field-contain">
<label for="feed_edit_unit">Feed Unit:</label>
<select name="feed_edit_unit" id="feed_edit_unit">
</select>
</div>

<input type="button" class="ui-btn" value="Add Unit"
   onclick="add_feed_unit('#feed_edit', 'feed_edit');">

<div class="ui-field-contain">
<label for="feed_edit_purchased">Units Purchased:</label>
<input type="number" min="0.01" id="feed_edit_purchased" 
       step="0.01" name="feed_edit_purchased">
</div>

<div class="ui-field-contain">
<label for="feed_edit_price">Price per Unit ($):</label>
<input type="number" min="0.01" id="feed_edit_price" 
       step="0.01" name="feed_edit_price">
</div>

<div class="ui-field-contain">
<label for="feed_edit_unit_weight">Weight per Unit (lbs):</label>
<input type="number" min="0.01" id="feed_edit_unit_weight" 
       step="0.01" name="feed_edit_unit_weight">
</div>

<div class="ui-field-contain" id="feed_edit_comments_div">
<label for="feed_edit_comments">Comments:</label>
<textarea id="feed_edit_comments" name="feed_edit_comments">
</textarea>
</div>

<div id="feed_edit_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_feed_edit">

</form>
</div>
</div>

<div data-role="page" id="add_feed_unit">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#feed">Feed Purchase</a>
         <ul> 
         <li> <a href="#feed_input" 
                 onclick="feed_input_init('feed_input')">Input Form</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="add_feed_unit_form">

<h1>New Feed Unit Input Form</h1>

<div class="ui-field-contain">
<label for="add_feed_unit_unit">Feed Unit:</label>
<input type="text" id="add_feed_unit_unit" name="add_feed_unit_unit">
</div>

<div id="feed_unit_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_feed_unit">
<input type="button" value="Cancel" onclick="cancel('#feed_input');">

</form>
</div>
</div>

<div data-role="page" id="add_vendor">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#feed">Feed Purchase</a>
         <ul> 
         <li> <a href="#feed_input" 
                 onclick="feed_input_init('feed_input')">Input Form</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="add_vendor_form">

<h1>New Vendor Input Form</h1>

<div class="ui-field-contain">
<label for="add_vendor_vendor">Vendor:</label>
<input type="text" id="add_vendor_vendor" name="add_vendor_vendor">
</div>

<div id="vendor_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_vendor">
<input type="button" value="Cancel" onclick="cancel('#feed_input');">

</form>
</div>
</div>

<div data-role="page" id="add_feed_mtype">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#feed">Feed Purchase</a>
         <ul> 
         <li> <a href="#feed_input" 
                 onclick="feed_input_init('feed_input')">Input Form</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="add_feed_type_form">

<h1>New Feed Major Type Input Form</h1>

<div class="ui-field-contain">
<label for="add_feed_type">Feed Major Type:</label>
<input type="text" id="add_feed_type" name="add_feed_type">
</div>

<div id="feed_type_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_feed_type">
<input type="button" value="Cancel" onclick="cancel('#feed_input');">

</form>
</div>
</div>

<div data-role="page" id="add_feed_subtype">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#feed">Feed Purchase</a>
         <ul> 
         <li> <a href="#feed_input" 
                 onclick="feed_input_init('feed_input')">Input Form</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="add_feed_subtype_form">

<h1>New Feed Type Details Input Form</h1>

<div class="ui-field-contain">
<label for="add_feed_subtype_type">Feed Major Type:</label>
<select id="add_feed_subtype_type" name="add_feed_subtype_type">
</select>
</div>

<div class="ui-field-contain">
<label for="add_feed_subtype_subtype">Feed Type Details:</label>
<input type="text" id="add_feed_subtype_subtype" name="add_feed_subtype_subtype">
</div>

<div id="feed_subtype_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_feed_subtype">
<input type="button" value="Cancel" onclick="cancel('#feed_input');">

</form>
</div>
</div>

<div data-role="page" id="care_gen">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#care_gen">Care</a>
         <ul> 
         <li> <a href="#vet">Vet</a>
         <li> <a href="#care">Periodic</a>
         </ul> 
      </ul> 
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="notes">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#notes">Notes/Tasks</a>
         <ul>
         <li> <a href="#notes_notes">Notes</a>
         <li> <a href="#notes_tasks">Tasks</a>
         </ul> 
      </ul> 
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="notes_tasks">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#notes">Notes/Tasks</a>
         <ul>
         <li> <a href="#notes_tasks">Tasks</a>
            <ul>
            <li> <a href="#notes_tasks_list"
                    onclick="init_daily_task_list();">Task List</a>
            <li> <a href="#notes_tasks_input"
                    onclick="init_labor_input('labor_input');">
                 Labor Input Form</a>
            <li> <a href="#notes_tasks_report"
                    onclick="init_labor_report();">Labor Report</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="notes_tasks_input">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#notes">Notes/Tasks</a>
         <ul>
         <li> <a href="#notes_tasks">Tasks</a>
            <ul>
            <li> <a href="#notes_tasks_input"
                    onclick="init_labor_input('labor_input');">
                 Labor Input Form</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="labor_input_form">
<h1>Labor Input Form</h1>

<div class="ui-field-contain">
<label for="labor_input_date">Date:</label>
<fieldset class="ui-grid-b" id="labor_input_date">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="labor_input_task">Task:</label>
<select id="labor_input_task" name="labor_input_task"> 
</select>
</div>

<div class="ui-field-contain" id="labor_input_comments_div">
<label for="labor_input_comments">Comments:</label>
<textarea id="labor_input_comments" name="labor_input_comments">
</textarea>
</div>

<div class="ui-field-contain">
<label for="labor_input_group">Animal Group:</label>
<select id="labor_input_group" name="labor_input_group" 
   onchange="init_all('labor_input', true);">
</select>
</div>

<div class="ui-field-contain">
<label for="labor_input_subgroup">Subgroup:</label>
<select id="labor_input_subgroup" name="labor_input_subgroup">
</select>
</div>

<div class="ui-field-contain">
<label for="labor_input_workers">Workers:</label>
<input type="number" min="1" id="labor_input_workers" 
       name="labor_input_workers" value="1">
</div>

<div class="ui-field-contain">
<label for="labor_input_minutes">Minutes:</label>
<input type="number" min="1" id="labor_input_minutes" 
       name="labor_input_minutes" value="1">
</div>

<div id="labor_input_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_labor_input">

</form>
</div>
</div>

<div data-role="page" id="labor_edit">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#notes">Notes/Tasks</a>
         <ul>
         <li> <a href="#notes_tasks">Tasks</a>
            <ul>
            <li> <a href="#notes_tasks_report"
                    onclick="init_labor_report();">Labor Report</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="labor_edit_form">
<h1>Labor Edit Form</h1>

<input type="hidden" id="labor_edit_auto_id" name="labor_edit_auto_id"
  value="">
<input type="hidden" id="labor_edit_origdate" name="labor_edit_origdate"
  value="">

<div class="ui-field-contain">
<label for="labor_edit_date">Date:</label>
<fieldset class="ui-grid-b" id="labor_edit_date">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="labor_edit_task">Task:</label>
<select id="labor_edit_task" name="labor_edit_task"> 
</select>
</div>

<div class="ui-field-contain" id="labor_edit_comments_div">
<label for="labor_edit_comments">Comments:</label>
<textarea id="labor_edit_comments" name="labor_edit_comments">
</textarea>
</div>

<div class="ui-field-contain">
<label for="labor_edit_group">Animal Group:</label>
<select id="labor_edit_group" name="labor_edit_group" 
   onchange="init_all('labor_edit', true);">
</select>
</div>

<div class="ui-field-contain">
<label for="labor_edit_subgroup">Subgroup:</label>
<select id="labor_edit_subgroup" name="labor_edit_subgroup">
</select>
</div>

<div class="ui-field-contain">
<label for="labor_edit_workers">Workers:</label>
<input type="number" min="1" id="labor_edit_workers" 
       name="labor_edit_workers" value="1">
</div>

<div class="ui-field-contain">
<label for="labor_edit_minutes">Minutes:</label>
<input type="number" min="1" id="labor_edit_minutes" 
       name="labor_edit_minutes" value="1">
</div>

<div class="ui-field-contain">
<label for="labor_edit_complete">Complete</label>
   <input name="labor_edit_complete" id = "labor_edit_complete" type="checkbox">
</div>

<div id="labor_edit_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_labor_edit">

</form>
</div>
</div>

<div data-role="page" id="notes_tasks_report">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#notes">Notes/Tasks</a>
         <ul>
         <li> <a href="#notes_tasks">Tasks</a>
            <ul>
            <li> <a href="#notes_tasks_report"
                    onclick="init_labor_report();">Labor Report</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="labor_report_form">

<h1>Labor Report</h1>

<div class="ui-field-contain">
<label for="labor_report_from">From:</label>
<fieldset class="ui-grid-b" id="labor_report_from">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="labor_report_to">To:</label>
<fieldset class="ui-grid-b" id="labor_report_to">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="labor_report_task">Task:</label>
<select id="labor_report_task" name="labor_report_task"> 
</select>
</div>

<div class="ui-field-contain">
<label for="labor_report_group">Animal Group:</label>
<select id="labor_report_group" name="labor_report_group" 
   onchange="init_all('labor_report', true);">
</select>
</div>

<div class="ui-field-contain">
<label for="labor_report_subgroup">Subgroup:</label>
<select id="labor_report_subgroup" name="labor_report_subgroup">
</select>
</div>

<div>&nbsp;</div>
<input type="submit" value="Submit" id="submit_labor_report">

<div id="labor_table">&nbsp;</div>
</form>
</div>
</div>

<div data-role="page" id="notes_tasks_list">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#notes">Notes/Tasks</a>
         <ul>
         <li> <a href="#notes_tasks">Tasks</a>
            <ul>
            <li> <a href="#notes_tasks_list"
                    onclick="init_daily_task_list();">Task List</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="daily_chores_form">
<input type="hidden" name="num_daily_chores" id="num_daily_chores" value="0">
<h1>Daily Task List</h1>

<div class="ui-field-contain">
<label for="daily_task_date">Date:</label>
<fieldset class="ui-grid-b" id="daily_task_date">
</fieldset>
</div>

<input type="button" class="ui-btn" value="Update Task List Date" 
   onclick="update_daily_task_table();">

<div>&nbsp;</div>
<div class="tablediv">
<table border name="daily_chores_table" id="daily_chores_table" style="width:100%">
<thead>
<tr><th>Task</th><th>Complete</th><th style='width:15%'>Comments</th>
    <th>Animal Group</th><th>Subgroup</th><th style='width:3%'>Number 
    of Workers</th> <th style='width:3%'>Labor Time (Minutes)</th>
    </tr></thead>
<tbody>
</tbody>
</table>
</div>

<div id="daily_chores_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_daily_chores">
</form>
</div>
</div>

<div data-role="page" id="notes_notes">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#notes">Notes/Tasks</a>
         <ul>
         <li> <a href="#notes_notes">Notes</a>
            <ul> 
            <li> <a href="#notes_input" 
                    onclick="init_notes('notes');">Input Form</a>
            <li> <a href="#notes_report"
                    onclick="create_date('notes_report_to', null);
                             create_date('notes_report_from', null);">Report</a>
            </ul> 
         </ul>
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="notes_report">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#notes">Notes/Tasks</a>
         <ul> 
         <li> <a href="#notes_notes">Notes</a>
            <ul> 
            <li> <a href="#notes_report"
                    onclick="create_date('notes_report_to', null);
                             create_date('notes_report_from', null);">Report</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="notes_report_form">

<h1>Notes Report</h1>

<div class="ui-field-contain">
<label for="notes_report_from">From:</label>
<fieldset class="ui-grid-b" id="notes_report_from">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="notes_report_to">To:</label>
<fieldset class="ui-grid-b" id="notes_report_to">
</fieldset>
</div>

<div>&nbsp;</div>
<input type="submit" value="Submit" id="submit_notes_report">

<div id="notes_table">&nbsp;</div>

</form>
</div>
</div>

<div data-role="page" id="notes_edit">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#notes">Notes/Tasks</a>
         <ul> 
         <li> <a href="#notes_notes">Notes</a>
            <ul> 
            <li> <a href="#notes_report"
                    onclick="create_date('notes_report_to', null);
                             create_date('notes_report_from', null);">Report</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="notes_edit_form" enctype="multipart/form-data">

<h1>Note Edit Form</h1>

<input type="hidden" id="notes_edit_auto_id" name="notes_edit_auto_id">
<input type="hidden" name="notes_edit_orig_file" id="notes_edit_orig_file"
       value="">

<div class="ui-field-contain">
<label for="notes_edit_date">Date:</label>
<fieldset class="ui-grid-b" id="notes_edit_date">
</fieldset>
</div>

<div class="ui-field-contain" id="notes_edit_note_div">
<label for="notes_edit_note">Note:</label>
<textarea id="notes_edit_note" name="notes_edit_note">
</textarea>
</div>

<div class="ui-field-contain">
<label for='notes_edit_current_picture'>Current Picture: </label>
<input type="text" readonly id="notes_edit_current_picture" 
       name="notes_edit_current_picture">
</div>

<div class="ui-field-contain" id="notes_edit_file_div">
<label for="notes_edit_file">Upload New Picture (optional):</label>
<input type="file" name="notes_edit_file" id="notes_edit_file">
</div>

<div class="ui-field-contain">
<label for="notes_edit_clear">Max File Size: 4 MB</label>
<input type="button" value="Delete Picture" 
   onclick="clearForm('notes_edit_file');
            clearEdit('notes_edit_current_picture');">
</div>

<div id="notes_edit_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_notes_edit">

</form>
</div>
</div>

<div data-role="page" id="notes_input">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#notes">Notes/Tasks</a>
         <ul> 
         <li> <a href="#notes_notes">Notes</a>
            <ul> 
            <li> <a href="#notes_input" 
                    onclick="init_notes('notes');">Input Form</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="notes_input_form" enctype="multipart/form-data">

<h1>Notes Input Form</h1>

<div class="ui-field-contain">
<label for="notes_date">Date:</label>
<fieldset class="ui-grid-b" id="notes_date">
</fieldset>
</div>

<div class="ui-field-contain" id="notes_note_div">
<label for="notes_note">Note:</label>
<textarea id="notes_note" name="notes_note">
</textarea>
</div>

<div class="ui-field-contain" id="notes_file_div">
<label for="notes_file">Picture (optional):</label>
<input type="file" name="notes_file" id="notes_file">
</div>

<div class="ui-field-contain">
<label for="notes_clear">Max File Size: 4 MB</label>
<input type="button" value="Clear Picture" onclick="clearForm('notes_file');">
</div>

<div id="notes_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_notes_input">

</form>
</div>
</div>


<div data-role="page" id="move">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#move">Move</a>
         <ul> 
         <li> <a href="#move_input" 
                onclick="move_input_init('move_input');">Input Form</a>
         <li> <a href="#move_report" 
                 onclick="move_report_init('move_report');">Report</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="move_report">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#move">Move</a>
         <ul> 
         <li> <a href="#move_report" onclick="move_report_init('move_report');">
              Report</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="move_report_form">
<h1>Grazing Move Report</h1>

<div class="ui-field-contain">
<label for="move_report_from">From:</label>
<fieldset class="ui-grid-b" id="move_report_from">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="move_report_to">To:</label>
<fieldset class="ui-grid-b" id="move_report_to">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="move_report_group">Animal Group:</label>
<select id="move_report_group" name="move_report_group" 
   onchange="init_all('move_report', true);">
</select>
</div>

<div class="ui-field-contain">
<label for="move_report_subgroup">Subgroup:</label>
<select id="move_report_subgroup" name="move_report_subgroup">
</select>
</div>

<div class="ui-field-contain">
<label for="move_report_paddock">Paddock ID::</label>
<select id="move_report_paddock" name="move_report_paddock">
</select>
</div>

<div>&nbsp;</div>
<input type="submit"  value="Submit" id="submit_move_report">

<div id="move_table">&nbsp;</div>

</form>
</div>
</div>

<div data-role="page" id="move_input">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#move">Move</a>
         <ul> 
         <li> <a href="#move_input" 
                 onclick="move_input_init('move_input');">Input Form</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="move_input_form">
<h1>Grazing Move Input Form</h1>

<div class="ui-field-contain">
<label for="move_input_date">Date:</label>
<fieldset class="ui-grid-b" id="move_input_date">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="move_input_group">Animal Group:</label>
<select name="move_input_group" id="move_input_group"
        onchange="update_subgroup('move_input', true);">
</select>
</div>

<div class="ui-field-contain">
<label for="move_input_subgroup">Subgroup:</label>
<select name="move_input_subgroup" id="move_input_subgroup"
        onchange="update_move_paddock('move_input');">
</select>
</div>

<div class="ui-field-contain">
<label for="move_input_move">Move:</label>
<select name="move_input_move" id="move_input_move"
        onchange="update_move_paddock('move_input');">
<option value="1">To</option>
<option value="0">From</option>
</select>
</div>

<div class="ui-field-contain">
<label for="move_input_paddock">Paddock ID:</label>
<select name="move_input_paddock" id="move_input_paddock">
</select>
</div>

<div class="ui-field-contain">
<label for="move_input_height">Forage Height (inches):</label>
<select name="move_input_height" id="move_input_height">
</select>
</div>

<div class="ui-field-contain">
<label for="move_input_density">Forage Density:</label>
<select name="move_input_density" id="move_input_density">
<option value="1">1: BARE SPOTS</option>
<option value="2">2: THIN</option>
<option value="3">3: MODERATE</option>
<option value="4">4: FULL</option>
<option value="5">5: LUSH</option>
</select>
</div>

<div class="ui-field-contain" id="move_input_comments_div" style="display:none">
<label for="move_input_comments">Comments:</label>
<textarea id="move_input_comments" name="move_input_comments">
</textarea>
</div>

<input type="button" class="ui-btn" value="Show Comments" 
   id="move_input_comments_btn" onclick="toggle('move_input_comments', 'Comments');">

<div id="move_input_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_move_input">

</form>
</div>
</div>

<div data-role="page" id="care">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#care_gen">Care</a>
         <ul> 
         <li> <a href="#care">Periodic</a>
            <ul> 
            <li> <a href="#sheep_care">Sheep/Goats</a>
            <li> <a href="#cattle_care">Cattle</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="sheep_care">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#care_gen">Care</a>
         <ul> 
         <li> <a href="#care">Periodic</a>
            <ul> 
            <li> <a href="#sheep_care">Sheep/Goats</a>
               <ul> 
               <li> <a href="#sheep_care_input" onclick="init_sheep_care_input('sheep_care_input');">
                    Input Form</a>
               <li> <a href="#sheep_care_report" onclick="init_sheep_care_report();">Report</a>
               </ul> 
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="sheep_care_input">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#care_gen">Care</a>
         <ul> 
         <li> <a href="#care">Periodic</a>
            <ul> 
            <li> <a href="#sheep_care">Sheep/Goats</a>
               <ul> 
               <li> <a href="#sheep_care_input" onclick="init_sheep_care_input('sheep_care_input');">
                    Input Form</a>
               </ul> 
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="sheep_care_input_form">
<h1>Sheep/Goat Care Input Form</h1>

<div class="ui-field-contain">
<label for="sheep_care_input_date">Care Date:</label>
<fieldset class="ui-grid-b" id="sheep_care_input_date">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="sheep_care_input_id">Animal ID:</label>
<select id="sheep_care_input_id" name="sheep_care_input_id" 
    onchange="animal_update('sheep_care_input');">
</select>
</div>

<div class="ui-field-contain">
<label for="sheep_care_input_group">Animal Group:</label>
<input type="text" id="sheep_care_input_group" name="sheep_care_input_group" readonly>
</div>

<div class="ui-field-contain">
<label for="sheep_care_input_name">Name:</label>
<input type="text" id="sheep_care_input_name" name="sheep_care_input_name" readonly>
</div>

<div class="ui-field-contain">
<label for="sheep_care_input_mark">Color &amp; Markings:</label>
<input type="text" id="sheep_care_input_mark" name="sheep_care_input_mark" readonly>
</div>

<div id="sheep_care_input_picture_div"></div>

<div class="ui-field-contain">
<label for="sheep_care_input_eye">Eye (FAMACHA):</label>
<select id="sheep_care_input_eye" name="sheep_care_input_eye">
<option value="-1">N/A</option>
<option value="1">1: RED</option>
<option value="2">2: RED-PINK</option>
<option value="3">3: PINK</option>
<option value="4">4: PINK-WHITE</option>
<option value="5">5: WHITE</option>
</select>
</div>

<div class="ui-field-contain">
<label for="sheep_care_input_body">Body Condition:</label>
<select id="sheep_care_input_body" name="sheep_care_input_body">
<option value="-1">N/A</option>
<option value="1">1: VERY THIN</option>
<option value="2">2: THIN</option>
<option value="3">3: IDEAL</option>
<option value="4">4: CHUBBY</option>
<option value="5">5: OBESE</option>
</select>
</div>

<div class="ui-field-contain">
<label for="sheep_care_input_tail">Tail:</label>
<select id="sheep_care_input_tail" name="sheep_care_input_tail">
<option value="CLEAN">CLEAN</option>
<option value="SOILED">SOILED</option>
</select>
</div>

<div class="ui-field-contain">
<label for="sheep_care_input_nose">Nose:</label>
<input type="text" id="sheep_care_input_nose" name="sheep_care_input_nose">
</div>

<div class="ui-field-contain">
<label for="sheep_care_input_coat">Coat:</label>
<input type="text" id="sheep_care_input_coat" name="sheep_care_input_coat">
</div>

<div class="ui-field-contain">
<label for="sheep_care_input_jaw">Bottle Jaw:</label>
<select id="sheep_care_input_jaw" name="sheep_care_input_jaw">
<option value="0">0: NONE</option>
<option value="1">1: MINOR</option>
<option value="2">2: MAJOR</option>
</select>
</div>

<div class="ui-field-contain">
<label for="sheep_care_input_wormer">Wormer Given:</label>
<select id="sheep_care_input_wormer" name="sheep_care_input_wormer">
</select>
</div>

<input type="button" class="ui-btn" value="Add Worming Product"
   onclick="add_wormer('#sheep_care_input', 'sheep_care_input');">

<div class="ui-field-contain">
<label for="sheep_care_input_wormer_quantity">Quantity Given:</label>
<input id="sheep_care_input_wormer_quantity" type="text" name="sheep_care_input_wormer_quantity">
</div>

<div class="ui-field-contain">
<label for="sheep_care_input_hoof">Hoof Condition:</label>
<select id="sheep_care_input_hoof" name="sheep_care_input_hoof">
<option value="GOOD">GOOD</option>
<option value="OK">OK</option>
<option value="BAD">BAD</option>
</select>
</div>

<div class="ui-field-contain">
<label for="sheep_care_input_trim">Hoof Trim?:</label>
<select id="sheep_care_input_trim" name="sheep_care_input_trim">
<option value="YES">YES</option>
<option value="NO">NO</option>
</select>
</div>

<div class="ui-field-contain">
<label for="sheep_care_input_weight">Weight (lbs.):</label>
<input type="number" min="1" id="sheep_care_input_weight" name="sheep_care_input_weight">
</div>

<div class="ui-field-contain">
<label for="sheep_care_input_estimated">Weight:</label>
<select id="sheep_care_input_estimated" name="sheep_care_input_estimated">
<option value="ESTIMATED">ESTIMATED</option>
<option value="MEASURED">MEASURED</option>
</select>
</div>

<div class="ui-field-contain" id="sheep_care_input_comments_div" style="display:none">
<label for="sheep_care_input_comments">Comments:</label>
<textarea id="sheep_care_input_comments" name="sheep_care_input_comments">
</textarea>
</div>

<input type="button" class="ui-btn" value="Show Comments" 
   id="sheep_care_input_comments_btn" onclick="toggle('sheep_care_input_comments', 'Comments');">

<div id="sheep_care_input_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_sheep_care_input">

</form>
</div>
</div>

<div data-role="page" id="sheep_care_edit">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#care_gen">Care</a>
         <ul> 
         <li> <a href="#care">Periodic</a>
            <ul> 
            <li> <a href="#sheep_care">Sheep/Goats</a>
               <ul> 
               <li> <a href="#sheep_care_report" onclick="init_sheep_care_report();">
                    Report</a>
               </ul> 
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="sheep_care_edit_form">
<input type="hidden" name="sheep_care_edit_auto_id" id="sheep_care_edit_auto_id">
<h1>Sheep/Goat Care Edit Form</h1>

<div class="ui-field-contain">
<label for="sheep_care_edit_date">Care Date:</label>
<fieldset class="ui-grid-b" id="sheep_care_edit_date">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="sheep_care_edit_id">Animal ID:</label>
<select id="sheep_care_edit_id" name="sheep_care_edit_id">
</select>
</div>

<div class="ui-field-contain">
<label for="sheep_care_edit_eye">Eye (FAMACHA):</label>
<select id="sheep_care_edit_eye" name="sheep_care_edit_eye">
<option value="-1">N/A</option>
<option value="1">1: RED</option>
<option value="2">2: RED-PINK</option>
<option value="3">3: PINK</option>
<option value="4">4: PINK-WHITE</option>
<option value="5">5: WHITE</option>
</select>
</div>

<div class="ui-field-contain">
<label for="sheep_care_edit_body">Body Condition:</label>
<select id="sheep_care_edit_body" name="sheep_care_edit_body">
<option value="-1">N/A</option>
<option value="1">1: VERY THIN</option>
<option value="2">2: THIN</option>
<option value="3">3: IDEAL</option>
<option value="4">4: CHUBBY</option>
<option value="5">5: OBESE</option>
</select>
</div>

<div class="ui-field-contain">
<label for="sheep_care_edit_tail">Tail:</label>
<select id="sheep_care_edit_tail" name="sheep_care_edit_tail">
<option value="CLEAN">CLEAN</option>
<option value="SOILED">SOILED</option>
</select>
</div>

<div class="ui-field-contain">
<label for="sheep_care_edit_nose">Nose:</label>
<input type="text" id="sheep_care_edit_nose" name="sheep_care_edit_nose">
</div>

<div class="ui-field-contain">
<label for="sheep_care_edit_coat">Coat:</label>
<input type="text" id="sheep_care_edit_coat" name="sheep_care_edit_coat">
</div>

<div class="ui-field-contain">
<label for="sheep_care_edit_jaw">Bottle Jaw:</label>
<select id="sheep_care_edit_jaw" name="sheep_care_edit_jaw">
<option value="0">0: NONE</option>
<option value="1">1: MINOR</option>
<option value="2">2: MAJOR</option>
</select>
</div>

<div class="ui-field-contain">
<label for="sheep_care_edit_wormer">Wormer Given:</label>
<select id="sheep_care_edit_wormer" name="sheep_care_edit_wormer">
</select>
</div>

<input type="button" class="ui-btn" value="Add Worming Product"
   onclick="add_wormer('#sheep_care_edit', 'sheep_care_edit');">

<div class="ui-field-contain">
<label for="sheep_care_edit_wormer_quantity">Quantity Given:</label>
<input id="sheep_care_edit_wormer_quantity" type="text" name="sheep_care_edit_wormer_quantity">
</div>

<div class="ui-field-contain">
<label for="sheep_care_edit_hoof">Hoof Condition:</label>
<select id="sheep_care_edit_hoof" name="sheep_care_edit_hoof">
<option value="GOOD">GOOD</option>
<option value="OK">OK</option>
<option value="BAD">BAD</option>
</select>
</div>

<div class="ui-field-contain">
<label for="sheep_care_edit_trim">Hoof Trim?:</label>
<select id="sheep_care_edit_trim" name="sheep_care_edit_trim">
<option value="YES">YES</option>
<option value="NO">NO</option>
</select>
</div>

<div class="ui-field-contain">
<label for="sheep_care_edit_weight">Weight (lbs.):</label>
<input type="number" min="1" id="sheep_care_edit_weight" name="sheep_care_edit_weight">
</div>

<div class="ui-field-contain">
<label for="sheep_care_edit_estimated">Weight:</label>
<select id="sheep_care_edit_estimated" name="sheep_care_edit_estimated">
<option value="ESTIMATED">ESTIMATED</option>
<option value="MEASURED">MEASURED</option>
</select>
</div>

<div class="ui-field-contain" id="sheep_care_edit_comments_div">
<label for="sheep_care_edit_comments">Comments:</label>
<textarea id="sheep_care_edit_comments" name="sheep_care_edit_comments">
</textarea>
</div>

<div>&nbsp;</div>

<input type="submit"  value="Submit" id="submit_sheep_care_edit">

</form>
</div>
</div>

<div data-role="page" id="sheep_care_report">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#care_gen">Care</a>
         <ul> 
         <li> <a href="#care">Periodic</a>
            <ul> 
            <li> <a href="#sheep_care">Sheep/Goats</a>
               <ul> 
               <li> <a href="#sheep_care_report" onclick="init_sheep_care_report();">
                    Report</a>
               </ul> 
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="sheep_care_report_form">
<h1>Sheep/Goat Care Report</h1>

<div class="ui-field-contain">
<label for="sheep_care_report_from">From:</label>
<fieldset class="ui-grid-b" id="sheep_care_report_from">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="sheep_care_report_to">To:</label>
<fieldset class="ui-grid-b" id="sheep_care_report_to">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="sheep_care_report_group">Animal Group:</label>
<select id="sheep_care_report_group" name="sheep_care_report_group">
<option value="%">ALL</option>
<option value="SHEEP">SHEEP</option>
<option value="GOATS">GOATS</option>
</select>
</div>

<div>&nbsp;</div>
<input type="submit"  value="Submit" id="submit_sheep_care_report">

<div id="sheep_care_report_scroll"></div>
<div id="sheep_care_table">&nbsp;</div>

</form>
</div>
</div>

<div data-role="page" id="add_wormer">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#care_gen">Care</a>
         <ul> 
         <li> <a href="#care">Periodic</a>
            <ul> 
            <li> <a href="#sheep_care">Sheep/Goats</a>
               <ul> 
               <li> <a href="#sheep_care_input" onclick="init_sheep_care_input('sheep_care_input');">
                    Input Form</a>
               </ul> 
            </ul> 
         </ul> 
      </ul> 
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="add_wormer_form">

<h1>New Worming Product Input Form</h1>

<div class="ui-field-contain">
<label for="reason">Worming Product:</label>
<input type="text" id="wormer" name="wormer">
</div>

<div id="wormer_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_wormer">
<input type="button" value="Cancel" onclick="cancel('#sheep_care_input');">

</form>
</div>
</div>

<div data-role="page" id="egg">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#egg">Egg Log</a>
         <ul> 
         <li> <a href="#egg_input" onclick="init_egg_input();">Input Form</a>
         <li> <a href="#egg_report" onclick="init_egg_report();">Report</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="egg_input">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#egg">Egg Log</a>
         <ul> 
         <li> <a href="#egg_input" onclick="init_egg_input();">Input Form</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="egg_input_form">
<h1>Egg Log Input Form</h1>

<div class="ui-field-contain">
<label for="egg_input_date">Date:</label>
<fieldset class="ui-grid-b" id="egg_input_date">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="egg_input_amt">Number of eggs collected:</label>
<input type="number" min="1" id="egg_input_amt" name="egg_input_amt">
</div>

<div class="ui-field-contain">
<label for="egg_input_comments">Comments:</label>
<textarea id="egg_input_comments" name="egg_input_comments">
</textarea>
</div>

<div id="egg_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_egg_input">

</form>
</div>
</div>

<div data-role="page" id="egg_edit">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#egg">Egg Log</a>
         <ul> 
         <li> <a href="#egg_report" onclick="init_egg_report();">Report</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="egg_edit_form">
<h1>Egg Log Edit Form</h1>
<input type="hidden" name="egg_edit_auto_id" id="egg_edit_auto_id">

<div class="ui-field-contain">
<label for="egg_edit_date">Date:</label>
<fieldset class="ui-grid-b" id="egg_edit_date">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="egg_edit_amt">Number of eggs collected:</label>
<input type="number" min="1" id="egg_edit_amt" name="egg_edit_amt">
</div>

<div class="ui-field-contain">
<label for="egg_edit_comments">Comments:</label>
<textarea id="egg_edit_comments" name="egg_edit_comments">
</textarea>
</div>

<div id="egg_edit_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_egg_edit">

</form>
</div>
</div>

<div data-role="page" id="egg_report">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#egg">Egg Log</a>
         <ul> 
         <li> <a href="#egg_report" onclick="init_egg_report();">Report</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="egg_report_form">
<h1>Egg Log Report</h1>

<div class="ui-field-contain">
<label for="egg_report_from">From:</label>
<fieldset class="ui-grid-b" id="egg_report_from">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="egg_report_to">To:</label>
<fieldset class="ui-grid-b" id="egg_report_to">
</fieldset>
</div>

<div>&nbsp;</div>
<input type="submit"  value="Submit" id="submit_egg_report">

<div id="egg_table">&nbsp;</div>

</form>
</div>
</div>

<div data-role="page" id="egg_select">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#egg">Egg Log</a>
         <ul> 
         <li> <a href="#egg_report" onclick="init_egg_report();">Report</a>
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<div id="egg_select_header_div"></div>
<div id="egg_select_table_div"></div>
</div>
</div>

<div data-role="page" id="vet">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#care_gen">Care</a>
         <ul> 
         <li> <a href="#vet">Vet</a>
            <ul> 
            <li> <a href="#vet_input" onclick="vet_input_init();">Input Form</a>
            <li> <a href="#vet_report" onclick="vet_report_init();">Report</a>
            </ul> 
         </ul> 
      </ul> 
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="vet_input">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#care_gen">Care</a>
         <ul> 
         <li> <a href="#vet">Vet</a>
            <ul> 
            <li> <a href="#vet_input" onclick="vet_input_init();">Input Form</a>
            </ul> 
         </ul> 
      </ul> 
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="vet_input_form">

<h1>Vet Record Input Form</h1>

<div class="ui-field-contain">
<label for="vet_date">Date:</label>
<fieldset class="ui-grid-b" id="vet_date">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="vet_id">Animal ID:</label>
<select id="vet_id" name="vet_id" onchange="animal_update('vet');">
</select>
</div>

<div class="ui-field-contain">
<label for="vet_group">Animal Group:</label>
<input type="text" id="vet_group" name="vet_group" readonly>
</div>

<div class="ui-field-contain">
<label for="vet_name">Name:</label>
<input type="text" id="vet_name" name="vet_name" readonly>
</div>

<div class="ui-field-contain">
<label for="vet_mark">Color &amp; Markings:</label>
<input type="text" id="vet_mark" name="vet_mark" readonly>
</div>

<div id="vet_picture_div"></div>

<div class="ui-field-contain">
<label for="vet_reason">Reason for Care:</label>
<select id="vet_reason" name="vet_reason"> 
</select>
</div>

<input type="button" class="ui-btn" value="Add Reason for Care"
<?php  if ($_SESSION['admin']) {
   echo "onclick=\"add_reason('#vet_input', 'vet');\"";
} else {
   echo "onclick=\"nonAdmin();\"";
}
?>
>

<div class="ui-field-contain" id="symptoms_div" style="display:none">
<label for="symptoms">Symptoms:</label>
<textarea id="symptoms" name="symptoms">
</textarea>
</div>

<input type="button" class="ui-btn" value="Show Symptoms" id="symptoms_btn"
   onclick="toggle('symptoms', 'Symptoms');">

<div class="ui-field-contain">
<label for="temperature">Temperature:</label>
<select id="temperature" name="temperature">
<option value="N/A">N/A</option>
<?php
for ($i = 85; $i < 109; $i++) {
   echo "<option value='".$i."'>".$i."</option>";
}
?>
</select>
</div>

<div class="ui-field-contain" id="careGiven_div" style="display:none">
<label for="care">Care Given:</label>
<textarea id="careGiven" name="careGiven">
N/A
</textarea>
</div>

<input type="button" class="ui-btn" value="Show Care Given" id="care_btn"
   onclick="toggle('careGiven', 'Care Given');">

<input type=hidden name="num_med_rows" id="num_med_rows" value="0">

<div class="tablediv">
<table border name="med_table" id="med_table" style="width:100%">
<thead>
<tr><th>Medication</th><th>Dosage</th><th>Unit</th><th>Units Given</th></tr>
</thead>
<tbody>
</tbody>
</table>
</div>
<div class="ui-grid-a">
<div class="ui-block-a">
<input type="button" class="ui-btn" value="Add Medication" 
   onclick="add_med_row();">
</div>
<div class="ui-block-b">
<input type="button" class="ui-btn" value="Remove Medication" 
   onclick="remove_med_row();">
</div>
</div>

<input type="button" class="ui-btn" value="Add New Medication"
<?php  if ($_SESSION['admin']) {
   echo "onclick=\"add_medication('#vet_input', 'med_table');\"";
} else {
   echo "onclick=\"nonAdmin();\"";
}
?>
>

<div class="ui-field-contain">
<label for="vet_weight">Estimated Weight (lbs.):</label>
<input type="text" id="vet_weight" name="vet_weight" value="N/A">
</div>

<div class="ui-field-contain">
<label for="vet_advisor">Vet/Advisor:</label>
<input type="text" id="vet_advisor" name="vet_advisor" value="N/A">
</div>

<div class="ui-field-contain">
<label for="vet_contact">Contact:</label>
<select id="vet_contact" name="vet_contact">
<option value="PRESENT">PRESENT</option>
<option value="PHONE">PHONE</option>
<option value="EMAIL">EMAIL</option>
</select>
</div>

<div class="ui-field-contain">
<label for="vet_assist">Assistants:</label>
<input type="text" id="vet_assist" name="vet_assist" value="N/A">
</div>

<div class="ui-field-contain" id="vet_comments_div" style="display:none">
<label for="vet_comments">Comments:</label>
<textarea id="vet_comments" name="vet_comments">
N/A
</textarea>
</div>

<input type="button" class="ui-btn" value="Show Comments" 
   id="vet_comments_btn" onclick="toggle('vet_comments', 'Comments');">

<div id="vet_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_vet_input">

</form>
</div>
</div>

<div data-role="page" id="vet_report">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#care_gen">Care</a>
         <ul> 
         <li> <a href="#vet">Vet</a>
            <ul> 
            <li> <a href="#vet_report" onclick="vet_report_init();">Report</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="vet_report_form">

<h1>Vet Report</h1>

<div class="ui-field-contain">
<label for="vet_report_id">Animal ID:</label>
<select id="vet_report_id" name="vet_report_id">
</select>
</div>

<div class="ui-field-contain">
<label for="vet_date_from">From:</label>
<fieldset class="ui-grid-b" id="vet_date_from">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="vet_date_to">To:</label>
<fieldset class="ui-grid-b" id="vet_date_to">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="vet_report_med">Medication:</label>
<select id="vet_report_med" name="vet_report_med">
</select>
</div>

<div class="ui-field-contain">
<label for="vet_report_reason">Reason for Care:</label>
<select id="vet_report_reason" name="vet_report_reason">
</select>
</div>

<div>&nbsp;</div>

<input type="submit"  value="Submit" id="submit_vet_report">

<div id="vet_table">&nbsp;</div>

</form>
</div>
</div>

<div data-role="page" id="vet_edit">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#care_gen">Care</a>
         <ul> 
         <li> <a href="#vet">Vet</a>
            <ul> 
            <li> <a href="#vet_report" onclick="vet_report_init();">Report</a>
            </ul> 
         </ul> 
      </ul> 
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="vet_edit_form">

<h1>Vet Record Edit Form</h1>

<input type="hidden" name="vet_edit_auto_id" id="vet_edit_auto_id">

<div class="ui-field-contain">
<label for="vet_edit_date">Date:</label>
<fieldset class="ui-grid-b" id="vet_edit_date">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="vet_edit_id">Animal ID:</label>
<!--
<input type="text" id="vet_edit_id" name="vet_edit_id">
-->
<select id="vet_edit_id" name="vet_edit_id">
</select>
</div>

<div class="ui-field-contain">
<label for="vet_edit_reason">Reason for Care:</label>
<select id="vet_edit_reason" name="vet_edit_reason"> 
</select>
</div>

<input type="button" class="ui-btn" value="Add Reason for Care"
<?php  if ($_SESSION['admin']) {
   echo "onclick=\"add_reason('#vet_edit', 'vet_edit');\"";
} else {
   echo "onclick=\"nonAdmin();\"";
}
?>
>

<div class="ui-field-contain" id="symptoms_edit_div">
<label for="symptoms_edit">Symptoms:</label>
<textarea id="symptoms_edit" name="symptoms_edit">
</textarea>
</div>

<div class="ui-field-contain">
<label for="temperature_edit">Temperature:</label>
<select id="temperature_edit" name="temperature_edit">
<option value="N/A">N/A</option>
<?php
for ($i = 85; $i < 109; $i++) {
   echo "<option value='".$i."'>".$i."</option>";
}
?>
</select>
</div>

<div class="ui-field-contain" id="care_edit_div">
<label for="care_edit">Care Given:</label>
<textarea id="care_edit" name="care_edit">
</textarea>
</div>

<input type=hidden name="num_med_edit_rows" id="num_med_edit_rows" value="0">

<div class="tablediv">
<table border name="med_edit_table" id="med_edit_table" style="width:100%">
<thead>
<tr><th>Medication</th><th>Dosage</th><th>Unit</th><th>Units Given</th></tr>
</thead>
<tbody>
</tbody>
</table>
</div>
<div class="ui-grid-a">
<div class="ui-block-a">
<input type="button" class="ui-btn" value="Add Medication" 
   onclick="add_med_edit_row();">
</div>
<div class="ui-block-b">
<input type="button" class="ui-btn" value="Remove Medication" 
   onclick="remove_med_edit_row();">
</div>
</div>

<input type="button" class="ui-btn" value="Add New Medication"
<?php  if ($_SESSION['admin']) {
   echo "onclick=\"add_medication('#vet_edit', 'med_edit_table');\"";
} else {
   echo "onclick=\"nonAdmin();\"";
}
?>
>

<div class="ui-field-contain">
<label for="vet_edit_weight">Estimated Weight (lbs.):</label>
<input type="text" id="vet_edit_weight" name="vet_edit_weight" value="N/A">
</div>

<div class="ui-field-contain">
<label for="vet_edit_advisor">Vet/Advisor:</label>
<input type="text" id="vet_edit_advisor" name="vet_edit_advisor" value="N/A">
</div>

<div class="ui-field-contain">
<label for="vet_edit_contact">Contact:</label>
<select id="vet_edit_contact" name="vet_edit_contact">
<option value="PRESENT">PRESENT</option>
<option value="PHONE">PHONE</option>
<option value="EMAIL">EMAIL</option>
</select>
</div>

<div class="ui-field-contain">
<label for="vet_edit_assist">Assistants:</label>
<input type="text" id="vet_edit_assist" name="vet_edit_assist" value="N/A">
</div>

<div class="ui-field-contain" id="vet_edit_comments_div">
<label for="vet_edit_comments">Comments:</label>
<textarea id="vet_edit_comments" name="vet_edit_comments">
</textarea>
</div>

<div id="vet_edit_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_vet_edit">
</form>
</div>
</div>

<div data-role="page" id="add_reason">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#care_gen">Care</a>
         <ul> 
         <li> <a href="#vet">Vet</a>
            <ul> 
            <li> <a href="#vet_input" onclick="vet_input_init();">Input Form</a>
            </ul> 
         </ul> 
      </ul> 
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="add_reason_form">

<h1>New Reason Input Form</h1>

<div class="ui-field-contain">
<label for="reason">Reason:</label>
<input type="text" id="reason" name="reason">
</div>

<div id="reason_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_reason">
<input type="button" value="Cancel" onclick="cancel('#vet_input');">

</form>
</div>
</div>

<div data-role="page" id="add_medication">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#maintain" data-icon="eye">Maintain</a>
      <ul> 
      <li> <a href="#care_gen">Care</a>
         <ul> 
         <li> <a href="#vet">Vet</a>
            <ul> 
            <li> <a href="#vet_input" onclick="vet_input_init();">Input Form</a>
            </ul> 
         </ul> 
      </ul> 
   </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="add_medication_form">

<h1>New Medication Input Form</h1>

<div class="ui-field-contain">
<label for="medication">Medication:</label>
<input type="text" id="medication" name="medication">
</div>

<div class="ui-field-contain">
<label for="dosage">Dosage:</label>
<input type="text" id="dosage" name="dosage">
</div>

<div id="medication_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_medication">
<input type="button" value="Cancel" onclick="cancel('#vet_input');">

</form>
</div>
</div>

<div data-role="page" id="birth">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#birth">Add</a>
         <ul> 
         <li> <a href="#birth_input" onclick="birth_input_init();">Input Form</a>
         <li> <a href="#birth_report" onclick="birth_report_init();">Report</a>
         </ul> 
       </ul>
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
</div>
</div>

<div data-role="page" id="birth_input">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#birth">Add</a>
         <ul> 
         <li> <a href="#birth_input" onclick="birth_input_init();">Input Form</a>
         </ul> 
       </ul>
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="birth_input_form" enctype="multipart/form-data">

<h1>Animal Record Input Form</h1>

<div class="ui-field-contain">
<label for="birth_id">Animal ID:</label>
<input type="text" id="birth_id" name="birth_id">
</div>

<div class="ui-field-contain">
<label for="birth_group">Animal Group:</label>
<select id="birth_group" name="birth_group" 
   onchange="init_all('birth', false);">
</select>
</div>

<div class="ui-field-contain">
<label for="birth_breed">Breed:</label>
<select id="birth_breed" name="birth_breed">
</select>
</div>

<input type="button" class="ui-btn" value="Add Breed"
<?php  if ($_SESSION['admin']) {
   echo "onclick=\"add_breed('#birth_input', 'birth');\"";
} else {
   echo "onclick=\"nonAdmin();\"";
}
?>
>

<div class="ui-field-contain">
<label for="birth_subgroup">Subgroup:</label>
<select id="birth_subgroup" name="birth_subgroup">
</select>
</div>

<input type="button" class="ui-btn" value="Add Subgroup"
<?php  if ($_SESSION['admin']) {
   echo "onclick=\"add_subgroup('#birth_input', 'birth');\"";
} else {
   echo "onclick=\"nonAdmin();\"";
}
?>
>

<div class="ui-field-contain">
<label for="birth_gender">Gender:</label>
<select id="birth_gender" name="birth_gender">
<option value="M">M</option>
<option value="F">F</option>
</select>
</div>

<div class="ui-field-contain">
<label for="birth_date">Date of Birth:</label>

<fieldset class="ui-grid-b" id="birth_date">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="origin">Origin:</label>
<select id="birth_origin" name="birth_origin">
</select>
</div>

<input type="button" class="ui-btn" value="Add Origin"
<?php  if ($_SESSION['admin']) {
   echo "onclick=\"add_origin('#birth_input', 'birth');\"";
} else {
   echo "onclick=\"nonAdmin();\"";
}
?>
>

<div class="ui-field-contain">
<label for="birth_mother">Mother:</label>
<select id="birth_mother" name="birth_mother">
</select>
</div>

<div class="ui-field-contain">
<label for="birth_father">Father:</label>
<select id="birth_father" name="birth_father">
</select>
</div>

<div class="ui-field-contain">
<label for="name">Name:</label>
<input type="text" id="name" name="name" value="N/A">
</div>

<div class="ui-field-contain">
<label for="mark">Color &amp; Markings:</label>
<input type="text" id="mark" name="mark">
</div>

<div class="ui-field-contain" id="birth_comments_div" style="display:none">
<label for="birth_comments">Comments:</label>
<textarea id="birth_comments" name="birth_comments">
N/A
</textarea>
</div>

<input type="button" class="ui-btn" value="Show Comments" 
   id="birth_comments_btn" onclick="toggle('birth_comments', 'Comments');">

<div class="ui-field-contain" id="birth_file_div">
<label for="birth_file">Picture (optional):</label>
<input type="file" name="birth_file" id="birth_file">
</div>

<div class="ui-field-contain">
<label for="birth_clear">Max File Size: 4 MB</label>
<input type="button" value="Clear Picture" onclick="clearForm('birth_file');">
</div>

<div id="birth_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_birth_input">

</form>
</div>
</div>

<div data-role="page" id="birth_report">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
      <li> <a href="#animal" data-icon="tag">Animal</a>
      <ul> 
      <li> <a href="#birth">Add</a>
         <ul> 
         <li> <a href="#birth_report" onclick="birth_report_init();">Report</a>
         </ul> 
       </ul>
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="birth_report_form">

<h1>Animal Report</h1>

<div class="ui-field-contain">
<label for="birth_report_id">Animal ID:</label>
<select id="birth_report_id" name="birth_report_id">
</select>
</div>

<div class="ui-field-contain">
<label for="birth_report_group">Animal Group:</label>
<select id="birth_report_group" name="birth_report_group" 
   onchange="init_all('birth_report', true);">
</select>
</div>

<div class="ui-field-contain">
<label for="birth_report_breed">Breed:</label>
<select id="birth_report_breed" name="birth_report_breed">
</select>
</div>

<div class="ui-field-contain">
<label for="birth_report_subgroup">Subgroup:</label>
<select id="birth_report_subgroup" name="birth_report_subgroup">
</select>
</div>

<div class="ui-field-contain">
<label for="birth_report_gender">Gender:</label>
<select id="birth_report_gender" name="birth_report_gender">
<option value="%">ALL</option>
<option value="M">M</option>
<option value="F">F</option>
</select>
</div>

<div class="ui-field-contain">
<label for="birth_date_all">Ignore Birth Date Range</label>
   <input name="birth_date_all" id = "birth_date_all" type="checkbox"
    checked="checked">
</div>

<div class="ui-field-contain">
<label for="birth_date_from">Birth Date From:</label>
<fieldset class="ui-grid-b" id="birth_date_from">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="birth_date_to">Birth Date To:</label>
<fieldset class="ui-grid-b" id="birth_date_to">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="birth_report_mother">Mother:</label>
<select id="birth_report_mother" name="birth_report_mother">
</select>
</div>

<div class="ui-field-contain">
<label for="birth_report_father">Father:</label>
<select id="birth_report_father" name="birth_report_father">
</select>
</div>

<div class="ui-field-contain">
<label for="birth_report_alive">Alive/On Farm:</label>
<select id="birth_report_alive" name="birth_report_alive">
<option value="1">Yes</option>
<option value="0">No</option>
<option value="%">ALL</option>
</select>
</div>

<div id="birth_report_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_birth_report">

<div id="birth_table">&nbsp;</div>

</form>
</div>
</div>

<div data-role="page" id="birth_edit">
<div data-role="navbar">
<ul> 
<li> <a href="#home" data-icon="home"> Home</a>
   <ul> 
   <li> <a href="#animal" data-icon="tag">Animal</a>
   <ul> 
      <li> <a href="#birth">Add</a>
         <ul> 
         <li> <a href="#birth_report" onclick="birth_report_init();">Report</a>
         </ul> 
       </ul>
    </ul>
</ul>
</div>
<div data-role="main" class="ui-content">
<form id="birth_edit_form" enctype="multipart/form-data">

<h1>Animal Record Edit</h1>

<div class="ui-field-contain">
<label for="birth_edit_id">Animal ID:</label>
<input type="text" id="birth_edit_id" name="birth_edit_id">
</div>

<div class="ui-field-contain">
<label for="birth_edit_group">Animal Group:</label>
<select id="birth_edit_group" name="birth_edit_group" 
   onchange="init_all('birth_edit', false);">
</select>
</div>

<input type="hidden" name="birth_edit_orig_id" id="birth_edit_orig_id">
<input type="hidden" name="birth_edit_auto_id" id="birth_edit_auto_id">
<input type="hidden" name="birth_edit_orig_file" id="birth_edit_orig_file"
       value="">

<div class="ui-field-contain">
<label for="birth_edit_breed">Breed:</label>
<select id="birth_edit_breed" name="birth_edit_breed">
</select>
</div>

<input type="button" class="ui-btn" value="Add Breed"
<?php  if ($_SESSION['admin']) {
   echo "onclick=\"add_breed('#birth_edit', 'birth_edit');\"";
} else {
   echo "onclick=\"nonAdmin();\"";
}
?>
>

<div class="ui-field-contain">
<label for="birth_edit_subgroup">Subgroup:</label>
<select id="birth_edit_subgroup" name="birth_edit_subgroup">
</select>
</div>

<input type="button" class="ui-btn" value="Add Subgroup"
<?php  if ($_SESSION['admin']) {
   echo "onclick=\"add_subgroup('#birth_edit', 'birth_edit');\"";
} else {
   echo "onclick=\"nonAdmin();\"";
}
?>
>

<div class="ui-field-contain">
<label for="birth_edit_gender">Gender:</label>
<select id="birth_edit_gender" name="birth_edit_gender">
<option value="M">M</option>
<option value="F">F</option>
</select>
</div>

<div class="ui-field-contain">
<label for="birth_edit_date">Date of Birth:</label>

<fieldset class="ui-grid-b" id="birth_edit_date">
</fieldset>
</div>

<div class="ui-field-contain">
<label for="birth_edit_origin">Origin:</label>
<select id="birth_edit_origin" name="birth_edit_origin">
</select>
</div>

<input type="button" class="ui-btn" value="Add Origin"
<?php  if ($_SESSION['admin']) {
   echo "onclick=\"add_origin('#birth_edit', 'birth_edit');\"";
} else {
   echo "onclick=\"nonAdmin();\"";
}
?>
>

<div class="ui-field-contain">
<label for="birth_edit_mother">Mother:</label>
<select id="birth_edit_mother" name="birth_edit_mother">
</select>
</div>

<div class="ui-field-contain">
<label for="birth_edit_father">Father:</label>
<select id="birth_edit_father" name="birth_edit_father">
</select>
</div>

<div class="ui-field-contain">
<label for="name_edit">Name:</label>
<input type="text" id="name_edit" name="name_edit" value="N/A">
</div>

<div class="ui-field-contain">
<label for="mark_edit">Color &amp; Markings:</label>
<input type="text" id="mark_edit" name="mark_edit">
</div>

<div class="ui-field-contain" id="birth_edit_comments_div">
<label for="birth_edit_comments">Comments:</label>
<textarea id="birth_edit_comments" name="birth_edit_comments">
</textarea>
</div>

<div class="ui-field-contain">
<label for="birth_edit_alive">Alive/On Farm:</label>
<select id="birth_edit_alive" name="birth_edit_alive">
<option value="1">Yes</option>
<option value="0">No</option>
</select>
</div>

<div class="ui-field-contain">
<label for='birth_edit_current_picture'>Current Picture: </label>
<input type="text" readonly id="birth_edit_current_picture" 
       name="birth_edit_current_picture">
</div>

<div class="ui-field-contain" id="birth_edit_file_div">
<label for="birth_edit_file">Upload New Picture (optional):</label>
<input type="file" name="birth_edit_file" id="birth_edit_file">
</div>

<div class="ui-field-contain">
<label for="birth_edit_clear">Max File Size: 4 MB</label>
<input type="button" value="Delete Picture" 
   onclick="clearForm('birth_edit_file');
            clearEdit('birth_edit_current_picture');">
</div>

<div id="birth_edit_notification">&nbsp;</div>
<input type="submit"  value="Submit" id="submit_birth_edit">

</form>
</div>
</div>

</body>
</html>
