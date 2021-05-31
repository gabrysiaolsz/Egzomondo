<?php
    include '../_common/connect_to_db.php';

    $firststyle = "";
    $secondstyle = "display:none";
    
    if (isset($_POST['usersubmit'])) {
        $query = oci_parse($conn, "SELECT * FROM KONTO WHERE LOGIN = '$_POST[login]'");
        oci_execute($query);
        oci_fetch($query);

        if (oci_num_rows($query) < 1) {
            $error = "No profile with that username";
        } else {
            $tmp = $secondstyle;
            $secondstyle = $firststyle;
            $firststyle = $tmp;
            $username = " for $_POST[login]";
        }
    }

    if (isset($_POST['passwdsubmit'])) {
        if (strcmp($_POST[newpwd], $_POST[newpwd2]) == 0) {
            $query = oci_parse($conn, "UPDATE KONTO SET HASLO = '$_POST[newpwd]' WHERE LOGIN = '$_POST[login]'");
            oci_execute($query);
            $r = oci_commit($conn);
            if (!$r) {
                $e = oci_error($conn);
                trigger_error(htmlentities($e['message']), E_USER_ERROR);
            }
            header('location:index.php');
        } else {
            $error = "Passwords are not matching";
        }
    }

    oci_close($conn);
?>
<html>
    <head>
        <title>Changing password</title>
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
            <div class="forgotpwd-box">
                <?php 
                    if ($error) {
                        echo "<p style=\"color:red; text-align:center\">".$error."</p>";
                    }
                ?>
                <b><h2 style="text-align: center;">Reset password<?php echo $username?></h2></b>
                <div id="user" style="<?php echo $firststyle?>">
                    <form action="forgot_passwd.php" method="POST" id="userinput" class="input-group" style="top:50px;left:50px">
                        <input type="text" minlenght="4" maxlenght="20" pattern="[A-Za-z0-9@#$%]+" name="login" class="input-field" placeholder="User id" required><br><br><br><br>
                        <button type="submit" name="usersubmit" class="submit-btn" style="top:150px">Submit</button>
                    </form>
                </div>
                <div id="newpasswd" style="<?php echo $secondstyle?>">
                    <form action="forgot_passwd.php" method="POST" class="input-group" style="top:50px;left:50px">
                        <input type="hidden" name="login" value="<?php echo $_POST[login]?>">
                        <input type="password" minlenght="4" maxlenght="20" pattern="[A-Za-z0-9@#$%]+" name="newpwd" class="input-field" placeholder="New password" required>
                        <input type="password" minlenght="4" maxlenght="20" pattern="[A-Za-z0-9@#$%]+" name="newpwd2" class="input-field" placeholder="New password" required>
                        <button type="submit" name="passwdsubmit" class="submit-btn" style="top:130px">Update password</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
