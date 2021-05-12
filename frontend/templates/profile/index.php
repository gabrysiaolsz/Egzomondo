<?php
    $user = 'wz418498';
    $password = 'IO2021';
    $db = '//labora.mimuw.edu.pl/LABS';
    $conn = oci_connect($user, $password, $db);
    $redirect_to_login = substr($_SERVER["REQUEST_URI"], 0, -8)."log-in";
    $redirect_to_new_challenge = substr($_SERVER["REQUEST_URI"], 0, -8)."new_challenge";
    $upload_dir = '../../uploads/profilepic/';

    if (!$conn) {
        echo "oci_connect failed\n";
        $e = oci_error();
        echo $e['message'];
    }

    session_start();

    # If you're not logged in, redirect to login page.
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != TRUE) {
        header('location:'.$redirect_to_login);
    }
    
    $stid = oci_parse($conn, "SELECT id, waga, wzrost, plec FROM Konto WHERE login='".$_SESSION['login']."'");
    oci_execute($stid);
    $row = oci_fetch_array($stid, OCI_BOTH + OCI_RETURN_NULLS);
    [$id, $weight, $height, $sex] = $row;
    
    oci_free_statement($stid);
?>
<html>
    <head>
        <title>
            Egzomondo - <?php echo $_SESSION['login']; ?>
        </title>
        <link rel="shortcut icon" href="../../style/img/logo_icon.png">

        <link rel="stylesheet" type="text/css" href="../../style/css/global-style.css" />
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar-style.css" />
        <link rel="stylesheet" type="text/css" href="style.css" />

        <script src="buttons.js"></script>
        <script src="https://kit.fontawesome.com/67c66657c7.js"></script>
    </head>
    <body>
        <!-- Navbar -->
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
                    <li><a href="#">My profile</a></li>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About us</a></li>
                    <li><a href="../new_challenge">New Challenge</a></li>
                    <li><a href="../activity">New Activity</a></li>
                </ol>
                <div class="search_box">
                    <input type="search" placeholder="Search">
                    <a href="#"><span class="fa fa-search"></span></a>
                </div>
            </nav>
        </div>
        <div id="below-navbar">
            <div id="container">
                <!-- Profile picture and name -->
                <div id="pfp-and-name">
                    <div id="pfp-container">
                        <?php if (file_exists(''.$upload_dir.''.$id.'.png')) { ?>
                            <img src="../../uploads/profilepic/<?php echo $id;?>.png" />
                        <?php } else { ?>
                            <img src="../../style/img/default-pfp.png" />
                        <?php } ?>
                    </div>
                    <div id="name-container">
                        <?php echo $_SESSION['login']; ?>
                    </div>
                </div>
                <!-- User info -->
                <div id="user-info">
                    <div id="user-info-box">
                        <div id="user-info-display">

                            <?php
                                $row = oci_fetch_array($stid, OCI_BOTH  + OCI_RETURN_NULLS);
                                echo '<i class="fas fa-weight"></i>: '.$weight.' kg<br />';
                                echo '<i class="fas fa-arrows-alt-v"></i>: '.$height.' cm<br />';
                                echo '<i class="fas fa-venus-mars"></i>: ';
                                if ($sex == 0) echo 'Female';
                                else echo 'Male';
                                echo '<br />';
                            ?>
                            <button onclick="location.href='editprofile.php'">Edit</button>
                        </div>
                        <div id="user-info-edit" style="display: none;">
                            <input type="number" placeholder="Weight (kg)" min="0" step="1" value="<?php echo $weight; ?>" /><br />
                            <input type="number" placeholder="Height (cm)" min="0" step="1" value="<?php echo $height; ?>" /><br />
                            <input type="radio" id="male" name="sex" value="male" <?php if ($sex == 1) echo 'checked="checked"'; ?> />
                            <label for="male">Male</label>
                            <input type="radio" id="female" name="sex" value="female" <?php if ($sex == 0) echo 'checked="checked"'; ?> />
                            <label for="female">Female</label><br />
                            <button type="submit">Save</button>
                            <button onclick="cancelBtn()">Cancel</button>
                        </div>
                    </div>
                </div>
                <!-- Stats -->
                <div id="stats">
                    <div id="stats-flexbox">
                        <?php
                            $login = $_SESSION['login'];
                            $stid = oci_parse($conn, 
                                    "SELECT id FROM Konto WHERE login='$login'");
                            oci_execute($stid);
                            if(($row=oci_fetch_row($stid)) != false){
                                $id_uzytkownika = $row[0];
                                #echo "Your id is:$id_uzytkownika;";
                            }else{
                                $error = true;
                                echo "You need to be signed in $login;\n";
                            }
                            $stid = oci_parse($conn, "
                                SELECT T.ID, SUM(A.ILOSC) from TYP_AKTYWNOSCI T
                                INNER JOIN AKTYWNOSC A on T.ID = A.ID_RODZAJU AND A.ID = ".$id_uzytkownika.
                                " GROUP BY T.ID ORDER BY T.ID
                            ");
                            oci_execute($stid);

                            while ($row = oci_fetch_array($stid, OCI_BOTH  + OCI_RETURN_NULLS)) {
                                echo '
                                    <div class="stats-box-elem">
                                        <div class="discipline-img">
                                            <img src="../../style/img/disciplines/'.$row[0].'.png" />
                                        </div>
                                        <div class="discipline-value">'.$row[1].' km</div>
                                    </div>
                                ';
                            }
                        ?>
                    </div>
                </div>
                <!-- Friends -->
                <div id="friends">
                    <div id="friends-flexbox">
                        <?php
                            $stid = oci_parse($conn, "
                                SELECT K.LOGIN from KONTO K LEFt JOIN
                                (
                                    SELECT *
                                    from KONTO
                                            LEFT JOIN
                                        (SELECT *
                                            from ZNAJOMI
                                            UNION
                                            SELECT ZNAJOMY2, ZNAJOMY1
                                            from ZNAJOMI) P
                                        ON KONTO.id = P.ZNAJOMY1
                                    WHERE KONTO.LOGIN = '".$_SESSION['login']."'
                                ) T
                                ON K.ID = T.ZNAJOMY2 WHERE T.LOGIN is not null
                            ");
                            oci_execute($stid);

                            while ($row = oci_fetch_array($stid, OCI_BOTH  + OCI_RETURN_NULLS)) {
                                echo '
                                    <div class="friends-box-elem">
                                        <a href="#">
                                            <div class="friends-box-elem-link">
                                                <div class="friends-pfp-container">
                                                    <img src="../../style/img/default-pfp.png" />
                                                </div>
                                                <div class="friends-name-container">
                                                    '.$row[0].'
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                ';
                            }
                        ?>
                    </div>
                </div>
                <!-- Challenges -->
                <div id="challenges">
                    <div id="challenges-flex">
                        <?php
                            $login = $_SESSION['login'];
                            $stid = oci_parse($conn, 
                                    "SELECT id FROM Konto WHERE login='$login'");
                            oci_execute($stid);
                            if(($row=oci_fetch_row($stid)) != false){
                                $id_uzytkownika = $row[0];
                                #echo "Your id is:$id_uzytkownika;";
                            }else{
                                $error = true;
                                echo "You need to be signed in $login;\n";
                            }
                            #echo $_SESSION[id]." ".$id_uzytkownika;
                            $stid = oci_parse($conn, "
                                SELECT W.id, W.nazwa, W.czas_rozpoczecia, W.czas_ukonczenia, W.cel, W.jednostka_celu, W.id_aktywnosci
                                FROM UCZESTNICY_WYZWANIA UW
                                INNER JOIN KONTO K on K.ID = UW.UCZESTNIK AND K.ID = $id_uzytkownika
                                INNER JOIN WYZWANIE W on UW.WYZWANIE = W.ID
                                ORDER BY W.CZAS_UKONCZENIA DESC
                            ");
                            oci_execute($stid);

                            while ($row = oci_fetch_array($stid, OCI_BOTH  + OCI_RETURN_NULLS)) {
                                $challenge_id   = $row[0];
                                $challenge_name = $row[1];
                                $start_time     = $row[2];
                                $end_time       = $row[3];
                                $goal           = $row[4];
                                $unit           = $row[5];
                                $act_type       = $row[6];
                                #echo "$start_time do $end_time<br>";
                                
                                $query = "
                                    SELECT sum(ilosc), sum(czas_trwania) FROM Aktywnosc
                                    WHERE id = $id_uzytkownika AND id_rodzaju = $act_type AND '$start_time' <= data_rozpoczecia AND data_rozpoczecia <= '$end_time'
                                    GROUP BY id
                                ";

                                #echo $query;
                                $stid_loop = oci_parse($conn, $query); 
                                oci_execute($stid_loop);
                                $row_loop = oci_fetch_array($stid_loop, OCI_BOTH + OCI_RETURN_NULLS);
                                if($row_loop != false){
                                    $distance = $row_loop[0];
                                    $time     = $row_loop[1];
                                } else {
                                    $distance = 0;
                                    $time = 0;
                                }
                                
                                if($unit == "km"){
                                    $progress = $distance/$goal*100;
                                }else{
                                    $progress = $time/$goal*100;
                                }
                                #echo "progres:$progress% <br>";
                                if($progress > 100) $progress = 100;

                                echo '
                                    <div class="challenges-box-elem">
                                        <div class="challenges-box-progress-bar" style="width: '.$progress.'%;"></div>
                                        <div class="challenges-box-name">'.$row[1].'</div>
                                    </div>
                                ';
                            }
                        ?>
                    </div>
                </div>
            </div>        
        </div>
    </body>
</html>
<?php oci_close($conn); ?>
