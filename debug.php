<?php
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
//	001 Не выполненна автоизация
//	002 Нет свободных операторов. Выполнить паузу с таймаутом 5 секунд
//	003 Не указан номер телефона
//	Пример GET запроса http://phone.octotrade.ru/predicted/?login=predicted&password=FwLj7WPMZZ&phone=89881161760&order=test
// Включить вывод сообщений об ощибках
//ini_set('display_errors','On'); 
//error_reporting(E_ALL); 

$a = 'queue show 2010';
include 'cli-functions.php';

//echo "<pre>";
//print_r ($_GET);
//echo "</pre>";
$a = 'queue show 2010';				//Комадна CLI для получения массива
$phone = $_GET['phone'];			//Номер телефона кому звоним
$queue = '700';					//Номер очереди
$context = 'from-internal'; 		//Имя внутреннего контекста Asterisk
$acc_name = $_GET['order']; 		//Номер заказа или прочее
$login = $_GET['login'];			//Логин для авторизации(FreePBX)
$password = sha1($_GET['password']);//Пароль для авторизации

$checkUser = checkUser($login, $password);
$cli_queue_free_agents = cli_queue_free_agents($a);
//echo "</br>";
//echo $checkUser;
//echo "</br>";
//echo $cli_queue_free_agents;
//echo "</br>";

echo "<pre>";
print_r(monitoring($a));
echo "</pre>";
?>
