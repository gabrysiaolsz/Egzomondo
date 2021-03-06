<?php
    include '../_common/redirect_to_login.php';
    include '../_common/connect_to_db.php';
    $target_dir = '../../uploads/profilepic/';

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
            $uploadOk = 1;

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

            if ($_FILES['fileToUpload']['size'] != 0) {
                $target_file = $target_dir.$id.'.png';
            
                $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

                // Check if image file is a actual image or fake image
                if(isset($_POST["submit"])) {
                    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                    if($check !== false) {
                        $error = "File is an image - " . $check["mime"] . ".";
                        $uploadOk = 1;
                    } else {
                        $error = "File is not an image.";
                        $uploadOk = 0;
                    }
                }

                // Check if file already exists
                if (file_exists($target_file)) {
                    unlink($target_file);
                }

                // Check file size
                if ($_FILES["fileToUpload"]["size"] > 500000) {
                    $error = "Sorry, your file is too large.";
                    $uploadOk = 0;
                }

                // Allow certain file formats
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" ) {
                    $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    $uploadOk = 0;
                }

                // Check if $uploadOk is set to 0 by an error
                if ($uploadOk == 0) {
                    echo "Sorry, your file was not uploaded.";
                    // if everything is ok, try to upload file
                } else {
                    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                        // echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
                    } else {
                        $error = "Sorry, there was an error uploading your file.";
                    }   
                }
            }
            
            
            $q = "UPDATE KONTO SET ";
            if ($_POST['username'] != '') {
                $q .= "LOGIN = '$_POST[username]', ";
            }
            $q .= "PLEC = $new_sex, ";
            if ($_POST['weight'] != '') {
                $q .= "WAGA = $_POST[weight], ";
            }
            if ($_POST['height'] != '') {
                $q .= "WZROST = $_POST[height], ";
            }
            $q .= "ZGODA_RANKING = $checked WHERE ID = '$id'";
            $query = oci_parse($conn, $q);
            echo $q;
            oci_execute($query);

            $r = oci_commit($conn);
            if (!$r) {
                $e = oci_error($conn);
                trigger_error(htmlentities($e['message']), E_USER_ERROR);
            }

            $stid = oci_parse($conn, "SELECT * FROM Konto WHERE login='".$_SESSION['login']."'");
            oci_execute($stid);
            $row = oci_fetch_array($stid, OCI_BOTH + OCI_RETURN_NULLS);
            [$id, $username, $password, $sex, $weight, $height, $rankingswitch] = $row;
            
            
            if ($error) {
                
            } else {
                $success = "Success!";
                $_SESSION['login'] = $_POST['username'];
                header("location:index.php");
            }
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
        <?php include '../_common/navbar.php'; ?>
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
                <div id="user" style="width:250px;left:50px;position: relative;">
                    <form action="editprofile.php" enctype="multipart/form-data" method="POST" id="userinput" class="input-group" >
                        <table>
                            <tr>
                                <td><div id="pfp-and-name">
                                        <div id="pfp-container">
                                            <?php if (file_exists(''.$target_dir.''.$id.'.png')) { ?>
                                                <img src="../../uploads/profilepic/<?php echo $id;?>.png" />
                                            <?php } else { ?>
                                                <img src="../../style/img/default-pfp.png" />
                                            <?php } ?>
                                        </div>
                                    </div></td>
                                <td>Change profile picture:<input type="file" value="Change your profile pic" name="fileToUpload"/></td>
                            </tr>
                            <tr>
                                <td>Username: </td>
                                <td><input type="text" minlength="4" maxlength="20" pattern="[A-Za-z0-9@#$%]+" id="user" name="username" class="input-field" placeholder="User" value="<?php echo $username; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Weight: </td>
                                <td><input type="number" class="input-field" name="weight" placeholder="Weight (kg)" min="0" max="200" step="1" value="<?php echo $weight; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Height: </td>
                                <td><input type="number" class="input-field" name="height" placeholder="Height (cm)" min="0" max="210" step="1" value="<?php echo $height; ?>" /></td>
                            </tr>
                            <tr>
                                <td>Sex: </td>
                                <td><input type="radio" id="male" name="sex" value="male" <?php if ($sex == 1) echo 'checked="checked"'; ?> />
                                    <label for="male">Male</label>
                                    <input type="radio" id="female" name="sex" value="female" <?php if ($sex == 0) echo 'checked="checked"'; ?> />
                                    <label for="female">Female</label><br /></td>
                            </tr>
                            <tr>  
                                <td colspan=2><input type="checkbox" id="ranking" name="ranking" <?php if ($rankingswitch == 1) echo 'checked="checked"'; ?> />   Approval for global rankings.</td>
                            </tr>
                        </table>                                       
                        
                        <button type="submit" name="usersubmit" class="submit-btn" style="width:300px">Submit changes</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
