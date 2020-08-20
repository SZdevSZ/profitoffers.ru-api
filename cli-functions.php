<?php
//predicted
#$a = 'queue show 2010';
//require_once 'api/ApiClass.php';
function get_cli_result ($a)
{
    $a=str_replace("  ", " ",trim(strtolower($a)));
    /* переменую $b мы будем использовать в кострукции switch */
    $b=-1;
    /* Массив в котором мы будем хранить значения полученные из Астериска */
    $result = array ();
    /* массив где мы храним cli команды */
    $ar = array ("manager reload",
    "manager set debug",
    "manager show command ",
    "manager show event ", /* здесь простые команды заканчиваются, элемент массива № 3  */
    "manager show eventq",  /* 1 группа */
    "queue show 2010",/* 1 группа */ 
    "manager show user ",   /* 1 группа */
    "manager show commands",      /* 2 группа */
    "manager show events",        /* 2 группа */
    "manager show users",               /* Без группы */
    "manager show connected"            /* Без группы */
    );
    for ($i=0;$i<count($ar);$i++) 
        {
        if (strpos($a,$ar[$i])!==false) 
            {
                $b = $i;
            } 
        }
    switch (true)
        {
        case $b==-1: /* если мы не нашли команду то мы уходим из функции выдавая пустой массив  */
            $result[0]["Name"] = "Error";
            $result[0]["Function"] = "cli";
            $result[0]["Number"] = "00001";
            $result[0]["Description"] = "Не найдена CLI команда";
            return $result;
            break;
        case $b<=3:
            return asterisk_cli_exec($a);
            break; 
        case $b<=6:
            $t = asterisk_cli_exec($a); /* Получаем результаты выполнения команды в временный массив $t */
            while (isset($t[0]))            
                {
                    if ($t[0]!="")      /* Проверяем не является ли текущая строка пустой*/
                        {
                            unset($m);    /* Уничтожаем переменную чтобы при повторном обращении не появились остаточные данные */
                            $m = explode(":", $t[0]); /* Делим строку на массив используя разделитель символ ":" */
                            if (isset($m[1])) /* Проверяем существует ли значение, если его нет то или это не наши данные а просто строка или это данные с пустым значением что нам также не нужно */
                                {
                                    $n = $m[1]; /* Присваиваем в переименую начало значения */
                                    for ($i=2;$i<count($m);$i++) 
                                        {
                                            $n.=":".$m[$i];  /* Если имеются еще элементы массива то мы восстанавливаем значение */
                                        } 
                                    $result[trim($m[0])]=trim($n); /* Присваиваем значение и ключ в наш массив */
                                }            
                        }
                    array_splice($t, 0, 1);
                }
            return $result; 
        break; 
        }
    }  




function asterisk_cli_exec ($a)
    {
        exec("asterisk -rx '$a'", $b);
        return $b;
    }
    
function asterisk_cli_passthru ($a)
    {
        passthru("asterisk -rx '$a'", $b);
        return $b;
    }
    
function cli_call_queue ($a)
//колличество ожидающих звонков в очереди
{
    $result = get_cli_result ($a);
	$dump = var_export ($result,true);
	$queue = explode (' ',$dump);
	$queue_total = $queue[5];
		return $queue_total; 
           break; 
}

function cli_queue_free_agents ($a)
//Количество свободных операторв очереди
    {
    $result = get_cli_result ($a);
	$dump = var_export ($result,true);
	$member = mb_substr_count($dump," in use");
        return $member;
           break;
    //$action = new AMIActions();
    //$num = $action->getCountMemberOnline($queuenum);
    return $num;
    }
function cli_call_list_queue ($a)
// функция списка звонков в очередь
    {
    $result = get_cli_result ($a);
	$dump = var_export ($result,true);
	$queue = explode ('C',$dump);
	$queue_total = $queue[2];
        return $queue_total;
           break;
    }

function create_call($phone, $queue, $context, $acc_name, $idLog){
//Функция для создания call файла
        $separate='+';
//Action: Originate
        //Variable: idLog=$_GET['id']
        //Set: CDR(userfield)=PREDICTED
	$originate = "Channel: Local/$phone@from-internal
Set: IDLOG=$idLog
Set: CDR(userfield)=$acc_name
MaxRetries: 0
RetryTime: 1
WaitTime: 30
Context: $context
Extension: $queue
Callerid: $acc_name$separate$phone 
Account: $acc_name
Priority: 1";
//	sleep(4);
	$file = fopen("/var/www/predicted/calltmp/$phone.call", "w");
// записываем в файл текст
	fwrite($file, $originate);
 // закрываем
	fclose($file);
	$status = 'ok';
	updCallStats($queue);
		return $status;
	}

function queue_load ($a){
//соотношение колличество звонков в очереди к свободным операторам
	$cli_call_queue  = cli_call_queue($a);
	$cli_queue_free_agents  = cli_queue_free_agents($a);
		echo $cli_call_queue,' ',$cli_queue_free_agents;
	}

