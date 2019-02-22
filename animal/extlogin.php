<?php
// HTTPSON
if($_SERVER["HTTPS"] != "on") {
   header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
   exit();
}
// HTTPSOFF
session_start();
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" href="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="https://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>

<body>
<h1 align="center">AnimalData Login</h1> <br>
<form name='loginform' id='loginform'  method='POST' action="validate.php"
      data-ajax='false'>

<div class="ui-field-contain">
<label for="username">Username:</label>
<input type="text" name="username" id="username">
</div>

<div class="ui-field-contain">
<label for="pass">Password:</label>
<input type="password" name="pass" id="pass">
</div>

<input class = "submitbutton" type="submit" value = "Submit">
<script type="text/javascript">
  var u = document.getElementById("username");
  u.focus();
</script>
</form>
</body>

