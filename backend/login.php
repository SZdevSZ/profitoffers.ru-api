<?php
    namespace predicted\api;
    ini_set('display_errors','On'); 
    error_reporting(E_ALL);
    session_start();
    require_once 'dbconf.php';
    require_once "cli-functions.php";
    if (isset($_SESSION['login']) && isset($_SESSION['password'])){
        $login = $_SESSION['login'];
        $password = ($_SESSION['password']);
    }
        else{
          $login = $_POST['login'];
          $password = sha1($_POST['password']);
          $_SESSION['origPassword'] = ($_POST['password']);
        }
    $login = $_POST['login'];
    $password = sha1($_POST['password']);
    unset($_SESSION['error_auth']);
    //echo checkUser($login, $password, $dbhost, $dbname, $dblogin, $dbpassword, $charset);
    if (checkUser($login, $password, $dbhost, $dbname, $dblogin, $dbpassword, $charset) == 'ok'){
    $_SESSION['login'] = $login;
    $_SESSION['password'] = $password;
    
    }
        else $_SESSION['error_auth'] = 1;
    if (isset($_SESSION['error_auth']) && !empty($_SESSION['error_auth']) && $_SESSION['error_auth'] == 1){
    header("Location: index.php");
    //header("Location:".$_SERVER['HTTP_REFERER']);
    }

    //require_once "user_panel.php";
    // $session = (session_id());
    // echo $session;
    // echo '<br>';
    // var_dump($_SESSION);
    // echo '/br';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <meta http-equiv=Content-Type content="text/html;charset=utf-8">
  <title>AMS</title>
  <!-- Bootstrap core CSS -->
  <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.4/css/bootstrap.min.css" crossorigin="anonymous"> 
  <!-- Custom styles for this template -->
  <link href="css/simple-sidebar.css" rel="stylesheet">
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>   
    <!-- Change menu -->
  <link href="css/table.css" rel="stylesheet">
</head>
<body>
  <div class="d-flex" id="wrapper">
    <!-- Sidebar -->
    <div class="bg-dark border-right" id="sidebar-wrapper">
    <div class="sidebar-heading text-danger">Asterisk Management System</div>
    <div class="list-group list-group-flush">
<!--       <button class="tablink list-group-item list-group-item-action border border-dark bg-dark text-light" onclick="openPage('DialingList', this, 'red')" id="defaultOpen">Dialing list</button>
      <button class="tablink list-group-item list-group-item-action border border-dark bg-dark text-light" onclick="openPage('addList', this, 'green')">Add list</button>
      <button class="tablink list-group-item list-group-item-action border border-dark bg-dark text-light" onclick="openPage('DialingDB', this, 'green')">Dialing DB</button> -->
      <button class="tablink list-group-item list-group-item-action border border-dark bg-dark text-light" onclick="openPage('queueManagement', this, 'orange')" id="defaultOpen">Queue management</button>
      <button class="tablink list-group-item list-group-item-action border border-dark bg-dark text-light" onclick="openPage('userAdministration', this, 'blue')">User administration</button>
    </div>
   <!-- <a href="#" class="list-group-item list-group-item-action bg-light">Profile</a>
        <a href="#" class="list-group-item list-group-item-action bg-light">Status</a> -->
  </div>
    <!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper">
      <nav class="navbar navbar-expand-lg navbar-dark bg-dark text-light border-bottom">
        <button class="btn btn-outline-primary text-light" id="menu-toggle">Toggle Menu</button>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
            <li class="nav-item active"> 
              <?php
                   require_once "user_panel.php";
              ?>
            </li>            
            <li>
                <a class="nav-link text-primary" href="logout.php">Exit<span class="sr-only">(current)</span></a> 
            </li>
      <!--  <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Dropdown
          </a>
       <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="#">Action</a>
            <a class="dropdown-item" href="#">Another action</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#">Something else here</a>
          </div>
        </li> -->
          </ul>
        </div> 
      </nav>
<?php
    echo "<input type='hidden' name='login' id='login' value=".$login.">";
    echo "<input type='hidden' name='password' id='password' value=".$password.">";
