<?php
    session_start();
    unset($_SESSION['login']);
    unset($_SESSION['password']);
    header("Location: ".$_SERVER['HTTP_REFERER']);
?>