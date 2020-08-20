<?php
// Обработка запроса для осуществления записи в базу номера телефона, ID телефона и ID заказа
if (
	(isset($_POST['phone_number']) && $_POST['phone_number']!='') && 
	(isset($_POST['phone_id']) && $_POST['phone_id']!='') && 
	(isset($_POST['zakaz_id']) && $_POST['zakaz_id']!='')
	)
	{
	include '../includes/config.php';
	$phone_number=htmlspecialchars($_POST['phone_number']);
	$phone_id=(int)$_POST['phone_id'];
	$zakaz_id=(int)$_POST['zakaz_id'];
	
	// Узнаем кто рекл на заказе
	$sql_who_owner = "SELECT `owner_id` FROM `zakaz` WHERE `id`='$zakaz_id'";
	$result_who_owner=$mysqli->query($sql_who_owner);
	if (mysqli_num_rows($result_who_owner)>0) 
		{
		$res_who_owner=mysqli_fetch_array($result_who_owner);
		$owner_id = (int)$res_who_owner['owner_id'];
		}
	else
		{
		$owner_id = '0';
		}	
	
	
	$phone_number = str_replace('+', '', $phone_number);
	$phone_number = str_replace('(', '', $phone_number);
	$phone_number = str_replace(')', '', $phone_number);
	$phone_number = str_replace('-', '', $phone_number);
	$phone_number = str_replace(' ', '', $phone_number);
	$phone_number = trim($phone_number);
	if (strlen($phone_number)<10)
		{
		}
	else
		{
		$phone_number = substr($phone_number, -10);	
		
		// Записываем в свойства заказа информацию о дате нажатия на кнопку звонка
		$sql_check_zakaz = "SELECT * FROM `zakaz` WHERE `id` = '$zakaz_id' AND `phone` LIKE '%$phone_number%'";
		$result_check_zakaz=$mysqli->query($sql_check_zakaz);
		if (mysqli_num_rows($result_check_zakaz) > 0) 
			{
			$sql_update_zakaz = "UPDATE `zakaz` SET `last_ip_phone_call`='".strtotime('now')."' WHERE `id` = '$zakaz_id' AND `phone` LIKE '%$phone_number%'";
			$result_update_zakaz=$mysqli->query($sql_update_zakaz);			
			}		

		// Записываем данные в таблицу temp_call_log
		$sql_check_zakaz = "INSERT INTO `temp_call_log` (`phone_number`,`phone_id`,`zakaz_id`,`owner_id`) VALUES ('$phone_number','$phone_id','$zakaz_id','$owner_id')";
		$result_check_zakaz=$mysqli->query($sql_check_zakaz);
		
		// Определям ID, и тип роли пользователя нажавшего на кнопку звонка с сайта
		$sql_check_phone_id = "SELECT `id` FROM `ip_phone_avalaible` WHERE `number` = '$phone_id'";
		$result_check_phone_id=$mysqli->query($sql_check_phone_id);
		if (mysqli_num_rows($result_check_phone_id) > 0) 
			{
			$res_check_phone_id=mysqli_fetch_array($result_check_phone_id);	
			$ip_phone_id=(int)$res_check_phone_id['id'];		
		
			$sql_check_user = "SELECT `id`,`tip` FROM `users` WHERE `ip_phone_id` = '$ip_phone_id'";
			$result_check_user=$mysqli->query($sql_check_user);
			if (mysqli_num_rows($result_check_user) > 0) 
				{
				$res_check_user=mysqli_fetch_array($result_check_user);	
				$operator_id=(int)$res_check_user['id'];			
				$operator_tip=(int)$res_check_user['tip'];
		
				// Добавляем информацию в историю заказа
				$sql_save_history= "INSERT INTO `zakaz_history` 
					(
					`zakaz_id`,
					`action`,
					`user_id`,
					`tip`
					) 
					VALUES 
					(
					'$zakaz_id',
					'16',
					'$operator_id',
					'$operator_tip'
					)";
				$result_save_history=$mysqli->query($sql_save_history);	
				}
			}
			
		}
	exit;
	}

// Обработка запроса со стороны сервера телефонии
if (
	(isset($_GET['phone_number']) && $_GET['phone_number']!='') && 
	(isset($_GET['phone_id']) && $_GET['phone_id']!='')
	)
	{
	
	include '../includes/config.php';
	
	$phone_number=(int)htmlspecialchars($_GET['phone_number']);
	$phone_id=(int)$_GET['phone_id'];
	
	$phone_number = str_replace('+', '', $phone_number);
	$phone_number = str_replace('(', '', $phone_number);
	$phone_number = str_replace(')', '', $phone_number);
	$phone_number = str_replace('-', '', $phone_number);
	$phone_number = str_replace(' ', '', $phone_number);
	$phone_number = trim($phone_number);
	if (strlen($phone_number)<10)
		{
		}
	else
		{
		$phone_number = substr($phone_number, -10);	
	
		// Ищем ID заказа используя номер клиента и ID телефонного аппарата
		$sql_check_zakaz_id = "SELECT `zakaz_id` FROM `temp_call_log` WHERE `phone_number` LIKE '%$phone_number%' AND `phone_id`='$phone_id' ORDER BY `id` DESC";
		$result_check_zakaz_id=$mysqli->query($sql_check_zakaz_id);
		if (mysqli_num_rows($result_check_zakaz_id) > 0) 
			{
			$res_check_zakaz_id=mysqli_fetch_array($result_check_zakaz_id);	
			echo $zakaz_id=(int)$res_check_zakaz_id['zakaz_id'];
			}
		}
	if (mysqli_ping($mysqli)) 
		{
		mysqli_close($mysqli);
		}		
	exit;
	}
	
	
// Обработка запроса со стороны сервера телефонии передающего количество секунд робопрозвона
if (
	(isset($_GET['id']) && $_GET['id']!='') && 
	(isset($_GET['seconds']) && $_GET['seconds']!='') && 
	(isset($_GET['account']) && $_GET['account']!='')
	)
	{
	include '../includes/config.php';		

	$cur_id = (int)$_GET['id'];
	$cur_account = (int)$_GET['account'];
	$cur_seconds = (int)$_GET['seconds'];


	// Проверяем, есть ли соответствующая запись в логах робопрозвона
	$sql_save = "SELECT `id` FROM `temp_roboprozvon_log` WHERE `id`='$cur_id' AND `zakaz_id`='$cur_account'";
	$result_save=$mysqli->query($sql_save);
	if (mysqli_num_rows($result_save) > 0) 
		{
		$sql_update = "UPDATE `temp_roboprozvon_log` SET `seconds`='$cur_seconds' WHERE `id`='$cur_id' AND `zakaz_id`='$cur_account'";
		$result_update=$mysqli->query($sql_update);
		}
	exit;
	}
	
exit;
?>
