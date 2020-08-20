<?php
namespace predicted\api;
//require_once 'dbconf.php';
use PDO;
require_once 'classes/notification.php';
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
    "queue show 2011",/* 1 группа */ 
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
    
function cli_call_queue ($a)
//Количество ожидающих в очереди
    {
    $result = get_cli_result ($a);
	$dump = var_export ($result,true);
	$queue = explode (' ',$dump);
	$queue_total = $queue[5];
		return $queue_total; 
           exit; 
    }

function cli_queue_free_agents ($a)
//Количество свободных операторов в очереди
    {
    $result = get_cli_result ($a);
	$dump = var_export ($result,true);
    print_r($dump);
	$member = mb_substr_count($dump,"Not in use");
        return $member;
           exit;
    }

function cli_call_list_queue ($a)
//Список звонков в очереди
    {
    $result = get_cli_result ($a);
	$dump = var_export ($result,true);
	$queue = explode ('C',$dump);
	$queue_total = $queue[2];
        return $queue_total;
           exit;
    }

function create_call($phone, $agent, $context, $acc_name, $userfield){
//Создать call файл
	//$userfield = 'debug';
	$originate = "Action: Originate 
Set: CDR(userfield)=$userfield
Channel: Local/$phone@from-internal
MaxRetries: 0
RetryTime: 1
WaitTime: 30
Context: $context
Extension: $agent
Callerid: $phone 
Account: $acc_name
Priority: 1";
	#sleep(4);
	$file = fopen("/var/www/dialer/calltmp/predicted/".$phone.".call", "w");
// записываем в файл текст
	fwrite($file, $originate);
 // закрываем
	fclose($file);
	$status = 'ok';
//записываем в лог
	$logfile = './log';
	$timestamp = date("dS of F Y h:I:s A");
	$logstr = $timestamp.' '.$originate;
	file_put_contents($logfile, $logstr);
	return $status;
	}

function queue_load ($a){
//соотношение колличество звонков в очереди к свободным операторам
	$cli_call_queue  = cli_call_queue($a);
	$cli_queue_free_agents  = cli_queue_free_agents($a);
		echo $cli_call_queue,' ',$cli_queue_free_agents;
	}

function monitoring($a, $login, $password, $dbhost, $dbname, $dblogin, $dbpassword, $charset){
//Мониторинг состояний
	echo 'Статус авторизации: ', checkUser($login, $password, $dbhost, $dbname, $dblogin, $dbpassword, $charset);
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

function connectDB($dbhost, $dbname, $dblogin, $dbpassword, $charset){
//Подключение к базе данных
    $dsn = "mysql:host=$dbhost;dbname=$dbname;charset=$charset";
    $opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $dblogin, $dbpassword, $opt);
        return $pdo;
}

function checkUser ($login, $password, $dbhost, $dbname, $dblogin, $dbpassword, $charset) {
//Проверка существования пары логин и пароль в базе данных(Проверка авторизации). 
if (empty($login or $password)){
	return ("not authorized");
	}
    $connection = connectDB($dbhost, $dbname, $dblogin, $dbpassword, $charset);
    //$result_set = $connection->query("SELECT password_sha1 FROM ampusers WHERE username = '$login'");
    $result_set = $connection->query("SELECT password FROM users WHERE login = '$login'");
    $user = $result_set->fetch(PDO::FETCH_ASSOC);
    //$real_password = $user['password_sha1'];
    $real_password = $user['password'];
if ($real_password == $password){
	$result = 'ok';
		return $result;
		}
	$result = '001'; // Не выполненна авторизациия
		//$result = print_r($real_password, $password);
		return $result;
}

function getUserID($login, $dbhost, $dbname, $dblogin, $dbpassword, $charset){
// Получить ID учетной записи в базе данных
	$connection = connectDB($dbhost, $dbname, $dblogin, $dbpassword, $charset);
	$result_set  = $connection->query("SELECT id_users FROM users WHERE login = '$login'");
	$id = $result_set->fetch(PDO::FETCH_ASSOC);
	$result = var_export($id['id'],true);
	    return $result;
}

function deleteIdRow($idRow, $dbhost, $dbname, $dblogin, $dbpassword, $charset) {
//Удалить строку из таблици UsersCall
    $connection = connectDB($dbhost, $dbname, $dblogin, $dbpassword, $charset);
    $sql = 'DELETE FROM usersCall WHERE id_users = :id';
 $query = $connection->prepare($sql);
  // $stmt = $this->pdo->prepare($sql);
  $query->execute([':id' => $idRow]);
  $alert = new Notification();
  $result = $alert->success('SUCCESS ','User deleted!','','');
  return $result;
// return $query->rowCount();
}
function deleteUser($idRow, $dbhost, $dbname, $dblogin, $dbpassword, $charset) {
//Удалить пользователя из таблици Users
    $connection = connectDB($dbhost, $dbname, $dblogin, $dbpassword, $charset);
    $sql = 'DELETE FROM users WHERE id_users = :id';
 $query = $connection->prepare($sql);
  // $stmt = $this->pdo->prepare($sql);
  $query->execute([':id' => $idRow]);
 return $query->rowCount();
}

function getAccessMode ($login, $dbhost, $dbname, $dblogin, $dbpassword, $charset)
//Определить группу доступа пользователя(access mode)  administrator/user
    {
        $connection = connectDB($dbhost, $dbname, $dblogin, $dbpassword, $charset);
        $sql = "SELECT access_mode FROM users WHERE login = '$login'";
        $result_set  = $connection->query($sql);
        $access_mode = $result_set->fetch(PDO::FETCH_ASSOC);
        $result = $access_mode['access_mode'];
            return $result;
    }

