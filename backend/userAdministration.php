<?php 
namespace predicted\api;
use PDO;
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
<?php
    ini_set('display_errors','On'); 
    error_reporting(E_ALL);
    session_start();
    require_once 'dbconf.php';
    require_once 'cli-functions.php';
    require_once 'classes/notification.php';
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
//var_export($_POST);
    $firstName = (isset($_POST['firstName']))? $_POST['firstName'] : '';
    $lastName = (isset($_POST['lastName']))? $_POST['lastName'] : ''; 
    $newUserLogin = (isset($_POST['newUserLogin']))? $_POST['newUserLogin'] : ''; 
    $email = (isset($_POST['email']))? $_POST['email'] : '';
    $newUserPassword = (isset($_POST['newUserPassword']))? $_POST['newUserPassword'] : '';
    $confirmNewUserPassword = (isset($_POST['confirmNewUserPassword']))? $_POST['confirmNewUserPassword'] : '';
    $accessMode = (isset($_POST['accessMode']))? $_POST['accessMode'] : '';
    $token = (isset($_POST['token']))? $_POST['token'] : NULL;
// edit var 
    $editFirstName = (isset($_POST['editFirstName']))? $_POST['editFirstName'] : '';
    $editLastName = (isset($_POST['editLastName']))? $_POST['editLastName'] : '';
    $editNewUserPassword = (isset($_POST['editNewUserPassword']))? $_POST['editNewUserPassword'] : '';
    $editConfirmNewUserPassword = (isset($_POST['editConfirmNewUserPassword']))? $_POST['editConfirmNewUserPassword'] : '';
    $editAccessMode = (isset($_POST['editAccessMode']))? $_POST['editAccessMode'] : '';
    $editToken = (isset($_POST['editToken']))? $_POST['editToken'] : NULL;
//managment user
    $deleteUser = isset($_POST['idDelete'])? $_POST['idDelete'] :0;
    $editUser = isset($_POST['idEdit'])? $_POST['idEdit'] :0;
    $idEditSave = isset($_POST['idEditSave'])? $_POST['idEditSave'] :0;
//table+pagination
    //active page for mysql query
    $activePage = isset($_POST['activePage'])? intval($_POST['activePage']) :0;  
    $html = NULL;
    $currentNumPage = NULL;
    $pageCount = 0;
    $pageLimit = 5;//будет умножен на 2
    $items = array();
    //count rows on page
    $limit = 5;
//connect to db
    $connection = connectDB($dbhost, $dbname, $dblogin, $dbpassword, $charset);
    $allItems = $connection->query('select count(*) from users')->fetchColumn();
    $pageCount = ceil($allItems / $limit);
    $count = 1;
    $currentNumPage = isset($_POST['currentNumPage'])? intval($_POST['currentNumPage']) :1;
//first page set POST[start] to 0
    $start = isset($_POST['start'])? intval($_POST['start']) :0;
//current num page set GET[currentNumPage] to 1   
    $currentNumPage = isset($_POST['currentNumPage'])? intval($_POST['currentNumPage']) :1;
