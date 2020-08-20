<?php
namespace predicted\api;
//http://phone.octotrade.ru/api/v1/autodial?phone=9881161760&type=delivered&account=123456&idLog=TEST
//predicted
//FwLj7WPMZZ
// Подключим файл с функциями
// СПРАВКА ПО ФУНКЦИЯМ
// Получить количество звонков в очереди: cli_call_queue ($a)
// Получить Количество свободных операторов в очереди: cli_queue_free_agents ($a)
// Получить список звонков в очереди: cli_call_list_queue ($a)
// Создать call файл для выполнения звонка create_call($phone, $queue, $context, $acc_name)
//	$phone номер телефона абонента
//	$queue номер телефона очереди или внутреннего абонента
//	$context имя контекста (Asterisk) я выполнения исходящего звонка 
//	$acc_name имя аккаунта или метка звонка 
//Коды ответов API
//	000 Выполненно удачно (Call файл создан)
//	001 Не выполненна авторизация
//	002 Нет свободных операторов. Выполнить паузу с таймаутом 5 секунд
//	003 Не указан номер телефона
//	Пример GET запроса http://phone.octotrade.ru/predicted/?login=predicted&password=FwLj7WPMZZ&phone=89881161760&order=test&option=0
//  Пример GET запроса http://phone.octotrade.ru/predicted/?login=predicted&password=FwLj7WPMZZ&phone=89881161760&queue=2010&extension=700&order=test
//  Пример GET запроса http://phone.octotrade.ru/predicted/?login=predicted&password=FwLj7WPMZZ&queue=2010&option=1; (1,2)- Вернет количество свободныхо ператоров / ожидающих клиентов в очереди (1,2)
//  Пример GET запроса http://phone.octotrade.ru/predicted/?login=predicted&password=FwLj7WPMZZ&extension=700&clear=0; - вернет значение счетчика очереди
// 	Пример GET запроса http://phone.octotrade.ru/predicted/?login=predicted&password=FwLj7WPMZZ&extension=700&clear=1; - очистит значение счетчика очереди
//  Пример GET запроса http://phone.octotrade.ru/predicted/?callLog=1&dialNumber=$&account=$&ext=$&uid=$&sipHeader=
// Включить вывод сообщений об ошибках
ini_set('display_errors','On'); 
error_reporting(E_ALL); 
$queuenum = isset($_GET['queue'])? $_GET['queue'] : 0;
$a = 'queue show '.$queuenum;//Комадна CLI для получения массива
//$ext = '700';
include 'cli-functions.php';
require_once 'api/classes/ApiClass.php';
require_once 'api/classes/LoggerClass.php';

//$a = 'queue show 2010';				//Комадна CLI для получения массива
$phone = isset($_GET['phone'])? $_GET['phone'] : 1 ;			//Номер телефона кому звоним
$option = isset($_GET['option'])? $_GET['option'] : 0; //Вернет количество свободных/ожидающих операторов
//option 1 колличество активных операторов в очереди
//option 2 количество ожидающих в очереди
$extension = isset($_GET['extension'])? $_GET['extension'] : 0;
$clear = isset($_GET['clear'])? $_GET['clear'] : 10;
$context = 'from-internal'; 		//Имя внутреннего контекста Asterisk
$acc_name = isset($_GET['order'])?  $_GET['order'] : ''; 		//Номер заказа или прочее
$login = isset($_GET['login'])? $_GET['login'] : '';			//Логин для авторизации(FreePBX)
$password = isset($_GET['password'])? sha1($_GET['password']) : '';//Пароль для авторизации
$idLog = isset($_GET['id'])? $_GET['id'] : 0;

$checkUser = checkUser($login, $password);

//$cli_queue_free_agents = cli_queue_free_agents($a,$queuenum);
//for sip header to log
$callLog = isset($_GET['callLog'])? $_GET['callLog'] : 0;
$dialNumber = isset($_GET['dialNumber'])? $_GET['dialNumber'] : NULL;
$account = isset($_GET['account'])? $_GET['account'] : NULL; 
$ext = isset($_GET['ext'])? $_GET['ext'] : NULL;
$uid = isset($_GET['uid'])? $_GET['uid'] : NULL;
$sipHeader = isset($_GET['sipHeader'])? $_GET['sipHeader'] : NULL;


