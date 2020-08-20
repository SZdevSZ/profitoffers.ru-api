<?php	
$a = var_dump($_POST);
	$logfile = './log';
	$timestamp = date("dS of F Y h:I:s A");
	$logstr = $timestamp.' '.$a;
	file_put_contents($logfile, $logstr);
?>