<?php
    error_reporting(E_ALL);
    ini_set('error_reporting', E_ALL);
    ini_set('display_errors', 'On');
    ini_set('display_startup_errors', 'On');
    require_once 'dbconf.php';
    require_once "cli-functions.php";
    session_start();
    $login = $_SESSION['login'];
    $password = ($_SESSION['password']);
    if (checkUser($login, $password, $dbhost, $dbname, $dblogin, $dbpassword, $charset) == 'ok'){
        $_SESSION['login'] = $login;
        $_SESSION['password'] = $password;
    
    }
        else $_SESSION['error_auth'] = 1;
    
    if (isset($_SESSION['error_auth']) && !empty($_SESSION['error_auth']) && $_SESSION['error_auth'] == 1){
        header("Location: index.php");
    //header("Location:".$_SERVER['HTTP_REFERER']);


    }
    //echo '<br>';
    //var_dump($_SESSION);
    //echo '/br';
    // $session = (session_id());
    // echo $session;
    
    //connect to db
    $connection = connectDB($dbhost, $dbname, $dblogin, $dbpassword, $charset);
    //result array to limit
    $items = array();
    //html
    $html = NULL;
    $currentNumPage = NULL;
    //count rows on page
    $limit = 8;
    //count page for pagination
    $pageLimit = 5;//будет умножен на 2
    //count page
    $pageCount = 0;
    //first page set POST[start] to 0
    $start = isset($_POST['start'])? intval($_POST['start']) :0;  
    //active page for mysql query
    $activePage = isset($_POST['activePage'])? intval($_POST['activePage']) :0;  
    //current num page set GET[currentNumPage] to 1   
    $currentNumPage = isset($_POST['currentNumPage'])? intval($_POST['currentNumPage']) :1;
    // set value for add string to DB
    $dialerName = isset($_POST['dialerName'])? $_POST['dialerName'] :1;
    $numberPhone = isset($_POST['numberPhone'])? $_POST['numberPhone'] :1;
    $agent = isset($_POST['agent'])? $_POST['agent'] :1;
    $context = isset($_POST['context'])? $_POST['context'] :1;
    $userField = isset($_POST['userField'])? $_POST['userField'] :1;
    $numberPhoneList = isset($_POST['numberPhoneList'])? $_POST['numberPhoneList']: 0;
    // delete row from table by ID
    $idRow = isset($_POST['id'])? $_POST['id'] :0;
    //переменные для поиска searchDialerName
    $searchDialerName = isset($_POST['searchDialerName'])? $_POST['searchDialerName'] :'';
    
    // // if ($searchDialerName == '')
    // // {
    // //      $currentNumPage = 1;
    // //      $activePage = 1;
    // //      $start = 1
    //}


    // if (isset($_SESSION['searchDialerName']) && $_SESSION['searchDialerName'] !== 0 )
    // {
        // $searchDialerName = $_SESSION['searchDialerName'];
    // }
    // else
    // {
    	// $searchDialerName = isset($_GET['searchDialerName'])? $_GET['searchDialerName'] :0;
    	// $_SESSION['searchDialerName'] = $searchDialerName;
    // }

    // if ($_GET['searchDialerName'] == '')
    // {
    // 	unset($_SESSION['searchDialerName']);
    // }

    
    //$_SESSION['searchDialerName'] = $searchDialerName;
    //$searchDialerName = $_SESSION['searchDialerName'];
    // echo '<br>';
    // var_dump($_POST);
    // echo '</br>';
   // echo '<br>';
   // var_dump($_GET);
   // echo '</br>';
    // echo 'IDROW='.$idRow;
    // echo '/pre';

     //echo 'pre';
     //var_dump($_SESSION);
     //echo 'br';
    
    if ($numberPhoneList != 0) {
        $str = $numberPhoneList;
        $dump = var_export ($str,true);
        $dump = str_replace("'", '', $dump);
        $ar = preg_split("/[\s,]+/", $dump);
        $i = 1;
        foreach ($ar as $k => $v) {
            $phone  = $v;
            $connection = connectDB($dbhost, $dbname, $dblogin, $dbpassword, $charset);
            $sql = "INSERT INTO usersCall (login, dialerName, numberPhone, agent, context, userField) 
            VALUES (:login, :dialerName, :phone, :agent, :context, :userField)";
            $query = $connection->prepare($sql);
            $query->execute(array( 
                ':login'=>$login, 
                ':dialerName'=>$dialerName, 
                ':phone'=>$phone,
                ':agent'=>$agent, 
                ':context'=>$context, 
                ':userField'=>$userField));
                // echo "User registered successfully."; 
            $conn = null;
            $phone = null;
        }    
    }
      // if data not empty then add one row to database
      if ($idRow != 0) {
    	    deleteIdRow($idRow, $dbhost, $dbname, $dblogin, $dbpassword, $charset);
    	}
