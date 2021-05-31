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
?>
<html>
    <head>
        <title>
            Egzomondo
        </title>

        <link rel="stylesheet" type="text/css" href="./frontend/style/css/global-style.css" />
        <link rel="stylesheet" type="text/css" href="./frontend/style/css/navbar-style.css" />
        <link rel="stylesheet" type="text/css" href="style.css" />

        <script src="https://kit.fontawesome.com/67c66657c7.js"></script>
    </head>
    <body>
        <!-- Navbar -->
        <div id="navbar">
            <nav>
                <a href="./">
                    <div class="logo">
                        <img src="./frontend/style/img/logo_icon.png" id="logo-icon-normal">
                        <img src="./frontend/style/img/logo_icon_hover.png" id="logo-icon-hover">
                        <div id="logo-text">Egzomondo</div>
                    </div>
                </a>
                <ol>
                    <li><a href="./frontend/templates/profile">My profile</a></li>
                    <li><a href="./frontend/templates/about">About us</a></li>
                    <li><a href="./frontend/templates/new_challenge">New Challenge</a></li>
                    <li><a href="./frontend/templates/activity">New Activity</a></li>
                </ol>
                <div class="search_box">
                    <input type="search" placeholder="Search">
                    <a href="#"><span class="fa fa-search"></span></a>
                </div>
            </nav>
        </div>
        <div id="below-navbar">
            <div class="container">
                <div class="container-flex">
                    
                    <?php
                        $id_uzytkownika = $_SESSION['id'];
                        $stid = oci_parse($conn, "
                            SELECT *
                            FROM ZAPROSZENIA_DO_ZNAJOMYCH 
                            WHERE zaproszony = $id
                        ");

                        $err = oci_execute($stid);

                        if (!$err) {
                            echo "Error finding friend requests:";
                            $e = oci_error($pars);
                            var_dump($e);
                            return;
                        }

                        while ($row = oci_fetch_array($stid, OCI_BOTH  + OCI_RETURN_NULLS)) {
                            $stid2 = oci_parse($conn, "
                                SELECT login
                                FROM KONTO
                                WHERE id = ".$row[0]."
                            ");

                            $err = oci_execute($stid2);

                            if (!$err) {
                                echo "Error finding inviter name:";
                                $e = oci_error($pars);
                                var_dump($e);
                                return;
                            }

                            $row2 = oci_fetch_array($stid2, OCI_BOTH  + OCI_RETURN_NULLS);

                            echo '
                            <div class="request">
                                <a href="frontend/templates/profile/?id='.$row[0].'">
                                    <div class="friend-request-user">
                                        <div class="request-pfp-container">
                                            <img src="img.jpg" />
                                        </div>
                                        <div class="request-user-name">'.$row2[0].'</div>
                                    </div>
                                </a>
                                <div class="buttons">
                                    <a href="accept_invite.php?accept=1&inviter='.$row[0].'&challenge=0"><div class="accept-button">Accept</div></a>
                                    <a href="accept_invite.php?accept=0&inviter='.$row[0].'&challenge=0"><div class="accept-button reject-button">Decline</div></a>
                                </div>
                            </div>';
                        }
                    ?>
                </div>
            </div>
            <div class="container">
                <div class="container-flex">

                </div>
            </div>
            <div class="container">
                <div class="container-flex">
                    <?php
                        $id_uzytkownika = $_SESSION['id'];
                        $stid = oci_parse($conn, "
                            SELECT *
                            FROM ZAPROSZENIE_DO_WYZWANIA 
                            WHERE zaproszony = $id
                        ");

                        $err = oci_execute($stid);

                        if (!$err) {
                            echo "Error finding challenge invites:";
                            $e = oci_error($pars);
                            var_dump($e);
                            return;
                        }

                        while ($row = oci_fetch_array($stid, OCI_BOTH  + OCI_RETURN_NULLS)) {
                            $stid2 = oci_parse($conn, "
                                SELECT NAZWA
                                FROM WYZWANIE
                                WHERE id = ".$row[0]."
                            ");

                            $err = oci_execute($stid2);

                            if (!$err) {
                                echo "Error:";
                                $e = oci_error($pars);
                                var_dump($e);
                                return;
                            }

                            $row2 = oci_fetch_array($stid2, OCI_BOTH  + OCI_RETURN_NULLS);

                            echo '<div class="request">
                                <a href="frontend/templates/challenge/?id='.$row[0].'">
                                    <div class="challenge-request-user">
                                        '.$row2[0].'
                                    </div>
                                </a>
                                <div class="buttons">
                                    <a href="accept_invite.php?accept=1&inviter='.$row[1].'&challenge='.$row[0].'"><div class="accept-button">Accept</div></a>
                                    <a href="accept_invite.php?accept=0&inviter='.$row[1].'&challenge='.$row[0].'"><div class="accept-button reject-button">Decline</div></a>
                                </div>
                            </div>';
                        }
                    ?>
                </div>
            </div>
        </div>
    </body>
</html>
