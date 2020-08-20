<?php
    namespace predicted\api;
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', 1);
    session_start();
    require_once 'dbconf.php';
    require_once 'cli-functions.php';
	// define(SITEDIR, '/phoneserver/');
 //    require_once $_SERVER['DOCUMENT_ROOT'].SITEDIR."/phoneserver/api/classes/ApiClass.php";
 //    require_once $_SERVER['DOCUMENT_ROOT'].SITEDIR."/phoneserver/api/classes/LoggerClass.php";
 //    require_once $_SERVER['DOCUMENT_ROOT'].SITEDIR."/phoneserver/backend/classes/notification.php";

    require_once $_SERVER['DOCUMENT_ROOT']."/phoneserver/api/classes/ApiClass.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/phoneserver/api/classes/LoggerClass.php";
    require_once $_SERVER['DOCUMENT_ROOT']."/phoneserver/backend/classes/notification.php";
    //require_once 'classes/notification.php';
    if (isset($_SESSION['login']) && isset($_SESSION['password'])){
        $login = $_SESSION['login'];
        $password = ($_SESSION['password']);
    }
        else{
          $login = $_POST['login'];
          $password = sha1($_POST['password']);
          $_SESSION['origPassword'] = ($_POST['password']);
        }
    unset($_SESSION['error_auth']);
    if (checkUser($login, $password, $dbhost, $dbname, $dblogin, $dbpassword, $charset) == 'ok')
    {
        $_SESSION['login'] = $login;
        $_SESSION['password'] = $password;
    }
        else $_SESSION['error_auth'] = 1;
    if (isset($_SESSION['error_auth']) && !empty($_SESSION['error_auth']) && $_SESSION['error_auth'] == 1)
    {
        header("Location: index.php");
    //header("Location:".$_SERVER['HTTP_REFERER']);
    }
    // print_r($_POST);
    $addAgent = isset($_POST['addAgent'])? $_POST['addAgent'] : 0;
    $deleteAgent = isset($_POST['deleteAgent'])? $_POST['deleteAgent'] : 0;
    $setPauseAgent = isset($_POST['setPauseAgent'])? $_POST['setPauseAgent'] : 0;
    $unsetPauseAgent = isset($_POST['unsetPauseAgent'])? $_POST['unsetPauseAgent'] : 0;
    $queueInputState = isset($_POST['queueInputState'])? $_POST['queueInputState'] : 'default';
    $clearStatistic = isset($_POST['clearStatistic'])? $_POST['clearStatistic'] : 'FALSE';
    //echo $unsetPauseAgent;
    $queuesMGMT = new AMIActions();
    //echo $addAgent;
    if ($addAgent != 0 ) {
    	$addMember = $queuesMGMT->addMember($queueInputState,$addAgent);
    	$alert = new Notification();
    	$result = $alert->success('SUCCESS ','Agent '.$addAgent.' added to queue '.$queueInputState.'','','');
    	print_r($result);
    	unset($alert);
    }
    if ($deleteAgent != 0 ) {
    	$deleteMember = $queuesMGMT->deleteMember($queueInputState,$deleteAgent);
    	$alert = new Notification();
    	$result = $alert->success('SUCCESS ','Agent '.$deleteAgent.' deleted from queue '.$queueInputState.'','','');
    	print_r($result);
    	unset($alert);
    }
    if ($setPauseAgent != 0 ) {
    	$setMemberPause = $queuesMGMT->setMemberPause($setPauseAgent);
    	$alert = new Notification();
    	$result = $alert->success('SUCCESS ','Agent '.$setPauseAgent.' paused','','');
    	print_r($result);
    	unset($alert);
    }
    
    if ($unsetPauseAgent != 0 ) {
    	$unsetMemberPause = $queuesMGMT->unsetMemberPause($unsetPauseAgent);
    	$alert = new Notification();
    	$result = $alert->success('SUCCESS ','Agent '.$unsetPauseAgent.' unpaused','','');
    	print_r($result);
    	unset($alert);
    }
    if ($clearStatistic === 'TRUE') {
      $clearStatistic = $queuesMGMT->resetStatsQueue($queueInputState);
      $alert = new Notification();
      $result = $alert->success('SUCCESS ',$queueInputState.' queue statistics cleared','','');
      print_r($result);
      unset($alert);
    }