?>
<style>
/* CSS for pagination */
.btn-circle {
    width: 38px;
    height: 38px;
    border-radius: 19px;
    text-align: center;
    padding-left: 0;
    padding-right: 0;
    font-size: 16px;
}
</style>
  <div id="pag" class="row">
  <div class="col text-center">
<?php

// echo $currentNumPage;
// echo '<br>';
// echo $start;
// echo '<br>';
// echo $activePage;
// echo '<br>';
// Form search
    echo '<div class="row my-3 mx-3">
            <form action="tableListCallLite" metod="get" onsubmit="return false"> 
              <div class="form-row">           
                <div class="col">
                  <input type="search" name="findNameList" class="form-control" id="findNameList" placeholder="Name list" value="'.$searchDialerName.'" >
                </div>
                <div class="col">
                  <input type="text" name="findNumberPhone" class="form-control" id="findNumberPhone" placeholder="Number phone">
                </div>
                <div class="col">
                  <input type="text" name="findAgent" class="form-control" id="findAgent" placeholder="Agent">
                </div>
                <div class="col">
                  <input type="text" name="findContext" class="form-control" id="findContext"placeholder="Context">
                </div>
                <div class="col">
                  <input type="text" name="findMark" class="form-control" id="findMark"placeholder="Mark">
                </div>  
                 <div id="col-sm">
                  <button type="submit" name="search" onclick="searchClick('.$currentNumPage.','.$start.','.$activePage.')" class="btn btn-primary form-control" id="search">search</button>
                </div>
              </div>  
            </form> 
          </div>';

    // Form table
    echo '<div id="tableCallList">';
    echo '<table class="table">
              <thead>
                <tr class="d-flex">
                  <th class="col-1">Num</th>
                  <th class="col">Name list</th>
                  <th class="col-2">Number phone</th>
                  <th class="col-1">Agent</th>
                  <th class="col-1">Context</th>
                  <th class="col-1">Mark</th>
                  <th class="col-1">Date</th>
                  <th class="col-1">Delete</th>
                </tr> 
              </thead>
              <tbody class="table-striped">';
    // $sql = 'SELECT * FROM usersCall ORDER BY id DESC LIMIT '.$start.','.$limit.'';
    // echo $searchDialerName;
    if ($searchDialerName !== '') {
    $sql = 'SELECT * FROM usersCall WHERE dialerName LIKE "%'.$searchDialerName.'%" ORDER BY id DESC LIMIT '.$start.','.$limit.'';
    }
        else {
        	$sql = 'SELECT * FROM usersCall ORDER BY id DESC LIMIT '.$start.','.$limit.'';
        }
    echo'<br>';        
    // echo $sql;
    $sth = $connection->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    $i = 1;
    foreach ($sth as $k => $v){
        echo  '<tr class="d-flex" scope="row"> 
                 <td class="col-1">'.$i.'</td>
                 <td class="col">'.$v['dialerName'].'</td>
                 <td class="col">'.$v['numberPhone'].'</td>
                 <td class="col-1">'.$v['agent'].'</td>
                 <td class="col-1">'.$v['context'].'</td>
                 <td class="col-1">'.$v['userField'].'</td>
                 <td>'.$v['datetime'].'</td>
                 <td>
                   <button type="button" class="btn btn-danger"onclick="deleteRowClick('.($currentNumPage).','.$start.','.$activePage.','.$v['id'].')">DELETE</button>
                 </td>
               </tr>';
        $i++;
    }
    if ($searchDialerName !== '') {
    	$allItems = $connection->query('select count(*) from usersCall WHERE dialerName LIKE "%'.$searchDialerName.'%"')->fetchColumn(); 
    }
        else {
            $allItems = $connection->query('select count(*) from usersCall')->fetchColumn(); 
        }    
    // echo "<br>";
    // echo $pageCount;
    // echo "<br>";
    $pageCount = ceil($allItems / $limit);
    // echo "<br>";
    // echo $pageCount;
    // echo "<br>";

    echo '</div>';
?>
<nav aria-label="Table">
  <ul class="pagination justify-content-right">
