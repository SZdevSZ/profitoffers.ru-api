<?php
namespace predicted\api;
use PDO;
/**
*Logging 
*$obj = new Logger();
*$res = $obj->addMessage('type','system',$ar) ;
*
*$messageLog = ['get=' => $_GET, 'response' => $num];
*$addLogging = $objLogger->addMessage($typeLog, $systemLog, $messageLog);
*/
class Logger 
{
	public $argDB = [
					'dbLogin' => 'ams',
	    			'dbPassword' => 'amsAMS!@#',
	    			'dbHost' => '127.0.0.1',
	    			'dbName' => 'utils',
	    			'dbTable' => 'log',
	    			'dbCharset' => 'utf8'
	    			];
	
	function addMessage($typeLog,$systemLog,$messageLog)
	{
			//$messageLog = implode(',', $messageLog);
			$messageLog = json_encode($messageLog);
		    $connection = new PDO('mysql:host='.$this->argDB['dbHost'].';dbname='.$this->argDB['dbName'].';charset='.$this->argDB['dbCharset'].'', $this->argDB['dbLogin'], $this->argDB['dbPassword']);
		    $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    $sql = "INSERT INTO ".$this->argDB['dbTable']." SET 
		    type='".$typeLog."',
		    system='".$systemLog."',
		    message='".$messageLog."'";
    		$result_set = $connection->prepare($sql);
    		$fetch = $result_set->execute();
			$connection = null;
    	    $result = 'success';
		    return $result;
	}
}
?>