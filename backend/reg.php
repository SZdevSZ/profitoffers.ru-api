<?php
session_start();
require_once "functions.php";
require_once "user_panel.php";

if (isset($_POST['reg'])){
    $login = htmlspecialchars($_POST['login']);
    $password = htmlspecialchars($_POST['password']);
    $bad = false;

    unset($_SESSION['error_login']);
    unset($_SESSION['error_password']);
    unset($_SESSION['success_reg']);
 if ((strlen($login) < 3) || (strlen($login)) > 32) {
    $_SESSION['error_login'] = 1;
    $bad = true;
    }
 if ((strlen($password) < 3) || (strlen($password)) > 32) {
        $_SESSION['error_password'] = 1;
        $bad = true;
    }
 if (!$bad) {
    regUser($login, sha1($password));
     $SESSION['reg_success'] = 1;
     //header("Location: index.php");
    }
}
?>
<!DOCTYPE html!>
    <html xmls = "http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="content-type" content="text/html; charset=windows-1251">
    <title>Регистрация</title>
        </head>
<body>
<table border="1" width="100%" cellpadding="0">
    <tr>
    <td colspan="2">
    <img src = "images/logo.jpg" width="100%" alt = "Шапка сайта" />
    </td>
    </tr>
<tr>
    <td style="width: 20%;" valign="top">
        <?php
        #require_once "menu.html"
        require_once "user_panel.php"
        ?>
    </td>
    <td style="text-align: center;">
        <h1>Регистрация</h1>
<form id = "form1" action="" method="post">
   <?php
    if ($_SESSION['error_login'] ==1) echo "ERROR_LOGIN";
    if ($_SESSION['error_password'] ==1) echo "ERROR_PASSWODR";
    ?>
    <p>
        <label>Ваш логин:</label>
        <input type = 'text' name = 'login'/>
    </p>
    <p>
        <label>Ваш пароль:</label>
        <input type = 'password' name = 'password'/>
    </p>
  <!  <tr>
        <td colspan = '2' >
            <input type = "submit" value= 'Войти' />
        </td>
    </tr> !>




</body>

</html>
