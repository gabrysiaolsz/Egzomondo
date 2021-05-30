<?php
    include '../_common/connect_to_db.php';
    include '../_common/redirect_to_login.php';

    if (!isset($_GET['id'])) {
        header('location:../profile');
    }

    $i = "DELETE FROM ZNAJOMI WHERE ZNAJOMY1 = (SELECT ID FROM KONTO WHERE LOGIN = '$_SESSION[login]') AND ZNAJOMY2 = $_GET[id]";
    $insert = oci_parse($conn, $i);

    $rc = oci_execute($insert);
    if (!$rc) {
        $e = oci_error($insert);
        var_dump($e);
    }
    oci_commit($conn);
    header("location:../profile/?id=$_GET[id]");
?>