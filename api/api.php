<?php
namespace predicted\api;
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', 1);
require_once 'classes/ApiClass.php';
require_once 'classes/LoggerClass.php';
require_once 'classes/AuthAPIClass.php';
require_once 'classes/CDRClass.php';
use PDO;

header('Content-Type: application/json; charset=utf-8');

//loger value
$typeLogInfo = 'INFO';
$typeLogAuth = 'AUTH';
$typeLogError = 'ERROR';
$systemLog = 'API';
//Response messages
$httpMessage200 = array(
			'http_response'=>array(
            'code'=>200,
            'description'=>'Success'));
$httpMessage400 = array(
			'http_response'=>array(
            'code'=>400,
            'description'=>'Bad Request'));
$httpMessage401 = array(
			'http_response'=>array(
            'code'=>401,
            'description'=>'Unauthorized'));
//Check authorization
$objLogger = new Logger();
$auth = new AuthAPI();
if (isset($_GET['token'])) {
	$checkUser = print_r($auth->checkToken($_GET['token']),TRUE);
	$currentAPIUser = array('auth' => array('user' => $checkUser));
	if ($auth->checkToken($_GET['token']) === NULL) {
		$resultMessage = array('result' => array(0 =>'error: Token does not exist'));
		$glueMessage = $httpMessage401 + $resultMessage + $currentAPIUser;
		$result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		print_r($result);
		$result = json_encode($glueMessage);
		$messageLog = ['get=' => $_GET, 'response=' => $result];
    	$addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
		unset($objLogger);
		exit;
	}

//create obj
$action =  new AMIActions();
//code
// $checkUser = print_r($auth->checkToken($_GET['token']),TRUE);
// $resultMessage = array('result' => array('user' => $checkUser));
// $glueMessage = $httpMessage200 + $resultMessage;
// $result = json_encode($glueMessage);
// $messageLog = ['get=' => $_GET, 'response=' => $result];
// $addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
switch ($_GET['action']) {
	case 'getQueuesList':
		$queueList = $action->getQueuesList();
		$resultMessage = array('result'=> $queueList);
		$glueMessage = $httpMessage200 + $resultMessage + $currentAPIUser;
		$result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		print_r($result);
		$result = json_encode($glueMessage);
		$messageLog = ['get=' => $_GET, 'response=' => $result];
    	$addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
		unset($queueList);
		unset($objLogger);
		break;
	case 'getMemberList':
		$resultMessage = array('result' => array(0 =>'error: The requested queue name does not exist or is invalid.'));
		if (isset($_GET['queue'])) {
			$queueList = $action->getQueuesList();
			if (in_array($_GET['queue'], $queueList)) {
				$memberList = $action->getMemberList($_GET['queue']);
		        $resultMessage = array('result'=> $memberList);
		        $glueMessage = $httpMessage200 + $resultMessage + $currentAPIUser;
		        $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		        print_r($result);
		        $result = json_encode($glueMessage);
		        $messageLog = ['get=' => $_GET, 'response=' => $result];
    	        $addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
		        unset($memberList);
		        unset($queueList);
		        unset($objLogger);
		        break;
			}			
		    $glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
		    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		    print_r($result);
		    $result = json_encode($glueMessage);
		    $messageLog = ['get=' => $_GET, 'response=' => $result];
    	    $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
		    unset($queueList);
		    unset($objLogger);
		    break;
		}
	    $glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
	    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	    print_r($result);
	    $result = json_encode($glueMessage);
	    $messageLog = ['get=' => $_GET, 'response=' => $result];
   	    $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
	    unset($queueList);
	    unset($objLogger);
	    break;

	case 'getMembersListFree':
		$resultMessage = array('result' => array(0 =>'error: The requested queue name does not exist or is invalid.'));
		if (isset($_GET['queue'])) {
			$queueList = $action->getQueuesList();
			if (in_array($_GET['queue'], $queueList)) {
				$membersListFree = $action->getMembersListFree($_GET['queue']);
		        $resultMessage = array('result'=> $membersListFree);
		        $glueMessage = $httpMessage200 + $resultMessage + $currentAPIUser;
		        $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		        print_r($result);
		        $result = json_encode($glueMessage);
		        $messageLog = ['get=' => $_GET, 'response=' => $result];
    	        $addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
		        unset($membersListFree);
		        unset($queueList);
		        unset($objLogger);
		        break;
			}			
		    $glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
		    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		    print_r($result);
		    $result = json_encode($glueMessage);
		    $messageLog = ['get=' => $_GET, 'response=' => $result];
    	    $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
		    unset($queueList);
		    unset($objLogger);
		    break;
		}
	    $glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
	    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	    print_r($result);
	    $result = json_encode($glueMessage);
	    $messageLog = ['get=' => $_GET, 'response=' => $result];
   	    $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
	    unset($queueList);
	    unset($objLogger);
	    break;
	case 'getMembersCountFree':
		$resultMessage = array('result' => array(0 =>'error: The requested queue name does not exist or is invalid.'));
		if (isset($_GET['queue'])) {
			$queueList = $action->getQueuesList();
			if (in_array($_GET['queue'], $queueList)) {
				$membersCountFree = $action->getMembersCountFree($_GET['queue']);
		        $resultMessage = array('result'=> $membersCountFree);
		        $glueMessage = $httpMessage200 + $resultMessage + $currentAPIUser;
		        $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		        print_r($result);
		        $result = json_encode($glueMessage);
		        $messageLog = ['get=' => $_GET, 'response=' => $result];
    	        $addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
		        unset($membersCountFree);
		        unset($queueList);
		        unset($objLogger);
		        break;
			}			
		    $glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
		    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		    print_r($result);
		    $result = json_encode($glueMessage);
		    $messageLog = ['get=' => $_GET, 'response=' => $result];
    	    $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
		    unset($queueList);
		    unset($objLogger);
		    break;
		}
	    $glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
	    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	    print_r($result);
	    $result = json_encode($glueMessage);
	    $messageLog = ['get=' => $_GET, 'response=' => $result];
   	    $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
	    unset($queueList);
	    unset($objLogger);
	    break;
	case 'getMembersListBusy':
		$resultMessage = array('result' => array(0 =>'error: The requested queue name does not exist or is invalid.'));
		if (isset($_GET['queue'])) {
			$queueList = $action->getQueuesList();
			if (in_array($_GET['queue'], $queueList)) {
				$membersListBusy = $action->getMembersListBusy($_GET['queue']);
		        $resultMessage = array('result'=> $membersListBusy);
		        $glueMessage = $httpMessage200 + $resultMessage + $currentAPIUser;
		        $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		        print_r($result);
		        $result = json_encode($glueMessage);
		        $messageLog = ['get=' => $_GET, 'response=' => $result];
    	        $addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
		        unset($membersListBusy);
		        unset($queueList);
		        unset($objLogger);
		        break;
			}			
		    $glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
		    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		    print_r($result);
		    $result = json_encode($glueMessage);
		    $messageLog = ['get=' => $_GET, 'response=' => $result];
    	    $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
		    unset($queueList);
		    unset($objLogger);
		    break;
		}
	    $glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
	    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	    print_r($result);
	    $result = json_encode($glueMessage);
	    $messageLog = ['get=' => $_GET, 'response=' => $result];
   	    $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
	    unset($queueList);
	    unset($objLogger);
	    break;
	case 'getMembersCountBusy':
		$resultMessage = array('result' => array(0 =>'error: The requested queue name does not exist or is invalid.'));
		if (isset($_GET['queue'])) {
			$queueList = $action->getQueuesList();
			if (in_array($_GET['queue'], $queueList)) {
				$membersCountBusy = $action->getMembersCountBusy($_GET['queue']);
		        $resultMessage = array('result'=> $membersCountBusy);
		        $glueMessage = $httpMessage200 + $resultMessage + $currentAPIUser;
		        $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		        print_r($result);
		        $result = json_encode($glueMessage);
		        $messageLog = ['get=' => $_GET, 'response=' => $result];
    	        $addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
		        unset($membersCountBusy);
		        unset($queueList);
		        unset($objLogger);
		        break;
			}			
		    $glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
		    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		    print_r($result);
		    $result = json_encode($glueMessage);
		    $messageLog = ['get=' => $_GET, 'response=' => $result];
    	    $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
    	    unset($queueList);
		    unset($objLogger);
		    break;
		}
	    $glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
	    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	    print_r($result);
	    $result = json_encode($glueMessage);
	    $messageLog = ['get=' => $_GET, 'response=' => $result];
   	    $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
	    unset($objLogger);
	    break;
	case 'getPausedMembersList':
		$resultMessage = array('result' => array(0 =>'error: The requested queue name does not exist or is invalid.'));
		if (isset($_GET['queue'])) {
			$queueList = $action->getQueuesList();
			if (in_array($_GET['queue'], $queueList)) {
				$pausedMembersList = $action->getPausedMembersList($_GET['queue']);
		        $resultMessage = array('result'=> $pausedMembersList);
		        $glueMessage = $httpMessage200 + $resultMessage + $currentAPIUser;
		        $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		        print_r($result);
		        $result = json_encode($glueMessage);
		        $messageLog = ['get=' => $_GET, 'response=' => $result];
    	        $addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
		        unset($pausedMembersList);
		        unset($objLogger);
		        break;
			}			
		    $glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
		    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		    print_r($result);
		    $result = json_encode($glueMessage);
		    $messageLog = ['get=' => $_GET, 'response=' => $result];
    	    $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
		    unset($objLogger);
		    break;
		}
	    $glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
	    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	    print_r($result);
	    $result = json_encode($glueMessage);
	    $messageLog = ['get=' => $_GET, 'response=' => $result];
   	    $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
	    unset($objLogger);
	    break;
	case 'getSIPPeers':
		$SIPPeers = $action->getSIPPeers();
		$resultMessage = array('result'=> $SIPPeers);
		$glueMessage = $httpMessage200 + $resultMessage + $currentAPIUser;
		$result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		print_r($result);
		$result = json_encode($glueMessage);
		$messageLog = ['get=' => $_GET, 'response=' => $result];
    	$addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
		unset($SIPPeers);
		unset($objLogger);
		break;
	case 'addMember':
		$resultMessage = array('result' => array(0 =>'error: The requested queue name or agent name does not exist or is invalid.'));
		$SIPPeers = $action->getSIPPeers();
		$queueList = $action->getQueuesList();
		//if (isset($_GET['queue']) && isset($_GET['agent']) && in_array($_GET['queue'], $queueList) && array_key_exists($_GET['agent'], $SIPPeers)) {
		if (isset($_GET['queue']) && isset($_GET['agent']) && in_array($_GET['queue'], $queueList) ) {
			$memberList = $action->getMemberList($_GET['queue']);
			if (in_array($_GET['agent'], $memberList)){
				$resultMessage = array('result' => array(0 =>'error: Unable to add interface: Already there'));
			    $glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
	    	    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	    	    print_r($result);
	    	    $result = json_encode($glueMessage);
	    	    $messageLog = ['get=' => $_GET, 'response=' => $result];
   	    	    $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
	    	    unset($SIPPeers);
	    	    unset($queueList);
	    	    unset($objLogger);
			    break;
			}
				$addMember = $action->addMember($_GET['queue'],$_GET['agent']);
				$resultMessage = array('result'=> array(0 => $addMember));
			    $glueMessage = $httpMessage200 + $resultMessage + $currentAPIUser;
			    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
			    print_r($result);
			    $result = json_encode($glueMessage);
			    $messageLog = ['get=' => $_GET, 'response=' => $result];
    		    $addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
			    unset($SIPPeers);
	    	    unset($queueList);
			    unset($addMember);
			    unset($objLogger);
			    break;
			}
			$glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
	    	$result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	    	print_r($result);
	    	$result = json_encode($glueMessage);
	    	$messageLog = ['get=' => $_GET, 'response=' => $result];
   	    	$addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
	    	unset($SIPPeers);
	    	unset($queueList);
	    	unset($objLogger);
			break;
	case 'deleteMember':
		$resultMessage = array('result' => array(0 =>'error: The requested queue name or agent name does not exist in queue or is invalid.'));
		$getMemberList = $action->getMemberList($_GET['queue']);
		$queueList = $action->getQueuesList();
		if (isset($_GET['queue']) && isset($_GET['agent']) && in_array($_GET['queue'], $queueList) && in_array($_GET['agent'], $getMemberList)) {
			//$memberList = $action->getMemberList($_GET['queue']);
			if (!(in_array($_GET['agent'], $getMemberList))){
				$resultMessage = array('result' => array(0 =>'error: This agent is currently not in the list of members'));
			    $glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
	    	    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	    	    print_r($result);
	    	    $result = json_encode($glueMessage);
	    	    $messageLog = ['get=' => $_GET, 'response=' => $result];
   	    	    $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
	    	    unset($getMemberList);
	    	    unset($queueList);
	    	    unset($objLogger);
			    break;
			}
				$deleteMember = $action->deleteMember($_GET['queue'],$_GET['agent']);
				$resultMessage = array('result'=> array(0 => $deleteMember));
			    $glueMessage = $httpMessage200 + $resultMessage + $currentAPIUser;
			    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
			    print_r($result);
			    $result = json_encode($glueMessage);
			    $messageLog = ['get=' => $_GET, 'response=' => $result];
    		    $addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
			    unset($getMemberList);
	    	    unset($queueList);
			    unset($deleteMember);
			    unset($objLogger);
			    break;
			}
			$glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
	    	$result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	    	print_r($result);
	    	$result = json_encode($glueMessage);
	    	$messageLog = ['get=' => $_GET, 'response=' => $result];
   	    	$addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
	    	unset($getMemberList);
	    	unset($queueList);
	    	unset($objLogger);
			break;		

	case 'setMemberPause':
		$resultMessage = array('result' => array(0 =>'error: agent does not exist'));
		$SIPPeers = $action->getSIPPeers();
		if (isset($_GET['agent']) && array_key_exists($_GET['agent'], $SIPPeers)) {
			$setMemberPause = $action->setMemberPause($_GET['agent']);
			$resultMessage = array('result'=> array(0 => $setMemberPause));
		  	$glueMessage = $httpMessage200 + $resultMessage + $currentAPIUser;
		  	$result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		  	print_r($result);
		  	$result = json_encode($glueMessage);
		  	$messageLog = ['get=' => $_GET, 'response=' => $result];
    	  	$addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
	      	unset($SIPPeers);
		  	unset($setMemberPause);
		  	unset($objLogger);
		  	break;
		}
		$glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
	    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	    print_r($result);
	    $result = json_encode($glueMessage);
	    $messageLog = ['get=' => $_GET, 'response=' => $result];
   	    $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
	    unset($SIPPeers);
	    unset($objLogger);
		break;
	case 'unsetMemberPause':
		$resultMessage = array('result' => array(0 =>'error: agent does not exist'));
		$SIPPeers = $action->getSIPPeers();
		if (isset($_GET['agent']) && array_key_exists($_GET['agent'], $SIPPeers)) {
			$unsetMemberPause = $action->unsetMemberPause($_GET['agent']);
			$resultMessage = array('result'=> array(0 => $unsetMemberPause));
		  	$glueMessage = $httpMessage200 + $resultMessage + $currentAPIUser;
		  	$result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		  	print_r($result);
		  	$result = json_encode($glueMessage);
		  	$messageLog = ['get=' => $_GET, 'response=' => $result];
    	  	$addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
	      	unset($SIPPeers);
		  	unset($unsetMemberPause);
		  	unset($objLogger);
		  	break;
		}
		$glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
	    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	    print_r($result);
	    $result = json_encode($glueMessage);
	    $messageLog = ['get=' => $_GET, 'response=' => $result];
   	    $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
	    unset($SIPPeers);
	    unset($objLogger);
		break;
	case 'getPausedMembersList':
$resultMessage = array('result' => array(0 =>'error: The requested queue name does not exist or is invalid.'));
		if (isset($_GET['queue'])) {
			$queueList = $action->getQueuesList();
			if (in_array($_GET['queue'], $queueList)) {
				$getPausedMembersList = $action->getPausedMembersList($_GET['queue']);
		        $resultMessage = array('result'=> $getPausedMembersList);
		        $glueMessage = $httpMessage200 + $resultMessage + $currentAPIUser;
		        $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		        print_r($result);
		        $result = json_encode($glueMessage);
		        $messageLog = ['get=' => $_GET, 'response=' => $result];
    	        $addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
		        unset($getPausedMembersList);
		        unset($queueList);
		        unset($objLogger);
		        break;
			}			
		    $glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
		    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		    print_r($result);
		    $result = json_encode($glueMessage);
		    $messageLog = ['get=' => $_GET, 'response=' => $result];
    	    $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
		    unset($queueList);
		    unset($objLogger);
		    break;
		}
	    $glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
	    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	    print_r($result);
	    $result = json_encode($glueMessage);
	    $messageLog = ['get=' => $_GET, 'response=' => $result];
   	    $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
	    unset($queueList);
	    unset($objLogger);
	    break;
	case 'getStatsQueue':
		$resultMessage = array('result' => array(0 =>'error: The requested queue name does not exist or is invalid.'));
		$getQueuesList = $action->getQueuesList();
		if (isset($_GET['queue']) && isset($_GET['key']) && in_array($_GET['queue'], $getQueuesList)) {
			$getStatsQueue = $action->getStatsQueue($_GET['queue'],$_GET['key']);
			if (in_array(null,$getStatsQueue)) {
				$resultMessage = array('result' => array(0 =>'error: Key ('.$_GET['key'].') does not exist.'));
				$glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
	            $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	            print_r($result);
	            $result = json_encode($glueMessage);
	            $messageLog = ['get=' => $_GET, 'response=' => $result];
   	            $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
	            unset($queueList);
	            unset($objLogger);
	            break;
			}
		    $resultMessage = array('result'=> $getStatsQueue);
		    $glueMessage = $httpMessage200 + $resultMessage + $currentAPIUser;
		    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		    print_r($result);
		    $result = json_encode($glueMessage);
		    $messageLog = ['get=' => $_GET, 'response=' => $result];
    	    $addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
		    unset($getStatsQueue);
		    unset($getQueuesList);
		    unset($objLogger);
		    break;
		}
		$glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
	    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	    print_r($result);
	    $result = json_encode($glueMessage);
	    $messageLog = ['get=' => $_GET, 'response=' => $result];
   	    $addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
	    unset($queueList);
	    unset($objLogger);
	    break;
	case 'createCallTask':

		//$resultMessage = array('result' => array(0 =>'error: Bad request'));
		$callTaskObj = $action->createCallTask($_GET);
		if ($callTaskObj['response'] === 'Success') {
			$resultMessage = array('result'=> $callTaskObj);
		    $glueMessage = $httpMessage200 + $resultMessage + $currentAPIUser;
		    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		    print_r($result);
		    $result = json_encode($glueMessage);
		    $messageLog = ['get=' => $_GET, 'response=' => $result];
    	    $addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
		    unset($callTaskObj);
		    unset($objLogger);
		    break;
		}
		$resultMessage = array('result'=> $callTaskObj);
		$glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
		$result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		print_r($result);
		$result = json_encode($glueMessage);
		$messageLog = ['get=' => $_GET, 'response=' => $result];
    	$addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
		unset($callTaskObj);
		unset($objLogger);
		break;
	case 'resetStatsQueue':
		$queueList = $action->getQueuesList();
		if (in_array($_GET['queue'], $queueList)) {
			$resetStatsQueue = $action->resetStatsQueue($_GET['queue']);
	 		$resultMessage = array('result'=> $resetStatsQueue);
	 		$glueMessage = $httpMessage200 + $resultMessage + $currentAPIUser;
	 		$result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	 		print_r($result);
	 		$result = json_encode($glueMessage);
	 		$messageLog = ['get=' => $_GET, 'response=' => $result];
     		$addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
     		unset($resetStatsQueue);
	 		unset($queueList);
	 		unset($objLogger);
	 		break;
		}
		$resultMessage = array('result' => array(0 =>'error: The requested queue name does not exist or is invalid.'));
		$glueMessage = $httpMessage400 + $resultMessage + $currentAPIUser;
		$result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		print_r($result);
		$result = json_encode($glueMessage);
		$messageLog = ['get=' => $_GET, 'response=' => $result];
    	$addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
		unset($callTaskObj);
		unset($objLogger);
		break;
	case 'CDRsearchFile':
		$CDR = new CDR();
		$linkFile = $CDR->searchFile($_GET['fileName']);
		$resultMessage = array('result' => array(0 =>$linkFile));
		$glueMessage = $httpMessage200 + $resultMessage + $currentAPIUser;
	 	$result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
	 	print_r($result);
	 	$result = json_encode($glueMessage);
	 	$messageLog = ['get=' => $_GET, 'response=' => $result];
     	$addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
     	unset($CDR);
	 	unset($queueList);
	 	unset($objLogger);
		break;

	default:
			$checkUser = print_r($auth->checkToken($_GET['token']),TRUE);
			$resultMessage = array('result' => array('user' => $checkUser));
		    $glueMessage = $httpMessage200 + $resultMessage + $currentAPIUser;
		    $result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		    print_r($result);
		    $result = json_encode($glueMessage);
		    $messageLog = ['get=' => $_GET, 'response=' => $result];
    	    $addLogging = $objLogger->addMessage($typeLogInfo, $systemLog, $messageLog);
		    unset($objLogger);
		    break;
	
   } 
}
	else { 
		$resultMessage = array('result' => array(0 =>'error: Token does not exist'));
		$glueMessage = $httpMessage401 + $resultMessage;
		$result = json_encode($glueMessage, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		print_r($result);
		$result = json_encode($glueMessage);
		$messageLog = ['get=' => $_GET, 'response=' => $result];
    	$addLogging = $objLogger->addMessage($typeLogError, $systemLog, $messageLog);
		unset($objLogger);
		exit;
	}

?>