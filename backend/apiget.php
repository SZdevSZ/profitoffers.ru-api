<?php
//predicted
//FwLj7WPMZZ
// Подключим файл с функциями
// СПРАВКА ПО ФУНКЦИЯМ
// Получить количество звонков в очереди: cli_call_queue ($a)
// Получить количество свободных операторов в очереди: cli_queue_free_agents ($a)
// Получить список звонков в очереди: cli_call_list_queue ($a)
// Создать call файл для выполнения звонка create_call($phone, $queue, $context, $acc_name)
//	$phone номер телефона абонента
//	$queue номер телефона очереди или внутреннего абонента
//	$context имя контекста (Asterisk) я выполнения исходящего звонка 
//	$acc_name имя аккаунта или метка звонка 
//Коды ответов API
//	000 Выполненно удачно (Call файл создан)
//	001 Не выполненна автоизация
//	002 Нет свободных операторов. Выполнить паузу с таймаутом 5 секунд
//	003 Не указан номер телефона
//	Пример GET запроса http://phone.octotrade.ru/predicted/?login=predicted&password=FwLj7WPMZZ&phone=89881161760&order=test
// http://phone.octotrade.ru/dialer/?login=dialer&password=dialer&phone=89881161760&order=test&agent=751&context=$context&userfield=$userfield
// Включить вывод сообщений об ощибках
//ini_set('display_errors','On'); 
//error_reporting(E_ALL); 

include 'cli-functions.php';
include 'dbconf.php';
//echo "<pre>";
//print_r ($_GET);
//echo "</pre>";
// Variables to connect to the database
//    $dbhost = '127.0.0.1';
//    $dbname = 'asterisk';
//    $dblogin = 'root';
//    $dbpassword = 'Rjxthuf28';
//    $charset = 'utf8';
// API variables
    $phone = $_GET['phone'];	 			//Номер телефона кому звоним
    $agent = $_GET['agent'];				//Номер очереди
    $a = 'queue show '.$agent;				//Комадна CLI для получения массива
    //$agent = '2010';							//Номер очереди
    // $context = $_GET['context'];			//Имя внутреннего контекста Asterisk
    $context = 'from-internal'; 				//Имя внутреннего контекста Asterisk
    $acc_name = $_GET['order']; 			//Номер заказа или прочее (Отображается в поле "Account" в таблице CDR)
    //$userfield = $_GET['userfield'];			//Ползовательское поле (userfield) (Отображается в поле "userfield" в таблице CDR)
    $userfield = 'test';            //Ползовательское поле (userfield) (Отображается в поле "userfield" в таблице CDR)
    $login = $_GET['login'];		   		//Логин для авторизации(FreePBX)
    $password = sha1($_GET['password']);    //Пароль для авторизации
    $checkUser = checkUser($login, $password, $dbhost, $dbname, $dblogin, $dbpassword, $charset);
    //$cli_queue_free_agents = cli_queue_free_agents($a);
    // echo "<br>";
    // echo $phone, $agent, $context, $acc_name, $userfield, $login, $password;
    
if ($checkUser != 'ok')
    {
        echo $checkUser;	//Вернет 001 если невыполненна авторизация
    }
        elseif (empty($_GET['phone']))
    {
            echo "003"; 	// Не указан номер телефона
    }	
        elseif ($checkUser == 'ok')
    {
        create_call($phone, $agent, $context, $acc_name, $userfield);
            echo "000";		//Выполненно удачно (Call файл создан)
    }
        else
    {
            echo "002";		//Нет свободных операторов. Выполнить паузу с таймаутом 5 секунд
	      //echo 'Нет свободных операторов! Свободных операторов сейчас; ', $cli_queue_free_agents;
    }
?>
