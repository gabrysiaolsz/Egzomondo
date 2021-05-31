<?php
session_start();
session_unset();
session_unset("loggedin");
session_destroy();
header("Location: ../log-in");
?>
