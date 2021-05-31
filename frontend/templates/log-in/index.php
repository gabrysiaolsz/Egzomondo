<?php
    include '../_common/connect_to_db.php';

    // Checks whether client is already log on.
    session_start();
    $location = '../profile';
    if (isset($_SESSION['login']))
        header('location:'.$location);

    // Checks whether the client tries to log in.
    if (isset($_POST['submit-log'])) {
        $q = "SELECT * FROM KONTO WHERE LOGIN = '$_POST[login]' AND HASLO = '$_POST[passwd]'";
        $query = oci_parse($conn, $q);
        oci_execute($query);
        oci_fetch($query);

        if (oci_num_rows($query) < 1) {
            $error = "No profile with that username / Wrong password";
        } else {
            $_SESSION['login'] = $_POST['login'];

            $stid = oci_parse($conn, "SELECT id FROM Konto WHERE login ='$_POST[login]'");
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

            $_SESSION['login'] = $_POST['login'];
            header('location:'.$location);
            oci_free_statement($insert);
        }
    }
    oci_free_statement($query);
    oci_close($conn);
?>

<html>
    <head>
        <title>Login and registration</title>
        <link rel="shortcut icon" href="../../style/img/logo_icon.png">
        <link rel="stylesheet" type="text/css" href="../../style/css/global-style.css" />
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar-style.css" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <script src="script.js"></script>
        <script src="https://kit.fontawesome.com/67c66657c7.js"></script>
    </head>
    <body>
        <?php include '../_common/navbar_not_logged_in.php' ?>
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
                    <input type="text" minlengh="4" maxlenght="20" pattern="[A-Za-z0-9@#$%]+" name="login" class="input-field" placeholder="User id" required>
                    <input type="password" minlengh="4" maxlenght="20" pattern="[A-Za-z0-9@#$%]+" name="passwd" class="input-field" placeholder="Enter password" required>
                    <input type="checkbox" name="rememberpwd" class="check-box"><span class="span-log">Remember Password</span>
                    <button type="submit" name="submit-log" class="submit-btn">Log in</button>
                    <a href="forgot_passwd.php" class="forgot-password"><span class="span-forgotpwd">Forgot password?</span></a>
                </form>
                
                <form action="index.php" method="POST" id="register" class="input-group">
                    <input type="text" minlengh="4" maxlenght="20" pattern="[A-Za-z0-9@#$%]+" name="login" class="input-field" placeholder="User id" required>
                    <input type="password" minlengh="4" maxlenght="20" pattern="[A-Za-z0-9@#$%]+" name="passwd" class="input-field" placeholder="Enter password" required> 
                    <input type="password" minlengh="4" maxlenght="20" pattern="[A-Za-z0-9@#$%]+" name="chkpassword" class="input-field" placeholder="Confirm password" required>
                    <input type="checkbox" class="check-box" required><span class="span-reg">I agree to the terms and conditions</span>
                    <button type="submit" name="submit-reg" class="submit-btn">Register</button>
                </form>
            </div>
        </div>
    </body>
</html>
