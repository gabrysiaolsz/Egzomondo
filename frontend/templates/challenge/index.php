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
                    <div id="users-flexbox">
                        <?php
                            $stid = oci_parse($conn, "
                                SELECT LOGIN, NAZWA, SUM(ILOSC) odleglosc, SUM(CZAS_TRWANIA) czas
                                FROM (
                                SELECT LOGIN,T.NAZWA,ILOSC,CZAS_TRWANIA
                                    from KONTO K
                                    INNER JOIN AKTYWNOSC A on K.ID = A.ID
                                    INNER JOIN TYP_AKTYWNOSCI T on t.ID = A.ID_RODZAJU
                                    INNER JOIN UCZESTNICY_WYZWANIA UW on UW.UCZESTNIK = K.ID
                                    INNER JOIN WYZWANIE W on UW.WYZWANIE = W.ID AND A.ID_RODZAJU = W.ID_AKTYWNOSCI
                                    WHERE W.ID = $id
                                    AND A.DATA_ROZPOCZECIA <= W.CZAS_UKONCZENIA AND A.DATA_ROZPOCZECIA>= W.CZAS_ROZPOCZECIA
                                ) GROUP BY LOGIN, NAZWA
                            ");
                            oci_execute($stid);
                            
                            while ($row = oci_fetch_array($stid, OCI_BOTH  + OCI_RETURN_NULLS)) {
                                echo "kupa";
                                if ($challenge_unit == 'km') {
                                    $progress = $row[2] / $challenge_goal * 100;
                                }
                                else {
                                    $progress = $row[3] / $challenge_goal * 100;
                                }
                                $login = $row[0];
                                $stid2 = oci_parse($conn, "SELECT id FROM Konto WHERE login='$login'");
                                oci_execute($stid2);
                                if (($row2=oci_fetch_row($stid2)) != false) {
                                    $id_uzytkownika = $row2[0];
                                } else {
                                    $error = true;
                                    #echo "You need to be signed in $login;\n";
                                }
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

                            $login = $_SESSION['login'];
                            $stid = oci_parse($conn, "SELECT id FROM Konto WHERE login='$login'");
                            oci_execute($stid);
                            if (($row=oci_fetch_row($stid)) != false) {
                                $id_uzytkownika = $row[0];
                            } else {
                                echo "You need to be signed in $login;\n";
                            }
                            oci_free_statement($stid);

                            echo '
                                <div class="users-box-elem">
                                    <div class="users-box-elem-link">
                                        <div style="width: 0%" class="users-box-elem-progress-bar">
                                            <div class="pfp-container">
                                                <img src="../../style/img/default-pfp.png" />
                                            </div>
                                            <div class="user-name-field">
                                                Invite your friends to the challenge
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ';

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

                            oci_close($conn);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
