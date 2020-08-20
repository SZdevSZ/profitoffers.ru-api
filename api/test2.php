<?php
namespace predicted\api;
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', 1);
require_once 'classes/ApiClass.php';
require_once 'classes/LoggerClass.php';

////////// CODE START //////////
$queuesMGMT = new AMIActions();
//
echo '<p>тестирование Originate</p>';
echo '<br>';
////Local/89881161760@from-internal
$callParamss = ['channel' => '89881161760',
			  'extension' => '751',
			  'callerid' => '89881161760',
			  'async' => FALSE,//-
			  'account' => 'SZ',
			  'variables' => [
			  'IDLOG' => 'szidlog',
			  'CDR(userfield)' => 'szuserfield']];
$callParamss2 = ['channel' => 'Local/89881161760@from-internal',
			  'extension' => '501',
			  'callerid' => ''
			  ];
$createCallTask = $queuesMGMT->createCallTask($callParamss);
echo '<pre>';
print_r($createCallTask);

//print_r($callParams);

echo '</pre>';

//echo'<p>Добавили пользователя 751</p>';
//$addMember = $queuesMGMT->addMember(2010,751);
//echo'<pre>';
//print_r($addMember);
//echo'<pre>';

//echo'<p>Список SIP пиров</p>';
//$getSIPPeers = $queuesMGMT->getSIPPeers();
//echo'<pre>';
//print_r($getSIPPeers);
//echo'<pre>';

// echo'<p>Список всех агентов из очереди 2010</p>';
// $queueSt = $queuesMGMT->getMemberList(2010);
// echo'<pre>';
// print_r($queueSt);
// echo'<pre>';

//+
// echo'<p>Список всех очередей</p>';
// echo'<pre>';
// $queueList = $queuesMGMT->getQueuesList();
// print_r($queueList);
// echo'<pre>';


//echo'<p>Удалим агента 751 из очереди 2010</p>';
//$deleteMember = $queuesMGMT->deleteMember(2010,751);
//echo'<pre>';
//print_r($deleteMember);
//echo'<pre>';

//echo'<p>Список всех агентов из очереди 2010</p>';
//echo'<pre>';
//$queueSt = $queuesMGMT->getMemberList(2010);
//print_r($queueSt);
//echo'<pre>';

//echo'<p>Список агентов очереди 2010 которые свободны</p>';
//echo'<pre>';
//$queueMemberOnline = $queuesMGMT->getMembersListFree(2010);
//print_r($queueMemberOnline);
//echo'<pre>';

//echo'<p>Количество агентов очереди 2010 которые онлайн</p>';
//echo'<pre>';
//$queueCountMemberOnline = $queuesMGMT->getMembersCountFree(2010);
//print_r($queueCountMemberOnline);
//echo'<pre>';

//echo'<p>Статистика</p>';
//echo'<pre>';
//$queueStats = $queuesMGMT->getStatsQueue(2010);
//print_r($queueStats);
//echo'<pre>';

// echo'<p>Очистить статистику</p>';
// echo'<pre>';
// $resetStats = $queuesMGMT->resetStatsQueue(2010);
// print_r($resetStats);
// echo'<pre>';

//echo'---------ЛОГИРОВАНИЕ---------';
//$ar = array('123' => 123, '345' => 444, 'long string' => 'Qwerty Asdfghzxc KJKJ_kll');
//$obj = new Logger();
//$res = $obj->addMessage('1','2',$ar) ;
//echo'<br>';
//echo $res;

//echo'<p>Сняли пользователя 751 с паузы</p>';
//$unsetMemberPause = $queuesMGMT->unsetMemberPause(751);
//echo'<pre>';
//print_r($unsetMemberPause);
//echo'<pre>';

// echo'<p>Поставили пользователя 751 на паузу</p>';
// $setMemberPause = $queuesMGMT->setMemberPause(751);
// echo'<pre>';
// print_r($setMemberPause);
// echo'<pre>';

// echo'<p>Список тех кто на паузе в очереди 2010</p>';
// $getPausedMembersList = $queuesMGMT->getPausedMembersList(2010);
// echo'<pre>';
// print_r($getPausedMembersList);
// echo'<pre>';

//echo'<p>Количество принятых звонков агентов в очереди 2010</p>';
//$getStatsMembers = $queuesMGMT->getStatsMembers(2010);
//echo'<pre>';
//print_r($getStatsMembers);
//echo'<pre>';

//echo'<p> ALL </p>';
//$all = $queuesMGMT->all(2010);
//echo'<pre>';
//print_r($all);
//echo'<pre>';

echo "test";
////////// CODE END //////////
?>