function monitoring($a){
//Мониторинг состояний
	//echo 'Статус авторизации: ', checkUser($login, $password);
	echo "</br>";
	echo 'Звонков в очереди: ',cli_call_queue ($a);
	echo "<br/>";
	echo 'Свободных операторов в очереди: ',cli_queue_free_agents ($a);
	echo "<br/>";
	echo 'Соотношение звонков в очереди и св. операторы ',queue_load($a);
	$cli_call_list_queue = cli_call_list_queue($a);
		if (!empty($cli_call_list_queue)){
	echo "<pre>";
	echo 'Список звонков в очереди:';
	echo 'C',cli_call_list_queue ($a,"<br/>");
	echo "</pre>";
	echo "</br>";
	}
}
////////ЭТО НЕ УДАЛЯТЬ///////////
#	echo create_call('89881161760','2010','from-internal','test');
////////ЭТО НЕ УДАЛЯТЬ///////////

#var_dump (cli_call_list_queue('queue show 2010','<br/>'));

#$dump = var_export (cli('queue show 2010'),true);
#$queue = explode (' ',$dump);
#$queue_total = $queue[5];
#echo $queue_total;
 
function connectDB(){
return new PDO('mysql:host=127.0.0.1;dbname=predicted;charset=utf8', 'root', 'Rjxthuf28');
}

function connectDBCallLog(){
return new PDO('mysql:host=127.0.0.1;dbname=utils;charset=utf8', 'root', 'Rjxthuf28');
}

function checkUser ($login, $password) {
if (empty($login or $password)){
	return ("not authorized");
	}
$connection = connectDB();
$result_set = $connection->query("SELECT password FROM users WHERE login = '$login'");
$user = $result_set->fetch(PDO::FETCH_ASSOC);
$real_password = $user['password'];
//echo 'real_password , ', $real_password, 'password ', $password;
//echo "</br>"; 
if ($real_password == $password){
	$result = 'ok';
		return $result;
		}
	$result = '001'; // Не выполненна авторизациия
		return $result;
}

function getCallStats($extension){
//возвращает статистику звонков отправленных используя функцию create_call
    if (empty($extension)){
        return ("Please send extension");
    }	
    $connection = connectDB();
    $result_set = $connection->query("SELECT callStats FROM callStats  WHERE extension = '$extension'");
    $numberCalls = $result_set->fetch(PDO::FETCH_ASSOC);
    $result = $numberCalls['callStats'];
        return $result;
}

function updCallStats($extension){
//обновляет +1 статистику звонков отправленных используя функцию create_call
    if (empty($extension)){
        return ("Please send extension");
    }	
    $connection = connectDB();
    $result_set = $connection->query("SELECT callStats FROM callStats  WHERE extension = '$extension'");
    $numberCalls = $result_set->fetch(PDO::FETCH_ASSOC);
    $callStats = $numberCalls['callStats'];
    $callStats++;
    $sql = "UPDATE callStats SET callStats ='$callStats' WHERE extension='$extension'";
    $query = $connection->prepare($sql);
    $res = $query->execute();
}
function clearCallStats($extension){
// очищает статистику звонков отправленных используя функцию create_call
    if (empty($extension)){
        return ("Please send extension");
    }	
    $connection = connectDB();
    $result_set = $connection->query("SELECT callStats FROM callStats  WHERE extension = '$extension'");
    $numberCalls = $result_set->fetch(PDO::FETCH_ASSOC);
    $callStats = $numberCalls['callStats'];
    $callStats = 0;
    $sql = "UPDATE callStats SET callStats ='$callStats' WHERE extension='$extension'";
    $query = $connection->prepare($sql);
    $res = $query->execute();
    $res = 0;
        return $res;
}

function addCallLog ($dialNumber, $account, $ext, $uid, $sipHeader)
{
    $connection = connectDBCallLog();
    $sql = "INSERT INTO callLog SET 
            dialNumber='$dialNumber', 
            account='$newUserPassword', 
            ext='$ext', 
            uid='$uid',
            sipHeader='$sipHeader'";
    $result_set = $connection->prepare($sql);
    $fetch = $result_set->execute();
    $res = 'ok';
    return $res;
}


/*function connectDB() {
return new mysqli ("phone.octotrade.ru", "predicted", "FwLj7WPMZZ", "asterisk" );
    }

function closeDB ($mysqli) {
$mysqli->close();
     }

function regUser ($login, $password) {
$mysqli = connectDB();
$mysqli->query("INSERT INTO ampusers ('username', 'password_sha1') VALUES ('$login','$password')");
closeDB($mysqli);
     }

function checkUser ($login, $password) {
if (($login == "") || ($password == "")) {
	echo 'login and/or password empty';
}
$mysqli = connectDB();
$result_set = $mysqli->query("SELECT password_sha1 FROM ampusers WHERE username = '$login'");
$user = $result_set->fetch_assoc();
$real_password = $user['password_sha1'];
closeDB($mysqli);

if ($real_password == $password){
	$result = 'ok';
		return $result;
}
echo 'login or password not correct';
    }
*/
?>
