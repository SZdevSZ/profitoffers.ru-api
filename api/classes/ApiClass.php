<?php
namespace predicted\api;
require_once 'conf.php';
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', 1);
use PAMI\Client\Impl\ClientImpl as PamiClient;
use PAMI\Listener\IEventListener;
use PAMI\Message\Response\ResponseMessage;
use PAMI\Message\Event\EventMessage;
use PAMI\Message\Event\DialEvent;
use PAMI\Message\Event\QueueParamsEvent;
use PAMI\Message\Event\QueueMemberEvent;
use PAMI\Message\Action\QueueStatusAction;
use PAMI\Message\Action\QueueResetAction;
use PAMI\Message\Action\QueueRemoveAction;
use PAMI\Message\Action\QueueSummaryAction;
use PAMI\Message\Action\QueuesAction;
use PAMI\Message\Action\ActionMessage;
use PAMI\Message\Action\QueueLogAction;
use PAMI\Message\Action\QueuePauseAction;
use PAMI\Message\Action\QueueUnpauseAction;
use PAMI\Message\Action\OriginateAction;
use PAMI\Message\Action\SIPPeersAction;
use PAMI\Message\OutgoingMessage;
use PAMI\Exception\PAMIException;
require $_SERVER['DOCUMENT_ROOT'] .SITEDIR."api/vendor/autoload.php";

class A implements IEventListener
{
    public function handle(EventMessage $event)
    {
        var_dump($event);
    }
}
class AMIActions 
{
    public $channelType = 'Local';
    public $contextForChannel = 'from-internal';
	public $options = [
            'host' => 'localhost',
            'scheme' => 'tcp://',
            'port' => 5038,
            'username' => 'ams',
            'secret' => 'amsAMS!@#',
            'connect_timeout' => 10000,
            'read_timeout' => 10000
        ];

    /**
    * Will return member list from queue
    * @param string,integer $queue
    * @return array,string $result
    */        
    public function getMemberList($queue)
    {
	    if (isset($queue)) {
	        $result = array();
            $pamiClient = new PamiClient($this->options);
	        $pamiClient->open();
            $arr = $pamiClient->send(new QueueStatusAction($queue))->getEvents();
            $countArr = count($arr);
            $i = 1;
            $countArr = ($countArr-1);
            	while ($i < $countArr) {
            		$keyName = ($arr[$i]->getKey('Name'));
            		array_push($result, $keyName);
            		$i++;
            	}
            $pamiClient->close();
            return $result;
        }
        $result = (string)'ERROR: Queue number not recived';
        $result = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        unset($arr);
        return $result;
}

    /**
    * Will return list all queues
    * @param no params
    * @return array $result
    */
    public function getQueuesList()
    {
    	$result = array();
    	$pamiClient = new PamiClient($this->options);
    	$pamiClient->open();
    	$arr = $pamiClient->send(new QueueStatusAction())->getEvents();
    	$countArr = count($arr);
    	$i = 0;
    	$countArr = ($countArr);
    	while ($i < $countArr) {
            		$keyName = ($arr[$i]->getKey('Queue'));
            		if (empty($keyName)) {
            			$i++;
            		}
            		elseif (in_array($keyName, $result)) {
            			$i++;
            		}
            			else
            			{
            			array_push($result, $keyName);
            			$i++;	
            			}            		
        }
    	$pamiClient->close();
        unset($arr);
    	return $result;
    }

        /**
    * Adding member to queue
    * @param string,integer $queue
    * @param string,integer $agent    
    * @return string $result
    */
    public function addMember($queue,$agent)
    {   
    	$pamiClient = new PamiClient($this->options);
   	    $pamiClient->open();
        $stateInterface = 'Local/'.$agent.'@from-queue/n';
        $penalty = '';
        $memberName = $agent;
        $paused = 'no';
        $action = new \PAMI\Message\Action\QueueAddAction($queue, $stateInterface);
        $action->setPaused($paused);
        $action->setMemberName($memberName);
        $action->setPenalty($penalty);
        $action->setStateInterface($stateInterface);
        $arr = $pamiClient->send($action)->getKey('message');
        $result = ($arr);
        $pamiClient->close();
        unset($arr);
        return $result;
    }

