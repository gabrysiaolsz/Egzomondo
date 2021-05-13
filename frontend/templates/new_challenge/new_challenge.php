<html>
    <head>
        <title>Creating new challenge</title>
    </head>
    <body>
        <!-- Navbar -->
        <div id="navbar">
            <nav>
                <a href="#">
                    <div class="logo">
                        <img src="../../style/img/logo_icon.png" id="logo-icon-normal">
                        <img src="../../style/img/logo_icon_hover.png" id="logo-icon-hover">
                        <div id="logo-text">Egzomondo</div>
                    </div>
                </a>
                <ol>
                    <li><a href="#">My profile</a></li>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About us</a></li>
                    <li><a href='../activity'>New Activity</a></li>
                </ol>
                <div class="search_box">
                    <input type="search" placeholder="Search">
                    <a href="#"><span class="fa fa-search"></span></a>
                </div>
            </nav>
        </div>
        <?php

            $conn = oci_connect('wz418498','IO2021',"//labora.mimuw.edu.pl/LABS");
            if (!$conn) {
                echo "oci_connect failed\n";
                $e = oci_error();
                echo $e['message'];
            }
            session_start();
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
            $stid = oci_parse($conn, 
                    "SELECT id FROM Konto WHERE login='$login'");
            oci_execute($stid);
            if(($row=oci_fetch_row($stid)) != false){
                $id_uzytkownika = $row[0];
                #echo "Your id is:$id_uzytkownika;";
            }else{
                $error = true;
                echo "You need to be signed in $login;\n";
            }
            oci_free_statement($stid);

            $cel = $_REQUEST['objective'];
                if($cel <= 0){
                    $error = true;
                    echo "Objective has to be greater than zero\n";
                }

            $jednostka_celu = $_REQUEST['objective_unit'];

            $id_aktywnosci = $_REQUEST['activity_type'];
                $query = "SELECT id FROM Typ_aktywnosci WHERE nazwa = '$id_aktywnosci'";
                $pars = oci_parse($conn, $query);
                oci_execute($pars);
                if(($row=oci_fetch_row($pars)) != false){
                    $id_aktywnosci = $row[0];
                }else{
                    $error = true;
                    echo "No such activity as $id_aktywnosci\n";
                }
                oci_free_statement($pars);


            if(!$error)
            {
                $query = "INSERT INTO WYZWANIE VALUES (null, '$nazwa', $id_uzytkownika, TO_DATE('$czas_rozpoczecia', 'YYYY-MM-DD'), TO_DATE('$czas_ukonczenia', 'YYYY-MM-DD'), 0, $cel, '$jednostka_celu', 0, $id_aktywnosci)";
                $pars = oci_parse($conn, $query);
                $err = oci_execute($pars);
                if(!$err){
                    $e = oci_error($pars);
                    var_dump($e);
                }
                oci_commit($conn);
                oci_free_statement($pars);

                $query = "SELECT id FROM WYZWANIE WHERE nazwa='$nazwa' AND tworca=$id_uzytkownika AND czas_rozpoczecia = TO_DATE('$czas_rozpoczecia', 'YYYY-MM-DD') AND czas_ukonczenia=TO_DATE('$czas_ukonczenia', 'YYYY-MM-DD') AND id_aktywnosci=$id_aktywnosci AND jednostka_celu='$jednostka_celu' AND cel=$cel";
                $pars = oci_parse($conn, $query);
                $err &= oci_execute($pars);
                $row = oci_fetch_array($pars, OCI_BOTH + OCI_RETURN_NULLS);
                $id_wyzwania = $row[0];
                if(!$err){
                    $e = oci_error($pars);
                    var_dump($e);
                }
                oci_commit($conn);
                oci_free_statement($pars);

                $query = "INSERT INTO UCZESTNICY_WYZWANIA VALUES ($id_wyzwania, $id_uzytkownika)";
                $pars = oci_parse($conn, $query);
                $err &= oci_execute($pars);
                if(!$err){
                    $e = oci_error($pars);
                    var_dump($e);
                }
                oci_commit($conn);
                oci_free_statement($pars);

                if($err){
                    $redirect_to_profile = substr($_SERVER["REQUEST_URI"], 0, -16)."profile";
                    header('Location: ../profile');
                }
            }

            oci_close($conn);

        ?>
    </body>
</html>
<?php oci_close($conn); ?>