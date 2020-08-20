<?php
namespace predicted\api;
use PDO;
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', 1);
require_once 'ApiClass.php';
require_once 'LoggerClass.php';
/**
* authorization API
*/
class AuthAPI extends AMIActions
{
	private $systemLog = 'AUTH_API';
    private $host = "localhost";
    private $DBName = "utils";
    private $username = "ams";
    private $password = "amsAMS!@#";
    private $charset = 'utf8';
    public $conn;

    private function getConnection(){
    	$dsn = 'mysql:host='.$this->host.'.;dbname='.$this->DBName.';charset='.$this->charset.'';
    	$opt = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    	];
    	$pdo = new PDO($dsn, $this->username, $this->password, $opt);
        return $pdo;
    }

    public function checkToken($token)
    {   
    	$objLogger = new Logger(); 
    	$connection = $this->getConnection();
    	$sql = "SELECT login FROM users WHERE token = '$token'";
    	$result_set  = $connection->query($sql);
        $fetch = $result_set->fetch(PDO::FETCH_ASSOC);
    	//$fetchToken = var_export($fetch['login'],true);
    	$fetchToken = $fetch['login'];
		return $fetchToken;
    }
}
//$authAPIobj = new AuthAPI();
//$result = $authAPIobj->checkToken('BJkjLlK67IgKKH1');
//print_r($result);
?>