?>
<script type="text/javascript">
  $(function(){
    $("#sub").click(function(){
      var login = document.getElementById("login").value;
      var password = document.getElementById("password").value; 
      var dialerName = document.getElementById("dialerName").value;
      var numberPhoneList = document.getElementById("numberPhoneList").value;
      var agent = document.getElementById("agent").value;
      var context = document.getElementById("context").value;
      var userField = document.getElementById("userField").value;
        $.ajax({
          url: 'tableListCallLite.php',
          type: 'post',
          data: {
            login: login, 
            password: password, 
            dialerName: dialerName, 
            numberPhoneList: numberPhoneList, 
            agent: agent, 
            context: context, 
            userField: userField },
          success: function(callList1){
            $("#addCallList").html(callList1);
            document.getElementById("numberPhoneList").value = "";
            document.getElementById("dialerName").value = "";
            document.getElementById("agent").value = "";
            document.getElementById("context").value = "";
            document.getElementById("userField").value = "";
            alert("success");
          }
        });
    });
  });
</script>
<!--           <div id="DialingDB" class="tabcontent container my-3 mx-5">
            <div class="container">
            </div>
            <div class="container-fluid my-3 mx-3 " id="addCallList">
            </div>
          </div> -->
          <div id="userAdministration" class="tabcontent container-fluid" >
            <!-- <div  class="container-fluid mx-auto mt-3 card bg-light shadow-lg rounded" id="usersPanel" id="usersPanel">
              <?php// require_once 'usersPanel.php';?>
            </div> -->
          </div>
          <div id="queueManagement" class="tabcontent container-fluid" >
          </div> 
          
        </div>
  <!-- /#page-content-wrapper -->
<script>
  function openPage(pageName,elmnt,color) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablink");
    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].style.backgroundColor = "";
    }
    document.getElementById(pageName).style.display = "block";
    elmnt.style.backgroundColor = color;
  }
  // Get the element with id="defaultOpen" and click on it
  document.getElementById("defaultOpen").click();
</script>
  </div>
  <!-- /#wrapper -->
  <!-- Bootstrap core JavaScript -->
  <script src="vendor/jquery/jquery.min.js"></script>
  <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- Menu Toggle Script -->
  <script>
    $("#menu-toggle").click(function(e) {
      e.preventDefault();
      $("#wrapper").toggleClass("toggled");
    });
  </script>

 <script type="text/javascript">
  $(function(){
    $(document).ready(function(){
      var login = document.getElementById("login").value;
      var password = document.getElementById("password").value; 
        $.ajax({
          url: 'userAdministration.php',
          type: 'post',
          data: {
            login: login, 
            password: password, 
         },
          success: function(callList){
            $("#userAdministration").html(callList);
          }
        });
    });
  });
</script>
 <script type="text/javascript">
  $(function(){
    $(document).ready(function(){
      var login = document.getElementById("login").value;
      var password = document.getElementById("password").value; 
        $.ajax({
          url: 'queueManagement.php',
          type: 'post',
          data: {
            login: login, 
            password: password, 
         },
          success: function(callList){
            $("#queueManagement").html(callList);
          }
        });
    });
  });
</script>

 <script type="text/javascript">
//   $(function(){
//     $(document).ready(function(){
//       var login = document.getElementById("login").value;
//       var password = document.getElementById("password").value; 
//        $.ajax({
//           url: 'tableListCallLite.php',
//           type: 'post',
//           data: {
//             login: login, 
//             password: password, 
// 				 },
//           success: function(callList){
//             $("#addCallList").html(callList);
//           }
//         });
//     });
//   });
 </script>



<script type="text/javascript">
  //document.getElementById("clearButton").onclick = function(e) {
  // Если необходимо предотвратить/отменить событие по умолчанию,
  // то необходимо вызвать метод preventDefault у события
  // https://developer.mozilla.org/ru/docs/Web/API/Event/preventDefault
  // e.preventDefault();
  // если необходимо также предотвратить дальнейшее "всплытие" события,
  // то необходимо вызвать метод stopPropagation у события
  // https://developer.mozilla.org/ru/docs/Web/API/Event/stopPropagation
  // e.stopPropagation();
  document.getElementById("numberPhoneList").value = "";
  //}	

</script>
</body>
</html>
