<?php
    $user = 'wz418498';
    $password = 'IO2021';
    $db = '//labora.mimuw.edu.pl/LABS';
    $conn = oci_connect($user, $password, $db);
    $redirect_to_login = "../log-in";
    $redirect_to_new_challenge = substr($_SERVER["REQUEST_URI"], 0, -7)."new_challenge";
    $redirect_to_search = substr($_SERVER["REQUEST_URI"], 0, -7)."search";
    $redirect_to_challenge = substr($_SERVER["REQUEST_URI"], 0, -7)."challenge/?id=";
    $redirect_to_profile = "../profile";
    $upload_dir = '../../uploads/profilepic/';

    include '../_common/redirect_to_login.php';

    if (!$conn) {
        echo "oci_connect failed\n";
        $e = oci_error();
        echo $e['message'];
    }

    session_start();
        
    if (!isset($_POST['keyword'])) {
        header('location:'.$redirect_to_profile);
    }
?>
<html>
    <head>
        <title> Egzomondo - <?php echo $login; ?> </title>
        <link rel="shortcut icon" href="../../style/img/logo_icon.png">
        <link rel="stylesheet" type="text/css" href="../../style/css/global-style.css" />
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar-style.css" />
        <link rel="stylesheet" type="text/css" href="style.css" />
        <script src="https://kit.fontawesome.com/67c66657c7.js"></script>
    </head>
    <body>
        <?php include '../_common/navbar.php'; ?>
        <div id="below-navbar">
            <div id="container" style="display:block;">
                <div id="inner_search">
                    <table style="margin: 0 auto; margin-left:20%;margin-right:20%">
                        <colgroup>
                            <col span="1" style="width:400px">
                            <col span="1" style="width:400px">
                        </colgroup>
                    
                        <tr>
                            <td colspan=2>
                                <div class="section-header"><h4 style="text-align:center"> You've searched for <?php echo $_POST['keyword']; ?> </h4></div>
                            </td>
                        </tr>
                        <tr>
                        <td><div class="section-header"><h4 style="text-align:center"> Users: </h4></td></div>
                        <td><div class="section-header"><h4 style="text-align:center"> Challenges: </h4></td></div>
                        </tr>
                        <tr><td>
                            <!-- Friends -->
                            <div id="friends" style="height:700px; overflow-y:auto;float:left; display:block;">
                                <div id="friends-flexbox">
                                    <?php
                                        // $query = 
                                        $stid = oci_parse($conn, "
                                            SELECT id, login FROM KONTO WHERE LOGIN LIKE '%$_POST[keyword]%'
                                        ");

                                        oci_execute($stid);

                                        while ($row = oci_fetch_array($stid, OCI_BOTH  + OCI_RETURN_NULLS)) {
                                            $user_id  = $row[0];
                                            $username = $row[1];
                                            
                                            $img_path = '../../style/img/default-pfp.png';
                                            if (file_exists($upload_dir.$user_id.'.png'))
                                                $img_path = $upload_dir.$user_id.'.png';
                                            echo '
                                            <div class="friends-box-elem" style="width:400px">
                                                <a href="../profile?id='.$user_id.'">
                                                    <div class="friends-box-elem-link">
                                                        <div class="friends-pfp-container">
                                                            <img src="'.$img_path.'" />
                                                        </div>
                                                        <div class="friends-name-container">
                                                            '.$username.'
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                            ';
                                        }
                                    ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <!-- Challenges -->
                            <div id="challenges" style="height:700px;overflow-y:auto;float:right; display:block;">
                                <div id="challenges-flex">
                                    <?php
                                        // $query = 
                                        $stid = oci_parse($conn, "
                                            SELECT w.id, w.nazwa, k.login, w.cel, w.jednostka_celu, w.czas_rozpoczecia, w.czas_ukonczenia 
                                            FROM WYZWANIE w JOIN KONTO k ON w.TWORCA = k.id 
                                            WHERE NAZWA LIKE '%$_POST[keyword]%' AND CZY_PRYWATNE = 0
                                        ");

                                        oci_execute($stid);

                                        while ($row = oci_fetch_array($stid, OCI_BOTH  + OCI_RETURN_NULLS)) {
                                            $challenge_id   = $row[0];
                                            $challenge_name = $row[1];
                                            $creator_name   = $row[2];
                                            $goal           = $row[3];
                                            $unit           = $row[4];
                                            $start_time     = $row[5];
                                            $end_time       = $row[6];

                                            echo "
                                                <div class='challenges-box-elem'>
                                                    <div class='challenges-box-progress-bar' style='width: 0%;'></div>
                                                    <div class='challenges-box-name'><a href='../challenge/?id=$challenge_id'>$challenge_name created by $creator_name.</a></div>
                                                </div>
                                            ";
                                        }
                                    ?>
                                </div>
                            </div>
                        </td></tr>
                    </table>
                </div>
            </div>        
        </div>
    </body>
</html>
<?php oci_close($conn); ?>
