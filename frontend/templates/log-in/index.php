<?php
    // Connects to SQLPLUS database.
    $user = 'wz418498';
    $password = 'IO2021';
    $db = '//labora.mimuw.edu.pl/LABS';
    $conn = oci_connect($user, $password, $db);
    $location = substr($_SERVER["REQUEST_URI"], 0, -16)."profile";
    session_start();

    // Checks whether connection has been done.
    if (!$conn) {
        echo "oci_connect failed\n";
        $e = oci_error();
        echo $e['message'];
    }

    // Checks whether client is already log on.
    if ($_SESSION['loggedin'] == TRUE) {
        header('location:'.$_SESSION['redirectURL']);
    }

    // Checks whether the client tries to log in.
    if (isset($_POST['submit-log'])) {
        $q = "SELECT * FROM KONTO WHERE LOGIN = '$_POST[login]' AND HASLO = '$_POST[passwd]'";
        $query = oci_parse($conn, $q);
        oci_execute($query);
        oci_fetch($query);

        if (oci_num_rows($query) < 1) {
            $error = "No profile with that username / Wrong password";
        } else {
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['login'] = $_POST['login'];
            $_SESSION['password'] = $_POST['passwd'];

            $stid = oci_parse($conn,
                "SELECT id FROM Konto WHERE login ='$_POST[login]'"
            );
            oci_execute($stid);
            $row = oci_fetch_array($stid, OCI_BOTH  + OCI_RETURN_NULLS);
            $_SESSION['id'] = $row[0];

            header('location:'.$location);
        }
    }

    // Checks whether the client tries to register.
    if (isset($_POST['submit-reg'])) {  
        $q = "SELECT * FROM KONTO WHERE LOGIN = '$_POST[login]'";
        $query = oci_parse($conn, $q);
        oci_execute($query);
        oci_fetch($query);

        if (oci_num_rows($query) != 0) {
            $error = "There is already a profile with that username.";
            echo oci_num_rows($query);
        } else if (strcmp($_POST['passwd'], $_POST['chkpassword']) != 0) {
            $error = "Passwords are not matching.";
        } else if (strcmp($_POST['login'], filter_var($_POST['login'],FILTER_SANITIZE_EMAIL)) != 0) {
            $error = "Illegal characters in User id";
        }else if (strcmp($_POST['passwd'], filter_var($_POST['passwd'],FILTER_SANITIZE_EMAIL)) != 0) {
            $error = "Illegal characters in password";
        }else {
            $login = "'".$_POST['login']."'";
            $password = "'".$_POST['passwd']."'";

            $i = "INSERT INTO KONTO VALUES (null, $login, $password, null, null, null, null)";
            $insert = oci_parse($conn, $i);

            $rc = oci_execute($insert);
            if (!$rc) {
                $e = oci_error($insert);
                var_dump($e);
            }
            oci_commit($conn);

            $_SESSION['loggedin'] = TRUE;
            $_SESSION['login'] = $_POST['login'];
            $_SESSION['password'] = $password;
            header('location:'.$location);
            oci_free_statement($insert);
        }
    }
    oci_free_statement($query);
    oci_close($conn);
?>

<html>
    <head>
        <title> Login and registration</title>
        <link rel="shortcut icon" href="../../style/img/logo_icon.png">
        <link rel="stylesheet" type="text/css" href="../../style/css/global-style.css" />
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar-style.css" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <script src="script.js"></script>
        <script src="https://kit.fontawesome.com/67c66657c7.js"></script>
    </head>
    <body>
    <div id="navbar">
            <nav>
                <a href="../home">
                    <div class="logo">
                        <img src="../../style/img/logo_icon.png" id="logo-icon-normal">
                        <img src="../../style/img/logo_icon_hover.png" id="logo-icon-hover">
                        <div id="logo-text">Egzomondo</div>
                    </div>
                </a>
                <ol>
                    <li><a href="../home">Home</a></li>
                    <li><a href="../about">About us</a></li>
                </ol>
                <div class="search_box">
                    <input type="search" placeholder="Search">
                    <a href="#"><span class="fa fa-search"></span></a>
                </div>
            </nav>
        </div>
        <div class="main-box">
            <div class="form-box">
                <div class="button-box">
                    <div id="btn"></div>
                    <button type="button" class="toggle-btn" onclick="login()">Log in</button>
                    <button type="button" class="toggle-btn register-btn" onclick="register()">Register</button>
                </div>
                <?php 
                    if ($error) {
                        echo "<p style=\"color:red; text-align:center\">".$error."</p>";
                    }
                ?>
                <form action="index.php" method="POST" id="login" class="input-group">
                    <input type="text" name="login" class="input-field" placeholder="User id" required>
                    <input type="password" name="passwd" class="input-field" placeholder="Enter password" required>
                    <input type="checkbox" name="rememberpwd" class="check-box"><span class="span-log">Remember Password</span>
                    <button type="submit" name="submit-log" class="submit-btn">Log in</button>
                    <a href="forgot_passwd.php" class="forgot-password"><span class="span-forgotpwd">Forgot password?</span></a>
                </form>
                
                <form action="index.php" method="POST" id="register" class="input-group">
                    <input type="text" name="login" class="input-field" placeholder="User id" required>
                    <input type="password" name="passwd" class="input-field" placeholder="Enter password" required> 
                    <input type="password" name="chkpassword" class="input-field" placeholder="Confirm password" required>
                    <input type="checkbox" class="check-box" required><span class="span-reg">I agree to the terms and conditions</span>
                    <button type="submit" name="submit-reg" class="submit-btn">Register</button>
                </form>
            </div>
        </div>
    </body>
</html>