function checkUserExist ($newUserLogin, $dbhost, $dbname, $dblogin, $dbpassword, $charset)
//Проверка существования пользвателя
    {
        $connection = connectDB($dbhost, $dbname, $dblogin, $dbpassword, $charset);
        $sql = "select count(*) from report where login ='$newUserLogin'";
        $result = $connection->query($sql)->fetchColumn();
            return $result;
    }
function addNewUser ($firstName,
                     $lastName,
                     $newUserLogin,
                     $email,
                     $newUserPassword,
                     $confirmNewUserPassword,
                     $accessMode,
                     $token,
                     $dbhost, $dbname, $dblogin, $dbpassword, $charset)
//Добавить нового пользователя
{
    //login
    $connection = connectDB($dbhost, $dbname, $dblogin, $dbpassword, $charset);
    $sql = "SELECT login FROM users WHERE login = '$newUserLogin'";
    $result_set  = $connection->query($sql);
    $fetch = $result_set->fetch(PDO::FETCH_ASSOC);
    $fetchLogin = var_export($fetch['login'],true);
    //email
    $sql = "SELECT e_mail FROM users WHERE e_mail = '$email'";
    $result_set  = $connection->query($sql);
    $fetch = $result_set->fetch(PDO::FETCH_ASSOC);
    $fetchEmail = var_export($fetch['e_mail'],true);
    if($fetchLogin !== 'NULL' or $fetchEmail !== 'NULL')
    {
        $alert = new Notification();
        $result = $alert->danger('ERROR ','Login or e-mail already exist!','','');
        return $result;
    }
    //password
    if($newUserPassword !== $confirmNewUserPassword)
    {
        $alert = new Notification();
        $result = $alert->danger('ERROR ','Password and confirm password no match!','','');
        return $result;
    }   
    //token
    $sql = "SELECT token FROM users WHERE token = '$token'";
    $result_set  = $connection->query($sql);
    $fetch = $result_set->fetch(PDO::FETCH_ASSOC);
    $fetchToken = var_export($fetch['token'],true);
    //echo $fetchToken,$token;
    if($token !== '' && $fetchToken !== $token)
    {
        $alert = new Notification();
        $result = $alert->danger('ERROR ','Token already exist!','','');
        return $result;       
    }
    //add user
    $newUserPassword = sha1($newUserPassword);
    $sql = "INSERT INTO users SET 
            login='$newUserLogin', 
            password='$newUserPassword', 
            e_mail='$email', 
            name='$firstName',
            last_name='$lastName',
            access_mode='$accessMode', 
            token='$token'"; 
    $result_set = $connection->prepare($sql);
    $fetch = $result_set->execute();
    $alert = new Notification();
        $result = $alert->success('SUCCESS ',' User added !','','');
        return $result;   
}

function editUser(
	    $idEdit,
	    $firstName,
        $lastName,
        $newUserPassword,
        $confirmNewUserPassword,
        $accessMode,
        $token,
        $dbhost, $dbname, $dblogin, $dbpassword, $charset)
{
    //password

    if($newUserPassword !== $confirmNewUserPassword)
    {
        //echo $newUserPassword.', '.$confirmNewUserPassword;
        $alert = new Notification();
    	$result = $alert->danger('ERROR ','Password and confirm password no match!','','');
        //'<label class="text-danger text-center font-weight-bold" for="addUser">Password and confirm password no match!</label>';
        return $result;
    }
	if($newUserPassword == '' && $confirmNewUserPassword == '')
	{
		$sql = "UPDATE users SET name='$firstName', last_name='$lastName', access_mode='$accessMode', token='$token' WHERE id_users = '$idEdit'";
	}
		else
		{
            $newUserPassword = sha1($newUserPassword);
            //echo $newUserPassword;
			$sql = "UPDATE users SET name='$firstName',
									 last_name='$lastName', 
									 access_mode='$accessMode', 
									 token='$token', 
									 password='$newUserPassword' 
							   WHERE id_users = '$idEdit'";
			//echo $sql;
		}
	//token
    $connection = connectDB($dbhost, $dbname, $dblogin, $dbpassword, $charset);
    $sqltoken = "SELECT token FROM users WHERE token = '$token'";
    $result_set  = $connection->query($sqltoken);
    $fetch = $result_set->fetch(PDO::FETCH_ASSOC);
    $fetchToken = var_export($fetch['token'],true);
    $sqlrows = "SELECT count(*) FROM `users` WHERE token = '$token'"; 
    $result = $connection->prepare($sqlrows); 
    $result->execute(); 
    $items = $result->fetchColumn(); 
    if($token !== '' && $fetchToken !== $token && $items >= '2')
    {
        $alert = new Notification();
        $result = $alert->danger('ERROR ','Token already exist!','','');
        //$result = '<label class="text-danger text-center font-weight-bold" for="addUser">Token already exist!</label>';
        return $result;
    }
    $connection = connectDB($dbhost, $dbname, $dblogin, $dbpassword, $charset);
    $result_set  = $connection->prepare($sql);
    $fetch = $result_set->execute();	
    $alert = new Notification();
    $result = $alert->success('SUCCESS ','User updated!','','');
    //$result = '<label class="text-success text-center font-weight-bold" >User updated!</label>';
    return $result;
}


//function regUser ($login, $password) {
//        $mysqli = connectDB();
//        $mysqli->query("INSERT INTO ampusers ('username', 'password_sha1') VALUES ('$login','$password')");
//        closeDB($mysqli);

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