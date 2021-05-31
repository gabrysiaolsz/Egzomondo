<?php
    include '../_common/redirect_to_login.php';
    include '../_common/connect_to_db.php';

    // Display date differently
    $stid = oci_parse($conn, "alter SESSION set NLS_DATE_FORMAT = 'DD-MM-YYYY'");
    oci_execute($stid);
    oci_free_statement($stid);

    $error = false;
    if (!isset($_GET['id_wyzwania'])) {
        $error = true;
        echo "No id_wyzwania provided!\n";

    }
    $id_wyzwania = $_GET['id_wyzwania'];
    
    $query = "INSERT INTO UCZESTNICY_WYZWANIA VALUES ($id_wyzwania, ".$_SESSION['id'].")";
    $pars = oci_parse($conn, $query);
    $err = oci_execute($pars);
    if (!$err) {
        echo "kupa ";
        echo $query;
        $e = oci_error($pars);

        var_dump($e);
        echo " kupa";
    }
    oci_commit($conn);
    oci_free_statement($pars);
    header("Location:./?id=$id_wyzwania");
?>