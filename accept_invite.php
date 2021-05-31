<?php
    session_start();
    $redirect_path = './frontend/templates/log-in';
    if (!isset($_SESSION['login']))
        header('location:'.$redirect_path);
    $user = 'wz418498';
    $password = 'IO2021';
    $db = '//labora.mimuw.edu.pl/LABS';
    $conn = oci_connect($user, $password, $db);

    if (!$conn) {
        echo "oci_connect failed\n";
        $e = oci_error();
        echo $e['message'];
        return;
    }

    $id = $_SESSION['id'];

    $error = false;

    if (!isset($_GET['challenge'])) {
        $error = true;
        echo "No challenge id provided!\n";
        return;
    }
    $id_wyzwania = $_GET['challenge'];

    if (!isset($_GET['accept'])) {
        $error = true;
        echo "No accept provided!\n";
        return;
    }
    $accept = $_GET['accept'];

    if (!isset($_GET['inviter'])) {
        $error = true;
        echo "No inviter id provided!\n";
        return;
    }
    $inviter = $_GET['inviter'];
    echo "$id_wyzwania, $inviter, $id, $accept";
    if($id_wyzwania == 0){
        $stid = oci_parse($conn, "
                            DELETE
                            FROM ZAPROSZENIA_DO_ZNAJOMYCH
                            WHERE zaproszony = $id AND zapraszajacy = $inviter
                        ");
        $err = oci_execute($stid);

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
            echo 'Accepting invite was unsuccessful</body></html>';
            return;
        }

        oci_free_statement($stid);

        if($accept == 1){
            $stid = oci_parse($conn, "INSERT INTO ZNAJOMI VALUES ($inviter, $id)");
            $err = oci_execute($stid);

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
                echo 'Accepting invite was unsuccessful</body></html>';
                return;
            }
            oci_commit($conn);
            oci_free_statement($stid);
        }
    }else {
         echo "$id_wyzwania, $inviter, $id, $accept";
        $stid = oci_parse($conn, "
                            DELETE
                            FROM ZAPROSZENIE_DO_WYZWANIA
                            WHERE zaproszony = $id AND zapraszajacy = $inviter AND wyzwanie = $id_wyzwania
                        ");
        $err = oci_execute($stid);

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
            echo 'Accepting invite was unsuccessful</body></html>';
            return;
            
        }
        oci_free_statement($stid);
        echo "$id_wyzwania, $inviter, $id, $accept";
        if($accept == 1){
            echo "insert $id_wyzwania $id";
            $stid = oci_parse($conn, "INSERT INTO UCZESTNICY_WYZWANIA VALUES ($id_wyzwania, $id)");
            $err = oci_execute($stid);

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
                echo 'Accepting invite was unsuccessful</body></html>';
                return;
            }
            oci_commit($conn);
            oci_free_statement($stid);
        }
    }
    oci_commit($conn);
    header("location:./index.php")
?>