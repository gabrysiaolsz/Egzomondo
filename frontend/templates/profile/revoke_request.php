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
            include '../_common/connect_to_db.php';
            include '../_common/redirect_to_login.php';
            include '../_common/navbar.php';

            if (!isset($_GET['id'])) {
                header('location:../profile');
            }

            $i = "DELETE FROM ZAPROSZENIA_DO_ZNAJOMYCH WHERE ZAPRASZAJACY = (SELECT ID FROM KONTO WHERE LOGIN = '$_SESSION[login]') AND ZAPROSZONY = $_GET[id]";
            $insert = oci_parse($conn, $i);

            $rc = oci_execute($insert);
            if (!$rc) {
                echo 'Deleting invite was unsuccessful';
                return;
            }
            oci_commit($conn);
            header("location:../profile/?id=$_GET[id]");
        ?>
    </body>
</html>