    /**
    * Deleting member from queue
    * @param string,integer $queue
    * @param string,integer $agent
    * @return string $result
    */
    public function deleteMember($queue,$agent)
    {
        $pamiClient = new PamiClient($this->options);
        $pamiClient->open();
        $stateInterface = 'Local/'.$agent.'@from-queue/n';
        $action = new \PAMI\Message\Action\QueueRemoveAction($queue, $stateInterface);
        $arr = $pamiClient->send($action)->getKey('message');
        $result = ($arr);
        $pamiClient->close();
        unset($arr);
        return $result;
    }

    /**
    * Will return a list of members who are free
    * @param string,integer $queue
    * @return array $result
    */    
    public function getMembersListFree($queue)
    {
        $result = array();
        $pamiClient = new PamiClient($this->options);
        $pamiClient->open();
        $arr = $pamiClient->send(new QueueStatusAction($queue))->getEvents();
        $pamiClient->close();
        $countArr = count($arr);
        $i = 1;
        $countArr = ($countArr-1);
                while ($i < $countArr) {
                    $keyName = ($arr[$i]->getKey('Name'));
                    $keyStatus = ($arr[$i]->getKey('status'));
                    if (isset($keyName) && $keyStatus == 1){
                        array_push($result, $keyName);
                        $i++;
                    }
                    else{
                           $i++; 
                        }
                }
        unset($arr);
        return $result; 
    }

    /**
    * Will return the number of participants that are free
    * @param string,integer $queue
    * @return array $result
    */
    public function getMembersCountFree($queue)
    {
        $result = array();
        $pamiClient = new PamiClient($this->options);
        $pamiClient->open();
        $arr = $pamiClient->send(new QueueStatusAction($queue))->getEvents();
        $pamiClient->close();
        $countArr = count($arr);
        $i = 1;
        $countArr = ($countArr-1);
                while ($i < $countArr) {
                    $keyName = ($arr[$i]->getKey('Name'));
                    $keyStatus = ($arr[$i]->getKey('status'));
                    if (isset($keyName) && $keyStatus == 1){
                        array_push($result, $keyName);
                        $i++;
                    }
                    else{
                           $i++; 
                        }
                }
        $result = array (0 => count($result));
        unset($arr);
        return $result; 
    }

    /**
    * Will return a list of members who are busy
    * @param string,integer $queue
    * @return array $result
    */
    public function getMembersListBusy($queue)
    {
        $result = array();
        $pamiClient = new PamiClient($this->options);
        $pamiClient->open();
        $arr = $pamiClient->send(new QueueStatusAction($queue))->getEvents();
        $pamiClient->close();
        $countArr = count($arr);
        $i = 1;
        $countArr = ($countArr-1);
                while ($i < $countArr) {
                    $keyName = ($arr[$i]->getKey('Name'));
                    $keyStatus = ($arr[$i]->getKey('status'));
                    if (isset($keyName) && $keyStatus == 2){
                        array_push($result, $keyName);
                        $i++;
                    }
                    else{
                           $i++; 
                        }
                }
        unset($arr);
        return $result; 
    }

    /**
    * Will return the number of agents that are busy
    * @param string,integer $queue
    * @return string $result
    */    
    public function getMembersCountBusy($queue)
    {
        $result = array();
        $pamiClient = new PamiClient($this->options);
        $pamiClient->open();
        $arr = $pamiClient->send(new QueueStatusAction($queue))->getEvents();
        $pamiClient->close();
        $countArr = count($arr);
        $i = 1;
        $countArr = ($countArr-1);
                while ($i < $countArr) {
                    $keyName = ($arr[$i]->getKey('Name'));
                    $keyStatus = ($arr[$i]->getKey('status'));
                    if (isset($keyName) && $keyStatus == 2){
                        array_push($result, $keyName);
                        $i++;
                    }
                    else{
                           $i++; 
                        }
                }
        $result = array (0 => count($result));
        unset($arr);
        return $result; 
    }