//get access mode
    $currentUserAccessMode = getAccessMode ($login, $dbhost, $dbname, $dblogin, $dbpassword, $charset);
   if ($currentUserAccessMode !== 'Administrator')
   {
       echo '<label class="text-danger text-center font-weight-bold" for="addUser">Im sorry, but you do not have sufficient access rights to manage users</label>';
       exit;
   }

   // echo '<div class="container-fluid">';
    echo '<div class="container-fluid mx-auto mt-3" id="usersPanel">
    <div>';
    //save edit user
    if ($idEditSave != 0)
    {
      $resultEditUser = editUser($idEditSave,$editFirstName,$editLastName,$editNewUserPassword,$editConfirmNewUserPassword,$editAccessMode,$editToken,$dbhost, $dbname, $dblogin, $dbpassword, $charset);
      echo $resultEditUser;
    }
    //add news user 
    if($email !== '' && $newUserLogin !== '')
      {
      	echo addNewUser($firstName, $lastName, $newUserLogin, $email, $newUserPassword, $confirmNewUserPassword, $accessMode, $token, $dbhost, $dbname, $dblogin, $dbpassword, $charset );
      }
    //delete user 
    if ($deleteUser != 0) 
    {
          deleteUser($deleteUser, $dbhost, $dbname, $dblogin, $dbpassword, $charset);
    }
    echo '</div>';
    echo '<div class="container-fluid card bg-white shadow-lg rounded">';
    echo '<form action="newUser" metod="post" onsubmit="return false">
            <div class="row  ">
              <div class="col">
                <label for="firstName" class="row-sm-2 row-form-label" >First Name</label>
                <input type="text" name="firstName" class="form-control" id="firstName" placeholder="First Name" required>
              </div>
              <div class="col">
                <label for="newUserLogin">Login</label>
                <input type="text" name="newUserLogin" class="form-control" id="newUserLogin" placeholder="Login" required>
              </div>
              <div class="col">
                <label for="newUserPassword">Password</label>
                <input type="password" name="newUserPassword" class="form-control" id="newUserPassword" placeholder="Password" required>
              </div>
              <div class="col">
                <label for="accessMode" >Access mode</label>
                <select class="form-control" id="accessMode">
                <option value="User">User</option>
                <option value="Administrator">Administrator</option>
                </select>
               <!-- <input type="text" name="accessMode" class="form-control" id="accessMode" placeholder="Access mode" required> -->
              </div>
              <div class="w-100"></div>
              <div class="col">
                <label for="lastName">Last Name</label>
                <input type="text" name="lastName" class="form-control" id="lastName" placeholder="Last Name" required>
              </div>
              <div class="col">
                <label for="email">E-mail</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="e-mail" required>
              </div>
              <div class="col">
                <label for="confirmNewUserPassword">Confirm password</label> 
                <input type="password" name="confirmNewUserPassword" class="form-control" id="confirmNewUserPassword" placeholder="Confirm password" required>
              </div> 
              <div class="col">
                <label for="token">Token</label>
                <input type="text" name="token" class="form-control" id="token" placeholder="Token">
              </div>
                  <div class="w-100"></div>
                  <div id="col">
                    <label>';

                        echo '
                    </label> <button type="submit"  name="addUser" class="btn btn-outline-primary form-control" onclick="addNewUser()" >Add new user</button>
                  </div>
          
          </form>';
    echo '</div>';
    echo '</div>';
    echo '<div>';
    //       <br>
    //      </div>';
    //echo '<div class="container"></div>';
    //container-fluid card bg-white shadow-lg rounded
    //echo '<div class="container-fluid" id="tableUsersList" >';
    echo '<div class="container-fluid card bg-white shadow-lg rounded" id="tableUsersList" >';
    echo '<table class="table container-fluid mt-4 bg-white shadow-lg rounded">
              <thead>
                <tr class="d-flex">
                  <th class="col-1">Num</th>
                  <th class="col-2">Login</th>
                  <th class="col-2">e-mail</th>
                  <th class="col-1">First Name</th>
                  <th class="col-1">Last Name</th>
                  <th class="col-1">Access Mode</th>
                  <th class="col-1">Token</th>
                  <th class="col-1">Reg.Date</th>
                  <th class="col-1">Delete</th>
                  <th class="col">Edit</th>
                </tr> 
              </thead>
              <tbody class="table-striped">';
    $sql = 'SELECT login, e_mail, name, last_name, access_mode, token, reg_date, id_users FROM users ORDER BY id_users DESC LIMIT '.$start.','.$limit.'';
    $sth = $connection->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    $i = 1;
    foreach ($sth as $k => $v){
        echo  '<tr class="d-flex" scope="row"> 
                 <td class="col-1">'.$i.'</td>
                 <td class="col-2">'.$v['login'].'</td>
                 <td class="col-2">'.$v['e_mail'].'</td>
                 <td class="col-1">'.$v['name'].'</td>
                 <td class="col-1">'.$v['last_name'].'</td>
                 <td class="col-1">'.$v['access_mode'].'</td>
                 <td class="col-1" type="password">'.$v['token'].'</td>
                 <td>'.$v['reg_date'].'</td>
                 <td class="col-1">
                   <button type="button" class="btn btn-outline-danger" onclick="deleteUserClick('.$currentNumPage.','.$start.','.$activePage.','.$v['id_users'].')">DELETE</button> 
                 </td>
                 <td class="col">
                   <button type="button" class="btn btn-outline-danger open-modal"data-whatever="'.$v['id_users'].'" onclick="editUserClick('.$currentNumPage.','.$start.','.$activePage.','.$v['id_users'].')">EDIT USER</button> 
                 </td>
               </tr>';
        $i++;
    }
    echo '</tbody>
        </table>
        </div>';
       
?>
<div>
<nav aria-label="Table">
  <ul class="pagination justify-content-right">