?>
<div id="queueManagement" class="container-fluid mx-auto mt-3" >
  <div class="container-fluid card bg-white shadow-lg rounded">
      <div class="form-group row-md-5">
        <label for="inputState">Choose a queue</label>
        <select onchange="queueInputState()" id="queueInputState" class="form-control">
<?php
$i = 0;
$queueList = $queuesMGMT->getQueuesList();
$countQueueList = count($queueList)-1;
echo'<option selected value="'.$queueInputState.'">'.$queueInputState.'</option>';
while ($i <= $countQueueList) {
	echo'<option value="'.$queueList[$i].'">'.$queueList[$i].'</option>';
	$i++;
}
?>
        </select>
      </div>
  </div>
<div class="container-fluid card bg-white shadow-lg rounded">
  <div class="form-group row-md-5">
    <p>Actions</p>
    <div class="container">
      <div class="row">
         <div class="col">
		   <?php echo'Add agent to queue: '.$queueInputState.'<br>';?> 
		   <div class="input-group mb-3">
             <div class="input-group-prepend">
               <button onclick="buttonAction()" class="btn btn-outline-primary" type="button" id="button-addon1">+</button>
               <input id="addAgent" type="text" class="form-control w-50 p-3" placeholder="Agent Number" aria-label="Agent Number" aria-describedby="basic-addon1">
             </div>
           </div>
		   <?php echo'Delete agent from queue: '.$queueInputState.'<br>';?>
		   <div class="input-group mb-3">
             <div class="input-group-prepend">
               <button onclick="buttonAction()" class="btn btn-outline-primary" type="button" id="button-addon1"> -</button>
               <input id="deleteAgent" type="text" class="form-control w-50 p-3" placeholder="Agent Number" aria-label="Agent Number" aria-describedby="basic-addon1">
             </div>
           </div>
         </div>
         <div class="col">
           <?php echo'Set pause for agent'.'<br>';?> 
           <div class="input-group mb-3">
             <div class="input-group-prepend">
               <button onclick="buttonAction()" class="btn btn-outline-primary" type="button" id="button-addon1">+</button>
               <input id="setPauseAgent" type="text" class="form-control w-50 p-3" placeholder="Agent Number" aria-label="Agent Number" aria-describedby="basic-addon1">
             </div>
           </div>
           <?php echo'Unset pause for agent'.'<br>';?> 
           <div class="input-group mb-3">
             <div class="input-group-prepend">
               <button onclick="buttonAction()" class="btn btn-outline-primary" type="button" id="button-addon1"> -</button>
               <input id="unsetPauseAgent" type="text" class="form-control w-50 p-3" placeholder="Agent Number" aria-label="Agent Number" aria-describedby="basic-addon1">
             </div>
         </div>
      </div>
    </div>
  </div>
