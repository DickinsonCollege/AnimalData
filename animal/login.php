<?PHP
session_start();
// include $_SERVER['DOCUMENT_ROOT'].'/utilities.php';
// $useragent=$_SERVER['HTTP_USER_AGENT'];
// $_SESSION['mobile'] = isMobile($useragent);
# $_SESSION['mobile'] = 1;
$dcCASURL = "https://auth.dickinson.edu/cas/";
$dcServicePage = "https://" . $_SERVER['HTTP_HOST'] . "/animal/login.php";
// echo $dcServicePage
// $dcServicePage = "https://farmdatadev.dickinson.edu/login.php";

// Are we trying to validate a service ticket?
if(isset($_GET['ticket'])) {
	$ch = curl_init($dcCASURL . "serviceValidate?service=" . urlencode($dcServicePage) . "&ticket=" . $_GET['ticket']);	
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$dcdata = curl_exec($ch);
	curl_close($ch);
	if(strpos($dcdata, "<cas:authenticationSuccess>") > 0) {
		preg_match_all("/<cas:user>(.*)<\/cas:user>/", $dcdata, $dcuser);
		$_SESSION['user'] = $dcuser[1][0];
		$_SESSION['dbuser'] = 'critter';
		$_SESSION['dbpass'] = 'critterpass';
		$_SESSION['db'] = 'critterdb';
	} else {
		die("CAS ticket validation failed. Please try again.");
	}
}
if(!isset($_SESSION['user'])) {
	header("Location: " . $dcCASURL . "login?service=" . urlencode($dcServicePage));
	exit();
} else {
	header("Location: home.php");

}
include 'connection.php';
if (isset($_SESSION['user'])) {
$sql= "Select exists(select username from users where username='".$_SESSION['user']."' and active = 1) as isUser";
                $check=$dbcon->query($sql);
        while ($users=$check->fetch(PDO::FETCH_ASSOC)) {
                if ($users['isUser']==0) {
		session_destroy();
		die("Access Denied.You are not authorized to use AnimalData!");
	}else {
        $sql= "Select exists(select username from users where username='".$_SESSION['user']."' and admin='1') as isAdmin";
                $check=$dbcon->query($sql);
        while ($admin=$check->fetch(PDO::FETCH_ASSOC)) {
                if ($admin['isAdmin']==0) {
 	       $_SESSION['admin']=0;
		}else {
        	$_SESSION['admin']=1;
		}	
		
	}
}
}
}
// If we are not logged in, redirect to CAS
?>
