<?php
//$login = 'predicted';
//$password = 'FwLj7WPMZZ';
// Включить вывод сообщений об ощибках

    ini_set('display_errors','On'); 
    error_reporting(E_ALL);
include 'cli-functions.php';
include 'dbconf.php';
//echo "<pre>";
//print_r ($_GET);
//echo "</pre>";
    echo 'Hello Word';
    echo "<br>";
// Текущее время
echo date('h:i:s') . "\n";
// ждать 2 секунды
usleep(2000000);
// вернуться к исполнению
echo date('h:i:s') . "\n";

//$mode = $_GET['mode'];			
//$phone = $_GET['phone'];	
		
    $phone = '89881161760';			
   
//$agent = $_GET['agent'];			
//    $dbhost = '127.0.0.1';
//    $dbname = 'asterisk';
//    $dblogin = 'root';
//    $dbpassword = 'Rjxthuf28';
//    $charset = 'utf8'; 
    $agent = '2011';		   
    $a = 'queue show '.$agent;
    $userfield = 'debug';
    echo "<br/>";
    echo $a;
    echo "<br/>";

//$agent = $_GET['context'];
		
    $context = 'from-internal';   	
    $acc_name = 'debug'; 		
    $login = 'dialer';			
    $password = sha1('dialer');
			
//    //переменные управления логикой
//    $checkUser = checkUser($login, $password);
//    $cli_queue_free_agents = cli_queue_free_agents($a);
//echo create_call($phone, $agent, $context, $acc_name);
monitoring($a, $login, $password, $dbhost, $dbname, $dblogin, $dbpassword, $charset);
"<br>";
connectDB($dbhost, $dbname, $dblogin, $dbpassword, $charset);
"<br>";
checkUser($login, $password, $dbhost, $dbname, $dblogin, $dbpassword, $charset);

//var_dump (get_cli_result($a));
//create_call($phone, $agent, $context, $acc_name);
	//$logfile = './log';
	//$timestamp = date("dS of F Y h:I:s A");
	//$originate = 'originate text';
	//$logstr = $timestamp.''.$originate;
	//file_put_contents($logfile, $logstr);
?>