</div>
</div>
<div class="container-fluid card bg-white shadow-lg rounded">
  <div class="form-group row-md-5">
    <?php echo "Queue Statistic:".' '; print_r($queueInputState);?>
    <div class="container">
      <div class="row">
         <div class="col">
           <div>
		     <?php $queueCountMemberOnline = $queuesMGMT->getMembersCountFree($queueInputState);
		     echo'Amount of free agents:'.' '; print_r($queueCountMemberOnline[0]);
		     ?>
		   </div>
		    <?php 
		    $queueStatsStrategy = $queuesMGMT->getStatsQueue($queueInputState,'strategy');
		    $queueStatsActiveCalls = $queuesMGMT->getStatsQueue($queueInputState,'calls');
		    $queueStatsHoldTime = $queuesMGMT->getStatsQueue($queueInputState,'holdtime');
		    $queueStatsTalkTime = $queuesMGMT->getStatsQueue($queueInputState,'talktime');
		    $queueStatsCompleted = $queuesMGMT->getStatsQueue($queueInputState,'completed');
		    $queueStatsAbandoned = $queuesMGMT->getStatsQueue($queueInputState,'abandoned');
		    ?>
		   <div>
		   	<?php echo'Call strategy:'.' '; print_r($queueStatsStrategy['strategy']);?>
		   </div>
		   <div>
		   	<?php echo'Amount active calls:'.' '; print_r($queueStatsActiveCalls['calls']);?>
		   </div>
		   <div>
		   	<?php echo'Average hold time:'.' '; print_r($queueStatsHoldTime['holdtime']);?>
		   </div>
		   <div>
		   	<?php echo'Average talk time:'.' '; print_r($queueStatsTalkTime['talktime']);?>
		   </div>
		   <div>
		   	<?php echo'Calls completed:'.' '; print_r($queueStatsCompleted['completed']);?>
		   </div>
		   <div>
		   	<?php echo'Calls abandoned:'.' '; print_r($queueStatsAbandoned['abandoned']);?>
		   </div>

         </div>
         <div class="col">
           <div>
         	<?php 
         	$queueMemberOnline = $queuesMGMT->getMembersListFree($queueInputState);
         	echo'Agents list (Who is added and free):'.' '.'<pre>'; print_r($queueMemberOnline);echo '</pre>';?>
           </div>
           <div>
         	<?php 
         	$getPausedMembersList = $queuesMGMT->getPausedMembersList($queueInputState);
         	echo'Agents list (Who is on paused):'.' '.'<pre>'; print_r($getPausedMembersList);echo '</pre>';?>
           </div>
           <div>
           	<?php $getStatsMembers = $queuesMGMT->getStatsMembers($queueInputState);
           	echo'Stats agents:'.' '.'<pre>'; print_r($getStatsMembers); echo '</pre>';?>
           </div>
         </div>
      </div>
      <div class="row">
        <button type="button" id="clearStatistic" onclick="buttonActionClearStats()" value="TRUE" class="btn btn-outline-primary">Clear statistic</button>
        <!-- <button onclick="buttonAction()" class="btn btn-outline-primary" type="button" id="button-addon1"> -</button>       -->
      </div>
    </div>
  </div>
</div>
</div>
<script type="text/javascript">
  function buttonAction()
  {
      var login = document.getElementById("login").value,
      	password = document.getElementById("password").value, 
      	queueInputState = document.getElementById("queueInputState").value,
      	addAgent = document.getElementById("addAgent").value,
      	deleteAgent = document.getElementById("deleteAgent").value,
      	setPauseAgent = document.getElementById("setPauseAgent").value,
      	unsetPauseAgent = document.getElementById("unsetPauseAgent").value;
      if (queueInputState != 0) { 
       $.ajax({
          url: 'queueManagement.php',
          type: 'post',
          data: {
            login: login, 
            password: password, 
            queueInputState: queueInputState,
            addAgent: addAgent,
            deleteAgent: deleteAgent,
            setPauseAgent: setPauseAgent,
            unsetPauseAgent: unsetPauseAgent,
				 },
          success: function(callList){
            $("#queueManagement").html(callList);
          }
        });
       };
  };
</script>
<script type="text/javascript">  
function buttonActionClearStats()
  {
      var login = document.getElementById("login").value,
        password = document.getElementById("password").value, 
        queueInputState = document.getElementById("queueInputState").value,
        clearStatistic = document.getElementById("clearStatistic").value;
      if (confirm("Do you really want to clear statistic?"))
        {   
        if (queueInputState != 0) { 
         $.ajax({
            url: 'queueManagement.php',
            type: 'post',
            data: {
              login: login, 
              password: password, 
              queueInputState: queueInputState,
              clearStatistic: clearStatistic,
           },
            success: function(callList){
              $("#queueManagement").html(callList);
            }
          });
        };
      }
  };
</script>
<script type="text/javascript">
  function queueInputState()
  {
      var login = document.getElementById("login").value;
      var password = document.getElementById("password").value; 
      var queueInputState = document.getElementById("queueInputState").value;
      console.log(queueInputState);
      if (queueInputState != 0) { 
       $.ajax({
          url: 'queueManagement.php',
          type: 'post',
          data: {
            login: login, 
            password: password, 
            queueInputState: queueInputState,
				 },
          success: function(callList){
            $("#queueManagement").html(callList);
          }
        });
       };
    
  };
</script>