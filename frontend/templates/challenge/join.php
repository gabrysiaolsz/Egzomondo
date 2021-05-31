<html>
    <head>
        <link rel="shortcut icon" href="../../style/img/logo_icon.png">
        <link rel="stylesheet" type="text/css" href="../../style/css/global-style.css" />
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar-style.css" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <script src="script.js"></script>
        <script src="https://kit.fontawesome.com/67c66657c7.js"></script>
    </head>
    <body>
        <?php
            include '../_common/redirect_to_login.php';
            include '../_common/connect_to_db.php';
            include '../_common/navbar.php';
            // Display date differently
            $stid = oci_parse($conn, "alter SESSION set NLS_DATE_FORMAT = 'DD-MM-YYYY'");
            oci_execute($stid);
            oci_free_statement($stid);

            $error = false;
            if (!isset($_GET['id_wyzwania'])) {                                
                echo 'No id_wyzwania provided!';
            }
            $id_wyzwania = $_GET['id_wyzwania'];
            
            $query = "INSERT INTO UCZESTNICY_WYZWANIA VALUES ($id_wyzwania, ".$_SESSION['id'].")";
            $pars = oci_parse($conn, $query);
            $err = oci_execute($pars);
            if (!$err) {
                
                echo 'Joining was unsuccessful';
            }
            oci_commit($conn);
            oci_free_statement($pars);
            if($err)
                header("Location:./?id=$id_wyzwania");
        ?>
    </body>
</html>