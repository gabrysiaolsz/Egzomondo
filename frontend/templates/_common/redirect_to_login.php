<?php
    session_start();
    $redirect_path = '../log-in';
    if (!isset($_SESSION['login']))
        header('location:'.$redirect_path);
?>