    /**
    * Resetting statistic for queue.
    * @param string,integer $queue
    * @return string $result (Queue stats reset successfully)
    */
    public function resetStatsQueue($queue)
    {
        $pamiClient = new PamiClient($this->options);
        $pamiClient->open();
        $arr = $pamiClient->send(new QueueResetAction($queue))->getKeys();
        $result = $arr;
        return $result;
    }

    /**
    * Will return a statistic for queue.
    * @param string,integer $queue
    * @param string,integer $key    
    * @return array $result
    */    
    public function getStatsQueue($queue,$key)
    {
        $result = array();
        $pamiClient = new PamiClient($this->options);
        $pamiClient->open();
        $arr = $pamiClient->send(new QueueStatusAction($queue))->getEvents();
        if (isset($key)) {
            $result = $arr[0]->getKey($key);
            $result = array($key => $result);
            $pamiClient->close();
            return $result;
        }
        $result = $arr[0]->getKeys();
        $pamiClient->close();
        unset($arr);
        return $result; 
    }

    /**
    * Will return a statistic for members. Calls taken.
    * @param string,integer $queue    
    * @param string,integer $agent
    * @return array $result
    */    
    public function getStatsMembers($queue)
    {
        //array('status'=>array(
        //$result = array('CallsTaken'=>array());
        $result = array();
        $pamiClient = new PamiClient($this->options);
        $pamiClient->open();
        $arr = $pamiClient->send(new QueueStatusAction($queue))->getEvents();
        $countArr = count($arr);
        $countArr = $countArr-2;
        $i = 1;
        while ($i <= $countArr) {
            $agentName = $arr[$i]->getKey('name');
            $callsTaken = $arr[$i]->getKey('CallsTaken');
            $result[$agentName] = $callsTaken;
            $i++;
        }
        $pamiClient->close();
        unset($arr);
        return $result; 
    }

    /**
    * Set pause for agent
    * @param string,integer $agent
    * @return array $result
    */ 
    public function setMemberPause($agent)
    {
        $result = array();
        $pamiClient = new PamiClient($this->options);
        $pamiClient->open();
        $stateInterface = 'Local/'.$agent.'@from-queue/n';
        $arr = $pamiClient->send(new QueuePauseAction($stateInterface))->getEvents();
        $resMessage = 'Agent paused:'.$stateInterface.'';
        array_push($result, $resMessage);
        $pamiClient->close();
        unset($arr);
        return $result;
    }

    /**
    * Unset pause for agent
    * @param string,integer $agent
    * @return array $result
    */ 
    public function unsetMemberPause($agent)
    {
        $result = array();
        $pamiClient = new PamiClient($this->options);
        $pamiClient->open();
        $stateInterface = 'Local/'.$agent.'@from-queue/n';
        $arr = $pamiClient->send(new QueueUnpauseAction($stateInterface))->getEvents();
        $resMessage = 'Agent unpaused:'.$stateInterface.'';
        array_push($result, $resMessage);
        $pamiClient->close();
        unset($arr);
        return $result;
    }

    /**
    * Will return a list of memebers who are paused
    * @param string,integer $queue
    * @return array $result
    */    
    public function getPausedMembersList($queue)
    {
        $result = array();
        $pamiClient = new PamiClient($this->options);
        $pamiClient->open();
        $arr = $pamiClient->send(new QueueStatusAction($queue))->getEvents();
        $countArr = count($arr);
        $i = 1;
        $countArr = ($countArr-1);
                while ($i < $countArr) {
                    $keyName = ($arr[$i]->getKey('Name'));
                    $keyStatus = ($arr[$i]->getKey('Paused'));
                    if (isset($keyName) && $keyStatus == 1){
                        array_push($result, $keyName);
                        $i++;
                    }
                    else{
                           $i++; 
                        }
                }
        $pamiClient->close();
        unset($arr);
        return $result; 
    }

