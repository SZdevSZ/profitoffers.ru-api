<?php
$secret_key="efi29Qs1Wo2";

// Сохраняем в файл все поступающие переменные
$file = fopen('log_api_test.txt', 'a');
foreach ($_REQUEST as $key => $val)	{fwrite($file, $key . ' => ' . $val . "\n");}
fclose($file);

if (
	(isset($_GET['key']) && $_GET['key']==$secret_key) && 
	(isset($_GET['order']) && $_GET['order']!='') && 
	(isset($_GET['status']) && $_GET['status']!='')
	)
	{
		
	include '../includes/config.php';		

	$zakaz_id = (int)$_GET['order'];
	$status = (int)$_GET['status'];
	
	// Проверяем, есть ли в базе указанный заказ
	$sql_check_zakaz = "SELECT * FROM `zakaz` WHERE `id` = '$zakaz_id' AND `status` = '2'";
	$result_check_zakaz=$mysqli->query($sql_check_zakaz);
	if (mysqli_num_rows($result_check_zakaz) > 0) 
		{
        $res_check_zakaz=mysqli_fetch_array($result_check_zakaz);
        // Получаем старый комментарий
		$old_comments=htmlspecialchars($res_check_zakaz['comments']);
			
		$cur_status='2';	
		$comment='Дата прозвона: '.date('d.m.Y');	
			
		// status 1 - клиент нажал "1" и решил пообщаться с оператором
		// status 2 - клиент нажал "2" и отменил заказ
		// status 3 - клиент нажал "3" чтобы ему перезвонили позже
		// status 4 - клиент ничего не нажал и завершил вызов
		
		if ($status=='1')
			{	
			$cur_status='2';
			$cur_addstatus='0';
			$comment='Дата прозвона: '.date('d.m.Y').'. Прозвон клиента.';		
			}
		elseif ($status=='2')
			{	
			$cur_status='0';
			$cur_addstatus='0';
			$comment='Дата прозвона: '.date('d.m.Y').'. Клиент передумал и отказался от заказа';			
			}			
		elseif ($status=='3')
			{
			$cur_status='2';
			$cur_addstatus='36';
			$comment='Дата прозвона: '.date('d.m.Y').'. Клиент попросил перезвонить позже';				
			}						
		elseif ($status=='4')
			{	
			$cur_status='2';
			$cur_addstatus='36';
			$comment='Дата прозвона: '.date('d.m.Y').'. Клиент не стал общаться, нажал на сброс.';			
			}									
		else
			{
			$cur_status='2';
			$cur_addstatus='36';
			$comment='Дата прозвона: '.date('d.m.Y').'. Прозвон клиента.';					
			}
		
		// $sql_update_zakaz = "UPDATE `zakaz` SET `status`='$cur_status', `addstatus`='$cur_addstatus', `comments`='".$old_comments." // ".$comment."' WHERE `id`='$zakaz_id' AND `status`='2'";
		$sql_update_zakaz = "UPDATE `zakaz` SET `status`='$cur_status', `addstatus`='$cur_addstatus', `comments`='".$comment."' WHERE `id`='$zakaz_id' AND `status`='2'";
		$result_update_zakaz=$mysqli->query($sql_update_zakaz);		

		// Добавляем информацию в таблицу истории заказов
		$sql_save_history= "INSERT INTO `zakaz_history` 
			(
			`zakaz_id`,
			`action`,
			`comments`
			) 
			VALUES 
			(
			'$zakaz_id',
			'18',
			'".$old_comments." // ".$comment."'
			)";
		$result_save_history=$mysqli->query($sql_save_history);		
		//			
		}	
	}
exit;
?>