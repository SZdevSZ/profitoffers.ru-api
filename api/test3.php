<?php
namespace predicted\api;
require_once 'classes/conf.php';

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

use PAMI\Message\Action\PJSIPShowEndpointsAction;
use PAMI\Message\Event\EndpointListCompleteEvent;
use PAMI\Message\Event\EndpointListEvent;
use PAMI\Message\Action\CoreShowChannelsAction;
require $_SERVER['DOCUMENT_ROOT'] .SITEDIR."api/vendor/autoload.php";
require_once 'src/PAMI/Message/Action/PJSIPShowEndpointsAction.php';

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
    * Will return member list endpoints
    * @param 
    * @return array,string $result
    */        
    public function getPJSIPShowEndpoints()
    {
        $result = array();
        $pamiClient = new PamiClient($this->options);
        $pamiClient->open();
        $result = $pamiClient->send(new PJSIPShowEndpointsAction())->getEvents();
        	return $result;
    }

        /**
    * Will return channels list
    * @param 
    * @return array,string $result
    */  
    public function getChannelsList()
    {
    	$result = array();
    	$pamiClient = new PamiClient($this->options);
    	$pamiClient->open();
    	$result = $pamiClient->send(new CoreShowChannelsAction())->getEvents();
    		return $result;

    }
}
////TEST CODE

$action =  new AMIActions();

$result = array();
$arrListEndpoints = $action->getPJSIPShowEndpoints();
echo '<pre>';
$countArrListEndpoints = count($arrListEndpoints);
$i = 0;
while ($i <= $countArrListEndpoints-2) {
	$arrListEndpointsKeys = $arrListEndpoints[$i]->getKeys();
	$currentEndpointKey = $arrListEndpointsKeys['objectname'];
	array_push($result, $currentEndpointKey);
	$i++;
}
print_r($result);
//print_r($arrListEndpointsKeys);
//var_dump($arrListEndpoints); 

echo '</pre>';
echo '______________________'.'<br>';
echo '<pre>';
$result = array();
$arrChannelsList = $action->getChannelsList();
$countArrChannelsList = count($arrChannelsList);
$i = 0;
while ($i <= $countArrChannelsList-1) {
	$arrChannelsListKeys = $arrChannelsList[$i]->getKeys();
	$currentChannelName = $arrChannelsListKeys['channel'];
	$containsChannel = strpos($currentChannelName, 'BeelinePj');
	if ($containsChannel === false) {
	}
		else{
			array_push($result, $currentChannelName);
		}
	$i++;

}
	print_r($result);
	echo'<br>';
	$countChannels = count($result);
	echo "count channels:".$countChannels;
	echo '</pre>';
//echo'<br>';
//var_dump($arrChannelsList);
//
?>
