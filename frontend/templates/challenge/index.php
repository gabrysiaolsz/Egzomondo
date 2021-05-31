<?php
    include '../_common/redirect_to_login.php';
    include '../_common/connect_to_db.php';

    // Display date differently
    $stid = oci_parse($conn, "alter SESSION set NLS_DATE_FORMAT = 'DD-MM-YYYY'");
    oci_execute($stid);

    $error = false;
    if (!isset($_GET['id'])) {
        $error = true;
        echo "No id provided!\n";
        return;
    }
    $id = $_GET['id'];
    $stid = oci_parse($conn,
        "SELECT w.nazwa, k.id, k.login, w.cel, w.jednostka_celu, w.id_aktywnosci, w.czas_rozpoczecia, w.czas_ukonczenia
        FROM Wyzwanie w, Konto k
        WHERE w.tworca=k.id AND w.id=".$id);
    oci_execute($stid);
    $row = oci_fetch_array($stid, OCI_BOTH + OCI_RETURN_NULLS);
    [$challenge_name, $challenge_author_id, $challenge_author, 
        $challenge_goal, $challenge_unit, $activity_id, 
        $start_date, $end_date] = $row;
    $width = 49;
    if($_SESSION['id'] != $challenge_author_id) $width = 100;
?>
<html>
    <head>
        <title><?php echo $challenge_name; ?></title>
        <link rel="shortcut icon" href="../../style/img/logo_icon.png">
        <link rel="stylesheet" type="text/css" href="../../style/css/global-style.css" />
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar-style.css" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <script src="script.js"></script>
        <script src="https://kit.fontawesome.com/67c66657c7.js"></script>
    </head>
    <body>
        <?php include '../_common/navbar.php'; ?>
        <div id="below-navbar">
            <div id="container">
                <!-- Challenge name -->
                <div id="challenge-name">
                    <div id="challenge-name-field">
                        <?php echo $challenge_name; ?>
                    </div>
                </div>
                <!-- Author -->
                <div id="author-name">
                    <a href="#">
                        <div id="author-name-field">
                            by <?php echo $challenge_author; ?>
                        </div>
                    </a>
                </div>
                <!-- Beginning and end -->
                <div id="time">
                    <div id="time-field">
                        <?php echo $start_date." - ".$end_date; ?>
                    </div>
                </div>
                <!-- Goal -->
                <div id="goal">
                    <div id="goal-field">
                        <div id="discipline-img-container">
                            <?php
                                echo '<img src="../../style/img/disciplines/'.$activity_id.'.png" />';
                            ?>
                        </div>
                        <div id="goal-field-text">
                            <?php echo $challenge_goal." ".$challenge_unit; ?>
                        </div>
                    </div>
                </div>
                <!-- Users -->
                <div id="users">
                    <div class="users-side" style="width: <?php echo $width;?>%;">
                        <div id="users-flexbox">
                            <?php
                                $stid = oci_parse($conn, "
                                    SELECT LOGIN, ID
                                    FROM KONTO K, UCZESTNICY_WYZWANIA W
                                    WHERE K.ID = W.uczestnik  AND W.wyzwanie = $id
                                ");
                                $err = oci_execute($stid);
                                if(!$err){
                                    $e = oci_error($pars);
                                    var_dump($e);
                                }
                                $participating = false;
                                
                                while ($row = oci_fetch_array($stid, OCI_BOTH  + OCI_RETURN_NULLS)) {
                                    
                                    $login = $row[0];
                                    $id_uzytkownika = $row[1];
                                    if($id_uzytkownika == $_SESSION['id']) $participating = true;
                                    $stid2 = oci_parse($conn, "
                                        SELECT SUM(A.ILOSC) odleglosc, SUM(A.CZAS_TRWANIA) czas, SUM(A.kcal)
                                        FROM AKTYWNOSC A, WYZWANIE W
                                        WHERE A.ID = $id_uzytkownika AND W.id = $id 
                                        AND A.DATA_ROZPOCZECIA <= W.CZAS_UKONCZENIA AND A.DATA_ROZPOCZECIA>= W.CZAS_ROZPOCZECIA
                                        GROUP BY A.ID
                                    ");
                                    $err = oci_execute($stid2);
                                    $exists = ($row2 = oci_fetch_array($stid2, OCI_BOTH  + OCI_RETURN_NULLS));
                                    if ($challenge_unit == 'km' && $exists) 
                                        $progress = $row2[0] / $challenge_goal * 100;
                                    else if ($challenge_unit == 'min' && $exists) 
                                        $progress = $row2[1] / $challenge_goal * 100;
                                    else if ($challenge_unit == 'kcal' && $exists) 
                                        $progress = $row2[2] / $challenge_goal * 100;
                                    else 
                                        $progress = 0;
                                    
                                    oci_free_statement($stid2);
                                    echo '
                                        <div class="users-box-elem">
                                            <a href="../profile/?id='.$id_uzytkownika.'">
                                                <div class="users-box-elem-link">
                                                    <div style="width: '.$progress.'%" class="users-box-elem-progress-bar">
                                                        <div class="pfp-container">
                                                            <img src="../../style/img/default-pfp.png" />
                                                        </div>
                                                        <div class="user-name-field">
                                                            '.$row[0].'
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    ';
                                }

                                oci_free_statement($stid);
                            ?>
                        </div>
                    </div>
                    <?php
                        if($_SESSION['id'] == $challenge_author_id){
                            echo'<div class="users-side" style="width: 49%;">
                                <div id="users-flexbox">';
                                

                                    $stid = oci_parse($conn, "
                                        SELECT K.LOGIN, K.id
                                        FROM KONTO K, ZNAJOMI Z
                                        WHERE (K.id = Z.znajomy2 AND Z.znajomy1 = $id_uzytkownika) OR (K.id = Z.znajomy1 AND Z.znajomy2 = $id_uzytkownika)
                                        MINUS
                                        SELECT K.LOGIN, K.id
                                        FROM KONTO K, UCZESTNICY_WYZWANIA W
                                        WHERE K.id = W.uczestnik AND W.wyzwanie = $id
                                        MINUS 
                                        SELECT K.LOGIN, K.id
                                        FROM KONTO K, ZAPROSZENIE_DO_WYZWANIA W
                                        WHERE K.id = W.zaproszony AND W.wyzwanie = $id                              
                                    ");

                                    $err = oci_execute($stid);

                                    if (!$err) {
                                        $e = oci_error($pars);
                                        var_dump($e);
                                        return;
                                    }

                                    while ($row = oci_fetch_array($stid, OCI_BOTH  + OCI_RETURN_NULLS)) {
                                        

                                        echo '
                                            <div class="users-box-elem">
                                                <a href="invite.php?id_zapraszanego='.$row[1].'&id_wyzwania='.$id.'">
                                                    <div class="users-box-elem-link">
                                                        <div style="width: 0%" class="users-box-elem-progress-bar">
                                                            <div class="pfp-container">
                                                                <img src="../../style/img/green_plus.png" />
                                                            </div>
                                                            <div class="user-name-field">
                                                                '.$row[0].'
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        ';
                                    }
                            echo '    
                                </div>
                            </div>';
                        }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>