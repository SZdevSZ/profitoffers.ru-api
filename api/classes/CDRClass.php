<?php
namespace predicted\api;
//use predicted\api;
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', 1);
/**
* For working with CDR
*/
 class CDR
 {
 	public $searchPath = '/var/spool/asterisk/monitor/';
 	protected $homeURL;
 	/**
	* Set home URL
	*/
	public function __construct() {
         $res = $this->homeURL = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].'/files/';
         return $res;
    }
    /**
	* Change to search path (by default /var/spool/asterisk/monitor/)
	* @param $newPath
	* @return   
	*/
 	public function setSearchPath($newPath)
 	{
 			$this->searchPath = $newPath;
 			return $this->searchPath;
 	}
 	/**
 	* Parse $fileName to dir
 	* @param $fileName
	* @return string, dir
	*/
 	public function getParseDir($fileName)
 	{
 		$parseArr = explode("-", $fileName);
 		sscanf($parseArr[3], "%4s%2s%2s", $year, $month, $day); 
 		$result = $year.'/'.$month.'/'.$day.'/'; 
 		return $result;
 	} 	
 	/**
 	* Search sound file
	* @param $fileName
	* @return string, link 
	*/
 	public function searchFile($fileName)
 	{
 		$searchDir = $this->searchPath.$this->getParseDir($fileName).$fileName;
 		$folder = $this->searchPath.$this->getParseDir($fileName);
 		$fp=opendir($folder);
 		while($cv_file=readdir($fp)){
 			if ($cv_file == $fileName){
 				$result =$this->homeURL.$this->getParseDir($fileName).$fileName;
 				return $result;
  	 		}
 	 			else{
 					$result = 'File not found';
 				}
 		}
 		return $result;
 	}
}

/*CODE*/
// $cdr = new CDR();
// $fileName = 'out-79632641297-998-20200602-210201-1591120921.13601.wav';
////$res = $cdr->getParseDir('out-79632641297-998-20200602-210201-1591120921.13601.wav');
////var_dump($res);
//echo'<br>';
//print_r($cdr->searchFile($fileName));

?>