    /**
    *Create call task using Originate
    *@param array $callParams
    *@return array $result
    */
    public function createCallTask($callParams)
    {
        $errorMsg = array(0 =>'error: Enmpty one or more parameters (channel, extension)');
        $callParams['channel'] = isset($callParams['channel'])? $this->channelType.'/'.$callParams['channel'].'@'.$this->contextForChannel : NULL;
        $callParams['extension'] = isset($callParams['extension'])? $callParams['extension'] : NULL;
        $callParams['context'] = isset($callParams['context'])? $callParams['context'] : 'from-internal';
        $callParams['priority'] = isset($callParams['priority'])? $callParams['priority'] : 1;
        $callParams['callerid'] = isset($callParams['callerid'])? $callParams['callerid'] : '';
        $callParams['timeout'] = isset($callParams['timeout'])? $callParams['timeout'] : 30000;
        $callParams['account'] = isset($callParams['account'])? $callParams['account'] : 'AMIActions';
        $callParams['async'] = isset($callParams['async'])? $callParams['async'] : '1';
        if ($callParams['channel'] === NULL || $callParams['extension'] === NULL) {
            return $errorMsg;
        }
        $originateMsg = new OriginateAction($callParams['channel']);
        $originateMsg->setExtension($callParams['extension']);
        $originateMsg->setPriority($callParams['priority']);
        $originateMsg->setContext($callParams['context']);
        $originateMsg->setCallerId($callParams['callerid']);
        $originateMsg->setAccount($callParams['account']);
        $originateMsg->setTimeout($callParams['timeout']);
        if (isset($callParams['variables'])) {
            $variables = $callParams['variables'];
            foreach ($variables as $k => $v) {
                $key = print_r($k, TRUE);
                $value = print_r($v,TRUE);
            $originateMsg->setVariable($key, $value);
            }
            unset($v);
        }
        if (isset($callParams['application'])) {
            $originateMsg->setApplication($callParams['application']);
        }
        if (isset($callParams['data'])) {
            $originateMsg->setApplication($callParams['data']);
        }
        if ($callParams['async'] === '0') {
            $callParams['async'] = FALSE;
            $originateMsg->setAsync($callParams['async']);
        }
            else {
                $callParams['async'] = TRUE;
                $originateMsg->setAsync($callParams['async']);    
            }
        if (isset($callParams['codecs'])) {
            $originateMsg->setCodecs($callParams['codecs']);
        }
        $pamiClient = new PamiClient($this->options);
        $pamiClient->open();
        $result = $pamiClient->send($originateMsg)->getKeys();
        unset($originateMsg);
        return $result;
    }

    /**
    * Will return list all SIP peers
    * @param no params
    * @return array $result
    */
    public function getSIPPeers()
    {
        $result = array();
        $pamiClient = new PamiClient($this->options);
        $pamiClient->open();
        //var_dump($a->send(new SIPPeersAction()));
        $arr = $pamiClient->send(new SIPPeersAction())->getEvents();
        $pamiClient->close();
        $countArr = count($arr);
        $i = 1;
        $countArr = ($countArr-1);
                while ($i < $countArr) {
                    $sipName = ($arr[$i]->getKey('objectname'));
                    $sipStatus = ($arr[$i]->getKey('status'));
                    if (isset($sipName)){
                        $result[$sipName] = $sipStatus;
                        $i++;
                    }
                    else{
                           $i++; 
                        }
                }
        unset($arr);
        return $result; 
    }


    public function all($queue)
    {
        $result = array();
        $pamiClient = new PamiClient($this->options);
        $pamiClient->open();
        $arr = $pamiClient->send(new QueueStatusAction($queue))->getEvents();
        //$arr = $arr[]->getKeys();
        //array_push($result, $resMessage);
        $pamiClient->close();
        unset($arr);
        return $arr;
    }
}
?>
