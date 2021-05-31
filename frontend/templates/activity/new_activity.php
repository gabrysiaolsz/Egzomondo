<?php
    include '../_common/redirect_to_login.php';
    include '../_common/connect_to_db.php';

    $error = false;

    $login = $_SESSION['login'];
    $id_uzytkownika = $_SESSION['id'];

    $czas_rozpoczecia = date('Y-m-d');
    $czas_rozpoczecia = date("Y-m-d", strtotime(str_replace('-', '/', $czas_rozpoczecia)));

    if (!isset($_POST['duration']) || !isset($_POST['distance']) || !isset($_POST['activity'])) {
        $error = true;
        echo "Invalid request!\n";
    }
    $czas = $_POST['duration'];
    $ilosc = $_POST['distance'];
    $id_aktywnosci = $_POST['activity'];

    $pars = oci_parse($conn, "SELECT id FROM Typ_aktywnosci WHERE nazwa = '$id_aktywnosci'");
    oci_execute($pars);
    if (($row=oci_fetch_row($pars)) != false) {
        $id_aktywnosci = $row[0];
    } else if (!$error) {
        $error = true;
        echo "No such activity as $id_aktywnosci\n";
    }
    oci_free_statement($pars);

    switch($id_aktywnosci){
        case 1:
            $mnoznik = $ilosc/$czas/60*2/3;
            break;
        
        case 3:
            $mnoznik = 8;
            break;
        default:
            $mnoznik = 7;
            break;
    }

    $pars = oci_parse($conn, "SELECT waga FROM Konto WHERE id = $id_uzytkownika");
    oci_execute($pars);
    if (($row=oci_fetch_row($pars)) != false) {
        $waga = $row[0];
    } else if (!$error) {
        $error = true;
        echo "No such activity as $id_aktywnosci\n";
    }
    oci_free_statement($pars);

    $kcal = $czas * $mnoznik *3.5* $waga/200;

    if (!$error) {
        $query = "INSERT INTO Aktywnosc VALUES ($id_uzytkownika, $id_aktywnosci, $ilosc, TO_DATE('$czas_rozpoczecia', 'YYYY-MM-DD'), $czas, $kcal)";
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
            echo 'Adding activity was unsuccessful</body></html>';
            return;
        }
        oci_free_statement($pars);
        oci_commit($conn);
        if ($err)
            header('Location: ../profile');
    }

    oci_close($conn);
?>