<?php
    //Pagination
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
        echo '<li class="page-item active "> <button class="page-link btn-circle" onclick="paginationUserClick('.($currentNumPage-1).','.($start-$limit).','.$activePage.')"> << </button> </li>';
    } 
        else {
           echo '<li class="page-item "> <button class="page-link btn-circle"> << </button> </li>';
        }
        for( $i = 0; $i < $pageCount; $i++ ) {    
            // Выделить если это текущая станица
            if ($count == $currentNumPage) {
                $html .= '<li class="page-item active"> <button class="page-link btn-circle" onclick="paginationUserClick('.$count.','.($i*$limit).','.$activePage.')">'.$count.'   </button> </li>';
                $activePage = $i;   
            }
            //если это последняя
            elseif ($count == $pageCount) {
                $html .= '<li class="page-item"> <button class="page-link btn-circle" onclick="paginationUserClick('.$count.','.($i*$limit).','.$activePage.')">...'.$count.'   </button> </li>';
            }
            //если в диапазоне
            elseif (in_array($count, $pagesRange)) {
                $html .= '<li class="page-item"> <button class="page-link btn-circle" onclick="paginationUserClick('.$count.','.($i*$limit).','.$activePage.')">'.$count.'   </button> </li>';
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
      echo '<li class="page-item active "> <button class="page-link btn-circle" onclick="paginationUserClick('.($currentNumPage+1).','.(($activePage+1)*$limit).','.$activePage.')"> >> </button> </li>';
    }
        else {
            echo '<li class="page-item"> <button class="page-link btn-circle"> >> </button> </li>'; 
        }
    $conn = null;
    $searchDialerName = null;
    echo '
      </ul>
      </nav>
      </div>
      </div>';  
    echo '<div id="userEdit" class="modal" >';
    //data-backdrop="false"
    //edit user
    //class="modal fade"
    $editUser = isset($_POST['idEdit'])? $_POST['idEdit'] :0;
    if ($editUser !== 0)
    {
    	$sql = "SELECT name, last_name, token FROM users WHERE id_users = '$editUser'";
        $result_set  = $connection->query($sql);
        $fetch = $result_set->fetch(PDO::FETCH_ASSOC);
        $firstName = print_r($fetch['name'],true);
        $lastName = print_r($fetch['last_name'],true);
        $token = print_r($fetch['token'],true);
    //var_export($_POST); 
    //var_dump($fetch);
    }
    echo'
      <div class="modal-dialog">
        <div class="modal-content" >
          <!-- Заголовок модального окна -->
          <div class="modal-header">
            <h4 class="modal-title">Edit user</h4>
            <button type="button" class="close" data-dismiss="modal" backdrop="false" aria-hidden="true">×</button>
          </div>
          <!-- Основное содержимое модального окна -->
          <div class="modal-body">
    		<form action="userEdit" metod="post" onsubmit="return false">
    	      <div class="col">
                <label for="firstName" class="row-sm-2 row-form-label" >First Name</label>';
                if ($editUser !== 0)
                {
                	echo'<input type="text" name="firstName" class="form-control" id="editFirstName"value="'.$firstName.'" placeholder="First Name">';
                }	
                	else
                	{
                		echo'<input type="text" name="firstName" class="form-control" id="editFirstName"value="'.$editFirstName.'" placeholder="First Name">';
                	}
                echo'
              </div>
              <div class="col">
                <label for="lastName" class="row-sm-2 row-form-label">Last Name</label>';
                if ($editUser !== 0)
                {
                	echo'<input type="text" name="lastName" class="form-control" id="editLastName" value="'.$lastName.'" placeholder="Last Name">';
                }	
                	else
                	{
                		echo'<input type="text" name="lastName" class="form-control" id="editLastName" value="'.$editLastName.'" placeholder="Last Name">';
                	}
              echo'
              </div>
              <div class="col">
                <label for="accessMode" >Access mode</label>
                <select class="form-control" id="editAccessMode">
                  <option value="User">User</option>
                  <option value="Administrator">Administrator</option>
                </select>
              </div>
              <div class="col">
                <label for="newUserPassword">Password</label>
                <input type="password" name="newUserPassword" class="form-control" id="editNewUserPassword" placeholder="Password" >
              </div>
              <div class="col">
                <label for="confirmNewUserPassword">Confirm password</label> 
                <input type="password" name="confirmNewUserPassword" class="form-control" id="editConfirmNewUserPassword" placeholder="Confirm password" >
              </div>
              <div class="col">
                <label for="token">Token</label>';
                if ($editUser !== 0)
                {
                	echo'<input type="text" name="token" class="form-control" id="editToken" value="'.$token.'" placeholder="Token">';
                }	
                	else
                	{
                		echo'<input type="text" name="token" class="form-control" id="editToken" value="'.$editToken.'" placeholder="Token">';
                	}
              echo'
              </div>
    		</form>
          </div>
          <!-- Футер модального окна -->
          <div class="modal-footer">';
      echo'<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" data-dismiss="modal" backdrop="false" aria-hidden="true" onclick="editUserSave('.$editUser.','.$currentNumPage.','.$start.','.$activePage.')" >Save</button>
          </div>
          ';
          //data-dismiss="modal" aria-hidden
          //var_export($_POST);
          //echo $firstName;
          echo '
        </div>
      </div>
    </div>';

?>
<script type="text/javascript">
  function paginationUserClick(currentNumPage, start, activePage){
    searchDialerName = document.getElementById("findNameList").value;
        $.ajax({
          url: 'userAdministration.php',
          type: 'post',
          data: {
                 start: start,
                 searchDialerName: searchDialerName,
                 currentNumPage: currentNumPage,
                 activePage: activePage,
                },
          success: function(callList){
            $("#userAdministration").html(callList);
          }
      });
  }
</script>
<script type="text/javascript">
  function addNewUser(){
          firstName = document.getElementById("firstName").value;
          lastName = document.getElementById("lastName").value;
          newUserLogin = document.getElementById("newUserLogin").value;
          email = document.getElementById("email").value;
          newUserPassword = document.getElementById("newUserPassword").value;
          confirmNewUserPassword = document.getElementById("confirmNewUserPassword").value;
          accessMode = document.getElementById("accessMode").value;
          token = document.getElementById("token").value;
          currentNumPage = 1;
          start = 0;
        $.ajax({
          url: 'userAdministration.php',
          type: 'post',
          data: {
              firstName: firstName,
              lastName: lastName,
              newUserLogin: newUserLogin,
              email: email,
              newUserPassword: newUserPassword ,
              confirmNewUserPassword: confirmNewUserPassword,
              accessMode: accessMode,
              token: token
            },
          success: function(callList){
            $("#userAdministration").html(callList);
          }
      });
  }
</script>
  <script type="text/javascript">
    function deleteUserClick(currentNumPage, start, activePage, idDelete){
      searchDialerName = document.getElementById("findNameList").value;
          if (confirm("Do you really want to delete the user?"))
          { 
          $.ajax({
                   url: 'userAdministration.php',
                   type: 'post',
                   data: {
                          currentNumPage: currentNumPage,
                          start: start,
                          activePage: activePage,
                          idDelete: idDelete
                         },
                    success: function(callList)
                    {
                      $("#userAdministration").html(callList);
                    }
                 });
        }
          else
            {
             alert("Action canceled!");
            }
    }
</script>
<script type="text/javascript">
function editUserClick(currentNumPage, start, activePage, idEdit){
    $.ajax({
    	url: 'userAdministration.php',
    	type: 'post',
    	data: {
    		currentNumPage: currentNumPage,
            start: start,
            activePage: activePage,
    		idEdit: idEdit,

    	},
        success: function(callList)
        {
        	$("#userAdministration").html(callList);
            $("#userEdit").modal('show');
        }
    })
}
</script>
<script type="text/javascript">
  function editUserSave(idEditSave, currentNumPage, start, activePage){
          editFirstName = document.getElementById("editFirstName").value;
          editLastName = document.getElementById("editLastName").value;
          editNewUserPassword = document.getElementById("editNewUserPassword").value;
          editConfirmNewUserPassword = document.getElementById("editConfirmNewUserPassword").value;
          editAccessMode = document.getElementById("editAccessMode").value;
          editToken = document.getElementById("editToken").value;
          //currentNumPage = 1;
          //start = 0;
         // $("#userEdit").modal('hide')
        $.ajax({
          url: 'userAdministration.php',
          type: 'post',
          data: {
          	  currentNumPage: currentNumPage,
              start: start,
              activePage: activePage,
              idEditSave: idEditSave,
              editFirstName: editFirstName,
              editLastName: editLastName,
              editNewUserPassword: editNewUserPassword,
              editConfirmNewUserPassword: editConfirmNewUserPassword,
              editAccessMode: editAccessMode,
              editToken: editToken
            },
          success: function(callList){
          	//alert('User updated');
          	//$("#userEdit").modal('hide');
          	//alert('User updated');
           // $("#userEdit").modal('hide');
           // alert('resultEditUser');
            $("#userAdministration").html(callList);
            //$('#userEdit').removeClass('fade');
 			      // $("#userEdit").modal('hide');
          //   $("#usersPanel").html(callList);
            //alert('User updated');
          
          }
      });
  }
</script>









