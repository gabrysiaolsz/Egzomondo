<?php
    // Connects to SQLPLUS database.
    $user = 'wz418498';
    $password = 'IO2021';
    $db = '//labora.mimuw.edu.pl/LABS';
    $conn = oci_connect($user, $password, $db);
    session_start();

    // Checks whether connection has been done.
    if (!$conn) {
        echo "oci_connect failed\n";
        $e = oci_error();
        echo $e['message'];
    }

    // Checks whether already log on.
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
            header('location:'.$_SESSION['redirectURL']);
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
        } else {
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
            header('location:/frontend/templates/profile/index.html');
            oci_free_statement($insert);
        }
    }
    oci_free_statement($query);
    oci_close($conn);
?>

<html>
<head>
    <title> Login and registration</title>
    <link rel="stylesheet" href="../css/style.css">

</head>
<body>
    
    <div class="hero">
        <div class="form-box">
            <div class="button-box">
                <div id="btn"></div>
                <button type="button" class="toggle-btn" onclick="login()">Log in</button>
                <button type="button" class="toggle-btn" onclick="register()">Register</button>
            </div>
            <?php 
                    if ($error) {
                        echo "<p style=\"color:red; text-align:center\">".$error."</p>";
                    }
                ?>
            <form action="login.php" method="POST" id="login" class="input-group">
                <input type="text" name="login" class="input-field" placeholder="User id" required>
                <input type="text" name="passwd" class="input-field" placeholder="Enter password" required>
                <input type="checkbox" name="rememberpwd" class="check-box"><span>Remember Password</span>
                <button type="submit" name="submit-log" class="submit-btn">Log in</button>
            </form>
            <form action="login.php" method="POST" id="register" class="input-group">
                <input type="text" name="login" class="input-field" placeholder="User id" required>
                <input type="text" name="passwd" class="input-field" placeholder="Enter password" required> 
                <input type="text" name="chkpassword" class="input-field" placeholder="Confirm password" required>
                <input type="checkbox" class="check-box" required><span>I agree to the terms and conditions.</span>
                <button type="submit" name="submit-reg" class="submit-btn">Register</button>
            </form>
        </div>
    </div>
    
    <script>
    
    var x = document.getElementById("login");
    var y = document.getElementById("register");
    var z = document.getElementById("btn");
    
    function register(){
        x.style.left = "-400px";
        y.style.left = "50px";
        z.style.left = "110px";
    }
    
    function login(){
        x.style.left = "50px";
        y.style.left = "450px";
        z.style.left = "0";
    }
    
    </script>

</body>
</html>