<?php
    //Pagination
    $count = 1;
    
    //Если общее колличество страниц менее лимита, то диапазон от 1 до последней
    if ($pageCount<$pageLimit*2){
    	//foreach (range(1, $pageLimit*2) as $pagesRange[]) {}
    	foreach (range(1, $pageCount) as $pagesRange[]) {}
    }
    //Если есть отрицательные значения в диапазоне, то диапазон от 1 до лимита*2
    elseif (($currentNumPage-$pageLimit)<=0){
    	//foreach (range(1, $pageLimit*2) as $pagesRange[]) {}
    	foreach (range(1, $pageLimit*2) as $pagesRange[]) {}
    }
    //Если сумма текущей стр + лимит страниц больше или равно общ количеству значит фиксируем диапазон 
    elseif ($currentNumPage+$pageLimit >= $pageCount){
    	foreach (range(($pageCount)-($pageLimit*2), $pageCount) as $pagesRange[]) {}
    }
    //Иначе диапазон в порядке
        else {
        	foreach (range($currentNumPage-($pageLimit-1), $currentNumPage+$pageLimit) as $pagesRange[]) {}
        }
	//Кнопка "назад", если стартовая выборка больше или равна лимиту количества записей на странице, то кнопка активна, иначе не активна 
    if ($start >= $limit) {
    		echo '<li class="page-item active "> <button class="page-link btn-circle" onclick="paginationClick('.($currentNumPage-1).','.($start-$limit).','.$activePage.')"> << </button> </li>';
   	}	
        else {
    	     echo '<li class="page-item "> <button class="page-link btn-circle"> << </button> </li>';
        }
        for( $i = 0; $i < $pageCount; $i++ ) {    
            // Выделить если это текущая станица
            if ($count == $currentNumPage) {
                $html .= '<li class="page-item active"> <button class="page-link btn-circle" onclick="paginationClick('.$count.','.($i*$limit).','.$activePage.')">'.$count.'   </button> </li>';
                $activePage = $i;   
            }
            //если это последняя
            elseif ($count == $pageCount) {
                $html .= '<li class="page-item"> <button class="page-link btn-circle" onclick="paginationClick('.$count.','.($i*$limit).','.$activePage.')">...'.$count.'   </button> </li>';
            }
            //если в диапазоне
            elseif (in_array($count, $pagesRange)) {
                $html .= '<li class="page-item"> <button class="page-link btn-circle" onclick="paginationClick('.$count.','.($i*$limit).','.$activePage.')">'.$count.'   </button> </li>';
            }
                else { 
                    $html .= NULL;
                }
            // Выводим на экран:
            echo $html;
            $html = NULL;
            $count++;
            // После нажатия на кнопку передать скрипту значение value методом POST и выполнить его
            // После выполниения поместить вывод результата в id дива addCallList
        }
    //Кнопка "вперёд", если теущая страница меньше общего колличества станиц, то кнопка активна, иначе не активна
    if ($currentNumPage < $pageCount) {
    	echo '<li class="page-item active "> <button class="page-link btn-circle" onclick="paginationClick('.($currentNumPage+1).','.(($activePage+1)*$limit).','.$activePage.')"> >> </button> </li>';
    }
        else {
            echo '<li class="page-item"> <button class="page-link btn-circle"> >> </button> </li>';	
        }
    $conn = null;
    $searchDialerName = null;
    //$("#addCallList").load('tableListCallLite.php');
    //function searchClick(currentNumPage, start, activePage, id, searchDialerName,){
?>
  </ul>
  </nav>
  </tbody>
  </table>
  <!--pagination-->
  <script type="text/javascript">
    function paginationClick(currentNumPage, start, activePage){
    	searchDialerName = document.getElementById("findNameList").value;
          $.ajax({
            url: 'tableListCallLite.php',
            type: 'post',
            data: {
              start: start,
              searchDialerName: searchDialerName,
              currentNumPage: currentNumPage,
              activePage: activePage},
            success: function(callList){
              $("#addCallList").html(callList);
            }
        });
    }
  </script>
   <!--delete row-->
  <script type="text/javascript">
    function deleteRowClick(currentNumPage, start, activePage,id){
    	searchDialerName = document.getElementById("findNameList").value;
          $.ajax({
            url: 'tableListCallLite.php',
            type: 'post',
            data: {
              start: start,
              searchDialerName: searchDialerName,
              currentNumPage: currentNumPage,
              activePage: activePage,
              id: id},
            success: function(callList){
              $("#addCallList").html(callList);
            }
        });
    }
  </script>
<script type="text/javascript">
  function searchClick(currentNumPage, start, activePage){
          searchDialerName = document.getElementById("findNameList").value;
          currentNumPage = 1;
          start = 0;
        $.ajax({
          url: 'tableListCallLite.php',
          type: 'post',
          data: {
              start: start,
              searchDialerName: searchDialerName,
              currentNumPage: currentNumPage,
              activePage: activePage},
          success: function(callList){
            $("#addCallList").html(callList);

          }
      });
  }
</script>

</div>
</div>