<?php
    include '../_common/redirect_to_login.php';
    include '../_common/connect_to_db.php';

    $error = false;
    $nazwa = $_REQUEST['challenge_name'];
    if(!$nazwa){
        $error = true;
        echo "Challenge name cannot be empty\n";
    }
    $nazwa = filter_var($nazwa,FILTER_SANITIZE_EMAIL);
    $czas_rozpoczecia = $_REQUEST['start_time'];
    echo 
    $czas_rozpoczecia = date("Y-m-d", strtotime(str_replace('-', '/', $czas_rozpoczecia)));

    $czas_ukonczenia = $_REQUEST['end_time'];
    $czas_ukonczenia = date("Y-m-d", strtotime(str_replace('-', '/', $czas_ukonczenia)));
    $login = $_SESSION['login'];
    $stid = oci_parse($conn, "SELECT id FROM Konto WHERE login='$login'");
    oci_execute($stid);
    if (($row=oci_fetch_row($stid)) != false) {
        $id_uzytkownika = $row[0];
    } else {
        $error = true;
        echo "You need to be signed in $login;\n";
    }
    oci_free_statement($stid);

    $cel = $_REQUEST['objective'];
    if ($cel <= 0) {
        $error = true;
        echo "Objective has to be greater than zero\n";
    }

    $jednostka_celu = $_REQUEST['objective_unit'];

    $id_aktywnosci = $_REQUEST['activity_type'];
    $query = "SELECT id FROM Typ_aktywnosci WHERE nazwa = '$id_aktywnosci'";
    $pars = oci_parse($conn, $query);
    oci_execute($pars);
    if (($row=oci_fetch_row($pars)) != false) {
        $id_aktywnosci = $row[0];
    } else if ($id_aktywnosci === "wszystkie") {
        $id_aktywnosci = null;
    } else {
        $error = true;
        echo "No such activity as $id_aktywnosci\n";
    }
    oci_free_statement($pars);
    if (!$error) {
        $query = "INSERT INTO WYZWANIE VALUES (null, '$nazwa', $id_uzytkownika, TO_DATE('$czas_rozpoczecia', 'YYYY-MM-DD'), TO_DATE('$czas_ukonczenia', 'YYYY-MM-DD'), 0, $cel, '$jednostka_celu', 0, $id_aktywnosci)";
        $pars = oci_parse($conn, $query);
        $err = oci_execute($pars);
        if (!$err) {
            echo '<html>
            <head>
                <link rel="shortcut icon" href="../../style/img/logo_icon.png">
                <link rel="stylesheet" type="text/css" href="../../style/css/global-style.css" />
                <link rel="stylesheet" type="text/css" href="../../style/css/navbar-style.css" />
                <link rel="stylesheet" type="text/css" href="style.css">
                <script src="script.js"></script>
                <script src="https://kit.fontawesome.com/67c66657c7.js"></script>
            </head>
            <body>';
            include '../_common/navbar.php';
            echo 'Creating new challenge was unsuccessful</body></html>';
            return;
        }
        oci_commit($conn);
        oci_free_statement($pars);

        $query = "SELECT id FROM WYZWANIE WHERE nazwa='$nazwa' AND tworca=$id_uzytkownika AND czas_rozpoczecia = TO_DATE('$czas_rozpoczecia', 'YYYY-MM-DD') AND czas_ukonczenia=TO_DATE('$czas_ukonczenia', 'YYYY-MM-DD') AND id_aktywnosci=$id_aktywnosci AND jednostka_celu='$jednostka_celu' AND cel=$cel";
        $pars = oci_parse($conn, $query);
        $err &= oci_execute($pars);
        $row = oci_fetch_array($pars, OCI_BOTH + OCI_RETURN_NULLS);
        $id_wyzwania = $row[0];
        if (!$err) {
            echo '<html>
            <head>
                <link rel="shortcut icon" href="../../style/img/logo_icon.png">
                <link rel="stylesheet" type="text/css" href="../../style/css/global-style.css" />
                <link rel="stylesheet" type="text/css" href="../../style/css/navbar-style.css" />
                <link rel="stylesheet" type="text/css" href="style.css">
                <script src="script.js"></script>
                <script src="https://kit.fontawesome.com/67c66657c7.js"></script>
            </head>
            <body>';
            include '../_common/navbar.php';
            echo 'Creating new challenge was unsuccessful</body></html>';
            return;
        }
        oci_commit($conn);
        oci_free_statement($pars);

        $query = "INSERT INTO UCZESTNICY_WYZWANIA VALUES ($id_wyzwania, $id_uzytkownika)";
        $pars = oci_parse($conn, $query);
        $err &= oci_execute($pars);
        if (!$err) {
            echo '<html>
            <head>
                <link rel="shortcut icon" href="../../style/img/logo_icon.png">
                <link rel="stylesheet" type="text/css" href="../../style/css/global-style.css" />
                <link rel="stylesheet" type="text/css" href="../../style/css/navbar-style.css" />
                <link rel="stylesheet" type="text/css" href="style.css">
                <script src="script.js"></script>
                <script src="https://kit.fontawesome.com/67c66657c7.js"></script>
            </head>
            <body>';
            include '../_common/navbar.php';
            echo 'Joining new challenge was unsuccessful</body></html>';
            return;
        }
        oci_commit($conn);
        oci_free_statement($pars);

        if ($err) {
            $redirect_to_profile = substr($_SERVER["REQUEST_URI"], 0, -16)."profile";
            header('Location: ../profile');
        }
    }

    oci_close($conn);
?>
