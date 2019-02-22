<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
.button {
    display: block;
    width: 100%;
    padding: 10px;
    margin: 5px;
    text-align: center;
    border-radius: 5px;
    font-weight: bold;
    color: black;
    background-color: ghostwhite;
    text-decoration : none;
    border: 1px solid #000;
    text-shadow: 0 1px 0 #059;
}
</style>
</head>

<body>
<?php
$_SESSION['user'] = 'critterGuest';
$_SESSION['dbuser'] = 'critterGuest';
$_SESSION['dbpass'] = 'critterGuestPass';
$_SESSION['db'] = 'critterdb';
$_SESSION['admin'] = 1;
?>

<h1 align="center">Guest Users</h1>

This page provides guest access to Dickinson College's AnimalData application.
Guest users are allowed to view all data and all AnimalData features.
Any attempt to add new data or modify existing data may result in a
database error message (beginning with SQLSTATE).  If your actions result
in such an error, simply click "OK" and continue using AnimalData.
<div>&nbsp;</div>

<a href="Guest Users Guide AnimalData v0.1.pdf" class="button" 
   target="_blank">Open Guest Users Guide</a>
<a href="home.php" class="button">Proceed to AnimalData</a>

</body>
</html>
