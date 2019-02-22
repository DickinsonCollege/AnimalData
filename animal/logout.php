<?php session_start(); ?>
<?php
// Unset all of the session variables.
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();
echo '<body id = "logout">';
echo '<br clear="all"/>';
echo 'To Log Out Completely, Close Your Browser!';
$method = "https";

$url = $method."://".$_SERVER['HTTP_HOST']."/animal/";
echo '<br clear="all"/><br clear="all"/>';
echo '<form method="POST" action="'.$url.'">';
echo '<input type ="submit" class="submitbutton" value = "Log In Again">';
echo '</form>';
?>
</body>
