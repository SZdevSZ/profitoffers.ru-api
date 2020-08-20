<?php
namespace predicted\api;
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
ini_set('display_errors', 1);
require_once 'classes/ApiClass.php';
require_once 'classes/LoggerClass.php';
require_once 'classes/AuthAPIClass.php';
require_once 'classes/CDRClass.php';
use PDO;
// require(implode(DIRECTORY_SEPARATOR, array(
//     __DIR__,
//     '..',
//     '..',
//     '..',
//     'vendor',
//     'autoload.php'
// )));
use PAMI\Client\Impl\ClientImpl;
use PAMI\Listener\IEventListener;
use PAMI\Message\Event\EventMessage;
use PAMI\Message\Action\ListCommandsAction;
use PAMI\Message\Action\ListCategoriesAction;
use PAMI\Message\Action\CoreShowChannelsAction;
use PAMI\Message\Action\CoreSettingsAction;
use PAMI\Message\Action\CoreStatusAction;
use PAMI\Message\Action\StatusAction;
use PAMI\Message\Action\ReloadAction;
use PAMI\Message\Action\CommandAction;
use PAMI\Message\Action\HangupAction;
use PAMI\Message\Action\LogoffAction;
use PAMI\Message\Action\AbsoluteTimeoutAction;
use PAMI\Message\Action\OriginateAction;
use PAMI\Message\Action\BridgeAction;
use PAMI\Message\Action\CreateConfigAction;
use PAMI\Message\Action\GetConfigAction;
use PAMI\Message\Action\GetConfigJSONAction;
use PAMI\Message\Action\AttendedTransferAction;
use PAMI\Message\Action\RedirectAction;
use PAMI\Message\Action\DAHDIShowChannelsAction;
use PAMI\Message\Action\DAHDIHangupAction;
use PAMI\Message\Action\DAHDIRestartAction;
use PAMI\Message\Action\DAHDIDialOffHookAction;
use PAMI\Message\Action\DAHDIDNDOnAction;
use PAMI\Message\Action\DAHDIDNDOffAction;
use PAMI\Message\Action\AgentsAction;
use PAMI\Message\Action\AgentLogoffAction;
use PAMI\Message\Action\MailboxStatusAction;
use PAMI\Message\Action\MailboxCountAction;
use PAMI\Message\Action\VoicemailUsersListAction;
use PAMI\Message\Action\PlayDTMFAction;
use PAMI\Message\Action\DBGetAction;
use PAMI\Message\Action\DBPutAction;
use PAMI\Message\Action\DBDelAction;
use PAMI\Message\Action\DBDelTreeAction;
use PAMI\Message\Action\GetVarAction;
use PAMI\Message\Action\SetVarAction;
use PAMI\Message\Action\PingAction;
use PAMI\Message\Action\ParkedCallsAction;
use PAMI\Message\Action\SIPQualifyPeerAction;
use PAMI\Message\Action\SIPShowPeerAction;
use PAMI\Message\Action\SIPPeersAction;
use PAMI\Message\Action\SIPShowRegistryAction;
use PAMI\Message\Action\SIPNotifyAction;
use PAMI\Message\Action\QueuesAction;
use PAMI\Message\Action\QueueStatusAction;
use PAMI\Message\Action\QueueSummaryAction;
use PAMI\Message\Action\QueuePauseAction;
use PAMI\Message\Action\QueueRemoveAction;
use PAMI\Message\Action\QueueUnpauseAction;
use PAMI\Message\Action\QueueLogAction;
use PAMI\Message\Action\QueuePenaltyAction;
use PAMI\Message\Action\QueueReloadAction;
use PAMI\Message\Action\QueueResetAction;
use PAMI\Message\Action\QueueRuleAction;
use PAMI\Message\Action\MonitorAction;
use PAMI\Message\Action\PauseMonitorAction;
use PAMI\Message\Action\UnpauseMonitorAction;
use PAMI\Message\Action\StopMonitorAction;
use PAMI\Message\Action\ExtensionStateAction;
use PAMI\Message\Action\JabberSendAction;
use PAMI\Message\Action\LocalOptimizeAwayAction;
use PAMI\Message\Action\ModuleCheckAction;
use PAMI\Message\Action\ModuleLoadAction;
use PAMI\Message\Action\ModuleUnloadAction;
use PAMI\Message\Action\ModuleReloadAction;
use PAMI\Message\Action\ShowDialPlanAction;
use PAMI\Message\Action\ParkAction;
use PAMI\Message\Action\MeetmeListAction;
use PAMI\Message\Action\MeetmeMuteAction;
use PAMI\Message\Action\MeetmeUnmuteAction;
use PAMI\Message\Action\EventsAction;
use PAMI\Message\Action\VGMSMSTxAction;
use PAMI\Message\Action\DongleSendSMSAction;
use PAMI\Message\Action\DongleShowDevicesAction;
use PAMI\Message\Action\DongleReloadAction;
use PAMI\Message\Action\DongleStartAction;
use PAMI\Message\Action\DongleRestartAction;
use PAMI\Message\Action\DongleStopAction;
use PAMI\Message\Action\DongleResetAction;
use PAMI\Message\Action\DongleSendUSSDAction;
use PAMI\Message\Action\DongleSendPDUAction;

////header('Content-Type: application/json; charset=utf-8');
//
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
$objLogger = new Logger();
$auth = new AuthAPI();
$action =  new AMIActions();
//////CODE START/////
try
{
    $options = array(
 // 'host' => $argv[1],
 // 'port' => $argv[2],
 // 'username' => $argv[3],
 // 'secret' => $argv[4],
 // 'connect_timeout' => $argv[5],
 // 'read_timeout' => $argv[6],
 // 'scheme' => 'tcp://' // try tls://
        'host' => 'localhost',
        'scheme' => 'tcp://',
        'port' => 5038,
        'username' => 'ams',
        'secret' => 'amsAMS!@#',
        'connect_timeout' => 10000,
        'read_timeout' => 10000
    );
	$a = new ClientImpl($options);
    // Registering a closure
    //$client->registerEventListener(function ($event) {
    //});

    // Register a specific method of an object for event listening
    //$client->registerEventListener(array($listener, 'handle'));

    // Register an IEventListener:
	$a->registerEventListener(new A());
	$a->open();


echo"<p>Command action</p>";
echo "<br>";
echo "<pre>";
//$commandAction = $action->CommandAction('pjsip list endpoints');
// var_dump($commandAction);
var_dump($a->send(new CommandAction('sip show peers')));
echo "</pre>";



	// $time = time();
	// while(true)//(time() - $time) < 60) // Wait for events.
	// {
	//     usleep(1000); // 1ms delay
	//     // Since we declare(ticks=1) at the top, the following line is not necessary
	//     $a->process();
	// }
	$a->close(); // send logoff and close the connection.
 } catch (Exception $e) {
 	//echo $e->getMessage() . "\n";
 }
?>