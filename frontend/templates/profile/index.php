<?php
    include '../_common/redirect_to_login.php';
    include '../_common/connect_to_db.php';
    $upload_dir = '../../uploads/profilepic/';

    // Preparing information about the user
    if (!isset($_GET['id'])) {
        $not_my_acc = "False";
        $login = $_SESSION['login'];
        $stid = oci_parse($conn, "SELECT id, waga, wzrost, plec FROM Konto WHERE login='".$login."'");
        oci_execute($stid);
        [$id, $weight, $height, $sex] = oci_fetch_array($stid, OCI_BOTH + OCI_RETURN_NULLS);
    }
    else {
        $id = $_GET['id'];
        $stid = oci_parse($conn, "SELECT login, waga, wzrost, plec FROM Konto WHERE id='".$id."'");
        oci_execute($stid);
        [$login, $weight, $height, $sex] = oci_fetch_array($stid, OCI_BOTH + OCI_RETURN_NULLS);

        if (strcmp($login, $_SESSION['login']) == 0) {
            $not_my_acc = "False";
        } else {
            $not_my_acc = "True";
            # Check if is my friend.

            $q = "SELECT * FROM ZNAJOMI WHERE ZNAJOMY1 = (SELECT id FROM KONTO WHERE login = '$_SESSION[login]') AND ZNAJOMY2 = $_GET[id]";
            $query = oci_parse($conn, $q);
            oci_execute($query);
            oci_fetch($query);

            if (oci_num_rows($query) < 1) {
                // S/he's not.
                $not_my_acc = "No";
            } else {
                // S/he is.
                $not_my_acc = "Yes";
            }
        }
    }
    oci_free_statement($stid);
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
            <div id="container">
                <!-- Profile picture and name -->
                <div id="pfp-and-name">
                    <div id="pfp-container">
                        <?php if (file_exists($upload_dir.$id.'.png')) { ?>
                            <img src="<?php echo $upload_dir.$id; ?>.png" />
                        <?php } else { ?>
                            <img src="../../style/img/default-pfp.png" />
                        <?php } ?>
                    </div>
                    <div id="name-container">
                        <?php echo $login; ?>
                    </div>
                    <?php if (strcmp($not_my_acc, "False") != 0) {?>
                    <div id="add_remove_friend">
                        <?php if (strcmp($not_my_acc, "Yes") == 0) { ?>
                            <a href="./remove_friend.php?id=<?php echo $id;?>"><i class="fas fa-minus-circle fa-3x" style="margin-left:20px;" title="Remove friend"></i></a>
                        <?php } else { ?>
                            <a href="./add_friend.php?id=<?php echo $id;?>"><i class="fas fa-plus-circle fa-3x" style="margin-left:20px;" title="Add friend"></i></a>
                        <?php } ?>
                    </div>
                    <?php } ?>
                </div>
                <!-- User info -->
                <div id="user-info">
                    <div id="user-info-box">
                        <div>
                            <?php
                                $row = oci_fetch_array($stid, OCI_BOTH  + OCI_RETURN_NULLS);
                                echo '<i class="fas fa-weight"></i>: '.$weight.' kg<br />';
                                echo '<i class="fas fa-arrows-alt-v"></i>: '.$height.' cm<br />';
                                echo '<i class="fas fa-venus-mars"></i>: ';
                                if ($sex == 0) echo 'Female';
                                else echo 'Male';
                                echo '<br />';
                                if ($_SESSION['login'] === $login)
                                    echo '<button onclick="location.href=\'editprofile.php\'">Edit</button>';
                            ?>
                        </div>
                    </div>
                </div>
                <!-- Stats -->
                <div id="stats">
                    <div id="stats-flexbox">
                        <?php
                            $stid = oci_parse($conn, "
                                SELECT T.ID, SUM(A.ILOSC) from TYP_AKTYWNOSCI T
                                INNER JOIN AKTYWNOSC A on T.ID = A.ID_RODZAJU AND A.ID = ".$id.
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
                                SELECT K.ID, K.LOGIN from KONTO K LEFT JOIN
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
                                    WHERE KONTO.LOGIN = '".$login."'
                                ) T
                                ON K.ID = T.ZNAJOMY2 WHERE T.LOGIN IS NOT NULL
                            ");
                            oci_execute($stid);

                            while ($row = oci_fetch_array($stid, OCI_BOTH  + OCI_RETURN_NULLS)) {
                                $img_path = '../../style/img/default-pfp.png';
                                if (file_exists($upload_dir.$row[0].'.png'))
                                    $img_path = $upload_dir.$row[0].'.png';
                                echo '
                                    <div class="friends-box-elem">
                                        <a href="../profile?id='.$row[0].'">
                                            <div class="friends-box-elem-link">
                                                <div class="friends-pfp-container">
                                                    <img src="'.$img_path.'" />
                                                </div>
                                                <div class="friends-name-container">
                                                    '.$row[1].'
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
                            $stid = oci_parse($conn, "
                                SELECT W.id, W.nazwa, W.czas_rozpoczecia, W.czas_ukonczenia, W.cel, W.jednostka_celu, W.id_aktywnosci
                                FROM UCZESTNICY_WYZWANIA UW
                                INNER JOIN KONTO K on K.ID = UW.UCZESTNIK AND K.ID = $id
                                INNER JOIN WYZWANIE W on UW.WYZWANIE = W.ID
                                ORDER BY W.CZAS_UKONCZENIA DESC
                            ");
                            oci_execute($stid);

                            while ($row = oci_fetch_array($stid, OCI_BOTH  + OCI_RETURN_NULLS)) {
                                
                                [$challenge_id, $challenge_name, $start_time, $end_time, $goal, $unit, $act_type] = $row;
                                $query = "
                                    SELECT sum(ilosc), sum(czas_trwania), sum(kcal) FROM Aktywnosc
                                    WHERE id = $id AND id_rodzaju = $act_type AND '$start_time' <= data_rozpoczecia AND data_rozpoczecia <= '$end_time'
                                    GROUP BY id
                                ";

                                $stid_loop = oci_parse($conn, $query); 
                                oci_execute($stid_loop);
                                $row_loop = oci_fetch_array($stid_loop, OCI_BOTH + OCI_RETURN_NULLS);
                                if($row_loop != false){
                                    $distance = $row_loop[0];
                                    $time     = $row_loop[1];
                                    $kcal     = $row_loop[2];
                                } else {
                                    $distance = 0;
                                    $time = 0;
                                    $kcal=0;
                                }
                                if ($unit == "km") {
                                    $progress = $distance / $goal * 100;
                                } else if($unit=='min'){
                                    $progress = $time / $goal * 100;
                                }else{
                                    $progress = $kcal / $goal * 100;
                                }
                                if ($progress > 100) $progress = 100;

                                echo '
                                    <div class="challenges-box-elem">
                                        <a href="../challenge/?id='.$challenge_id.'">
                                            <div class="challenges-box-elem-link">
                                                <div style="width: '.$progress.'%" class="challenges-box-progress-bar">
                                                    <div class="challenges-box-name">
                                                        '.$challenge_name.'
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
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
