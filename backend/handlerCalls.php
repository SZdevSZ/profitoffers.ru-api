<?php
ini_set('display_errors','On'); 
error_reporting(E_ALL);
session_start();
require_once 'dbconf.php';
require_once "cli-functions.php";
$login = $_SESSION['login'];
$password = sha1($_SESSION['origPassword']);
$origPassword = $_SESSION['origPassword'];
// echo "login:".$login."password:".$password."origPassword".$origPassword."<br>";
// print_r($_SESSION);
    unset($_SESSION['error_auth']);
    if (checkUser($login, $password, $dbhost, $dbname, $dblogin, $dbpassword, $charset) == 'ok'){
       $_SESSION['login'] = $login;
       $_SESSION['password'] = $password;
    }
        else header("Location: index.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Call..</title>
	<link href="css/main.css" rel="stylesheet">
	<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<?php
    if (empty($_POST['text'])) {
    	echo "<FONT color='red'>Error:</FONT>no data point";
    	return;
    }
$str = $_POST['text'];
$dump = var_export ($str,true);
$dump = str_replace("'", '', $dump);
$ar = preg_split("/[\s,]+/", $dump);
$i = 1;
foreach ($ar as $k => $v) {
	$modulo = ($k+1)/2;
	$res = gettype($modulo);
	if ($res == "double") {
		$phone  = $v;
	    //echo "This is mobile phone !".$phone."<br>";	
    }
	elseif ($res == "integer") {
		$userfield = $v;
		//echo "This is name !".$userfield."<br>";
	}
	if (!empty($userfield)&!empty($phone)){
		$agent = "751";
		$context = "from-internal";
		$acc_name = $login;
		$url = "http://phone.octotrade.ru/dialer/apiget.php?login=$login&password=$origPassword&phone=$phone&order=$acc_name&agent=$agent&context=$context&userfield=$userfield";
		$urlArray = parse_url($url);
		if ($urlArray['query']) {
			$requiestUrl = $urlArray['scheme'].'://'.$_SERVER['SERVER_ADDR'] . $urlArray['path'].'?'.$urlArray['query'];
		} 
		    else {
			    $requiestUrl = $urlArray['scheme'].'://'.$_SERVER['SERVER_ADDR'] . $urlArray['path'];
		}
		$get = file_get_contents($requiestUrl, null, null);
		 if ($get = "000") {
		 	$callStatus = "<FONT color='green'>OK</FONT>";
		 }
		//echo "url:".$requiestUrl."<br>";
		//echo "get:".$get."<br>";
		//echo "Call to number phone:".$phone." "."_Using name:".$userfield."<br>";
		$countAr = (count($ar) - 1)/2;
	    echo $countAr;
	 	echo "/".$i*10;
        
        echo "<div class='progress'>";
        echo "<div class='progress-bar' style='width: ".$i."%;'>";
        echo "</div>";
        echo "</div>";

        echo "---Call to number phone:".$phone." "."---Using name:".$userfield."---Call Status:".$callStatus."<br>";
		// sleep(2);
		unset($userfield, $phone, $agent, $context, $acc_name);
    	$i++;
    }
}    
?> 
      <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>

</body> 
</html> 