<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', 1);

if(!empty($_COOKIE['_ga']))
{
	$tmp = explode('.', $_COOKIE['_ga']);
	$clientid = $tmp[2].'.'.$tmp[3];

}
	else
	{
	$clientid = "uuid is empty";
	}
echo 'Your GOOGLE Client ID';
echo '<br>';
print_r($clientid);
echo '<br>';
echo 'Your COOKIE';
echo '<pre>';
print_r($_COOKIE);
echo '<pre>';
echo '<br>';
echo '<pre>';

echo 'Your UUID:';
echo '<br>';
print_r($_SERVER['UNIQUE_ID']);
echo '<br>';
echo '<br>';
echo 'Your User Agent / Type OS:';
echo '<br>';
print_r($_SERVER['HTTP_USER_AGENT']);
echo '<br>';
echo '<br>';
echo 'Your IP:';
echo '<br>';
print_r($_SERVER['REMOTE_ADDR']);
echo '<br>';
echo '<br>';
echo 'Your DOMAIN NAME';
echo '<br>';
$dnsname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
//sleep(2);
print_r($dnsname);
echo '<br>';
echo '<br>';
echo 'Type your device:';
echo '<br>';
function isMobile() { 
return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}
if(isMobile()){
    echo'Mobile';
}
else 
	{ echo 'PC';
    }
echo '<pre>';
print_r($_SERVER);
echo '</pre>';
echo '--------';
echo '<pre>';
$currentHost = $_SERVER['HTTP_HOST'];
print_r($currentHost);
echo '</pre>';



//function gaParseCookie() {
//if (isset($_COOKIE['_ga'])) {
//list($version,$domainDepth, $cid1, $cid2) = explode('.', $_COOKIE["_ga"]);
//$cid = $cid1.'.'.$cid2;
//}
//else $cid = gaGenUUID();
//return $cid;
//}


?>
