<?php 
namespace predicted\api; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
</head>
<body>
<div>
    <div class="container">
         <div class="row align-items-center border border-dark">
         <!--   <div class="col">
            </div> -->
                <div class="col">  
                    <?php
                         
                         //require_once "user_panel.php";
                         require_once "cli-functions.php";
                         require_once 'dbconf.php';
                             $login = $_SESSION['login'];
                             $password = $_SESSION['password'];
                         if (checkUser($login, $password, $dbhost, $dbname, $dblogin, $dbpassword, $charset)) {
                            echo "<a class='nav-link'>Hello, <b>".$_SESSION['login']."</b>!</a>";
                            // echo "<a class='nav-link text-primary' href='logout.php'>Exit<span class='sr-only'>(current)</span></a>";
                          }
                          else {
                              if ($_SESSION['error_auth'] == 1) {
                                  echo 'Incorrect login or password';
                                  unset($_SESSION['error_auth']);
                              }
                              require_once "formlogin.html";
                          }
                    ?> 
                </div> 
          <!--  <div class="col">
            </div> -->
         </div>
    </div> 
</div>

</body>

</html>
