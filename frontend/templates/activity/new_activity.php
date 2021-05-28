<?php
    include '../_common/redirect_to_login.php';
    include '../_common/connect_to_db.php';

    $error = false;

    $login = $_SESSION['login'];
    $id_uzytkownika = $_SESSION['id'];

    $czas_rozpoczecia = date('Y-m-d');
    $czas_rozpoczecia = date("Y-m-d", strtotime(str_replace('-', '/', $czas_rozpoczecia)));

    if (!isset($_POST['duration']) || !isset($_POST['distance']) || !isset($_POST['activity']) {
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

    if (!$error) {
        $query = "INSERT INTO Aktywnosc VALUES ($id_uzytkownika, $id_aktywnosci, $ilosc, TO_DATE('$czas_rozpoczecia', 'YYYY-MM-DD'), $czas)";
        $pars = oci_parse($conn, $query);
        $err = oci_execute($pars);
        if (!$err) {
            $e = oci_error($pars);
            var_dump($e);
        }
        oci_free_statement($pars);
        oci_commit($conn);
        if ($err)
            header('Location: ../profile');
    }

    oci_close($conn);
?>
