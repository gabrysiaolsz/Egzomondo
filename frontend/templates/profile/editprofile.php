<?php
    // Connects to SQLPLUS database.
    $user = 'wz418498';
    $password = 'IO2021';
    $db = '//labora.mimuw.edu.pl/LABS';
    $conn = oci_connect($user, $password, $db);
    $redirect_to_login = substr($_SERVER["REQUEST_URI"], 0, -8)."log-in";
    session_start();

    // Checks whether connection has been done.
    if (!$conn) {
        echo "oci_connect failed\n";
        $e = oci_error();
        echo $e['message'];
    }

    # If you're not logged in, redirect to login page.
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != TRUE) {
        header('location:'.$redirect_to_login);
    }

    $stid = oci_parse($conn, "SELECT * FROM Konto WHERE login='".$_SESSION['login']."'");
    oci_execute($stid);
    $row = oci_fetch_array($stid, OCI_BOTH + OCI_RETURN_NULLS);
    [$id, $username, $password, $sex, $weight, $height, $rankingswitch] = $row;

    if (isset($_POST['usersubmit'])) {
        # Check if new username is available
        if (strcmp($_POST['username'], $username) != 0) {
            $q = "SELECT * FROM KONTO WHERE LOGIN = '$_POST[username]'";
            $query = oci_parse($conn, $q);
            oci_execute($query);
            oci_fetch($query);

            if (oci_num_rows($query) != 0) {
                $error = "There's already a profile with that username";
            } 
        }
        if ($error) {
            
        } else {
            if ($_POST['sex'] == 'female') {
                $new_sex = 0;
            } else {
                $new_sex = 1;
            }
            if ($_POST['ranking'] == 'on') {
                $checked = 1;
            } else {
                $checked = 0;
            }
            $q = "UPDATE KONTO SET LOGIN = '$_POST[username]', PLEC = $new_sex, WAGA = $_POST[weight], WZROST = $_POST[height], ZGODA_RANKING = $checked WHERE ID = '$id'";
            $query = oci_parse($conn, $q);
            oci_execute($query);

            $stid = oci_parse($conn, "SELECT * FROM Konto WHERE login='".$_SESSION['login']."'");
            oci_execute($stid);
            $row = oci_fetch_array($stid, OCI_BOTH + OCI_RETURN_NULLS);
            [$id, $username, $password, $sex, $weight, $height, $rankingswitch] = $row;
            $success = "Success!";
        }
    }
    
    oci_free_statement($stid);
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
                <a href="#">
                    <div class="logo">
                        <img src="../../style/img/logo_icon.png" id="logo-icon-normal">
                        <img src="../../style/img/logo_icon_hover.png" id="logo-icon-hover">
                        <div id="logo-text">Egzomondo</div>
                    </div>
                </a>
                <ol>
                    <li><a href="<?php echo substr($_SERVER["REQUEST_URI"], 0, -16);?>">My profile</a></li>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About us</a></li>
                </ol>
                <div class="search_box">
                    <input type="search" placeholder="Search">
                    <a href="#"><span class="fa fa-search"></span></a>
                </div>
            </nav>
        </div>
        <div class="main-box">
            <div class="forgotpwd-box">
                <?php 
                    if ($error) {
                        echo "<p style=\"color:red; text-align:center\">".$error."</p>";
                    }
                ?>
                <?php 
                    if ($success) {
                        echo "<p style=\"color:green; text-align:center\">".$success."</p>";
                    }
                ?>
                <b><h2 style="text-align: center;">Edit your profile <?php echo $username?></h2></b>
                <div id="user" style="width:250px;left:75px;position: relative;">
                    <form action="editprofile.php" method="POST" id="userinput" class="input-group" >

                        <table>
                            <tr>
                                <td>Username: </td>
                                <td><input type="text" id="user" name="username" class="input-field" placeholder="User" value="<?php echo $username; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Password: </td>
                                <td><input type="password" id="password" name="password" class="input-field" readonly value="<?php echo str_repeat('*', strlen($password)); ?>" /></td>
                            </tr>
                            <tr>
                                <td>Weight: </td>
                                <td><input type="number" class="input-field" name="weight" placeholder="Weight (kg)" min="0" step="1" value="<?php echo $weight; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Height: </td>
                                <td><input type="number" class="input-field" name="height" placeholder="Height (cm)" min="0" step="1" value="<?php echo $height; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Sex: </td>
                                <td><input type="radio" id="male" name="sex" value="male" <?php if ($sex == 1) echo 'checked="checked"'; ?> />
                                    <label for="male">Male</label>
                                    <input type="radio" id="female" name="sex" value="female" <?php if ($sex == 0) echo 'checked="checked"'; ?> />
                                    <label for="female">Female</label><br /></td>
                            </tr>
                            <tr>  
                                <td colspan=2><input type="radio" id="ranking" name="ranking" <?php if ($rankingswitch == 1) echo 'checked="checked"'; ?> />   Approval for global rankings.</td>
                            </tr>
                        </table>                                       
                        
                        <button type="submit" name="usersubmit" class="submit-btn" style="top:150px">Submit changes</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>