$typeLogInfo = 'INFO';
$typeLogAuth = 'AUTH';
$typeLogError = 'ERROR';
$systemLog = 'API_GET';
//echo $get;
//echo "<pre>";
//print_r ($_GET);
//echo "</pre>";
//echo "</br>";
//echo $checkUser;
// echo "</br>";
// echo 'test'.$numOfAgent.'';
// echo "</br>";


//Write SIP HEADER to DB
if ($callLog !== 0)
    {
    	$sipHeader = print_r($sipHeader,true);
    	$sipHeader = urldecode($sipHeader);
    	$res = addCallLog ($dialNumber, $account, $ext, $uid, $sipHeader);
    	echo $res;
    	echo "HTTP/1.1 200 OK\r\nStatus: 200 OK\r\nContent-Type: text/plain\r\n\r\n";
    	exit;
    }
if ($option == '1' && $queuenum !==0){
	//Вернет количество свободных операторов
	$action = new AMIActions();
	$cli_queue_free_agents = $action->getMembersCountFree($queuenum);
	$num = $cli_queue_free_agents[0];
	//$num = cli_queue_free_agents($a,$queuenum);
	//$action = new AMIActions();
	//$num = $action->getCountMemberOnline($queuenum);

    //$addLogging = $objLogger->addMessage($typeLog, $systemLog, $resultLog);

    echo $num;
    $objLogger = new Logger();
    $messageLog = ['get=' => $_GET, 'response=' => $num];
    $addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
	exit;
}
if ($option == '2' && $queuenum !==0){
	//Вернет количество ожидающих клиентов в очереди
	$res = cli_call_queue($a);
	$num = $res[0];
    echo $num;
    $objLogger = new Logger();
    $messageLog = ['get=' => $_GET, 'response=' => $num];
    $addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
	exit;
}

if ($extension !== 0 && $clear == 0){
	echo getCallStats($extension);
	$objLogger = new Logger();
	$messageLog = ['get=' => $_GET, 'response=' => getCallStats($extension)];
    $addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
	exit;
}
if ($extension !== 0 && $clear == 1){
	getCallStats($extension);
	echo clearCallStats($extension);
	exit;
}
	//$num = $action->getMembersListFree($queuenum);
$action = new AMIActions();
$cli_queue_free_agents = $action->getMembersCountFree($queuenum);
if ($checkUser != 'ok')    
	{
			echo $checkUser;
			//$addLogging = $objLogger->addMessage($typeLog, $systemLog, $get);
	}
		elseif (empty($_GET['phone']))
	{
			echo "003"; 	// Не указан номер телефона
	}	
	    elseif ($queuenum == 0)
	{
		echo '005'; //неуказан номер очереди
	}
   	    elseif ($extension == 0)
	{
		echo '006'; //неуказан номер экстеншена
	}
		
		elseif ($checkUser == 'ok' && $cli_queue_free_agents > 0)
	{

			create_call($phone, $extension, $context, $acc_name, $idLog);

			echo "000";		//Выполненно удачно (Call файл создан)
			$objLogger = new Logger();
			$messageLog = ['get=' => $_GET, 'response=' => '000'];
    		$addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
	}
		else
	{
//$objLogger = new Logger();
//$get = print_r($_GET);
//$typeLog = 'INFO';
//$systemLog = 'API_GET';
//$addLogging = $objLogger->addMessage($typeLog, $systemLog, $get);
	echo "002"; //Нет свободных операторов. Выполнить паузу с таймаутом 5 секунд
	$objLogger = new Logger();
	$messageLog = ['get=' => $_GET, 'response=' => '002'];
    $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
	//echo 'Нет свободных операторов! Свободных операторов сейчас; ', $cli_queue_free_agents;
	}
?>
