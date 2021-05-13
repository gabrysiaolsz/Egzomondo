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
                    <li><a href='../new_challenge'>New Challenge</a></li>
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

            $czas_rozpoczecia = date('Y-m-d');
            $czas_rozpoczecia = date("Y-m-d", strtotime(str_replace('-', '/', $czas_rozpoczecia)));

            $czas = $_REQUEST['duration'];

            $ilosc = $_REQUEST['distance'];
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


            $id_aktywnosci = $_REQUEST['activity'];
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
                $query = "INSERT INTO Aktywnosc VALUES ($id_uzytkownika, $id_aktywnosci, $ilosc, TO_DATE('$czas_rozpoczecia', 'YYYY-MM-DD'), $czas)";
                $pars = oci_parse($conn, $query);
                $err = oci_execute($pars);
                if(!$err){
                    $e = oci_error($pars);
                    var_dump($e);
                }
                oci_free_statement($pars);
                oci_commit($conn);
                if($err){ 
                $redirect_to_profile = substr($_SERVER["REQUEST_URI"], 0, -16)."profile";
                header('Location: ../profile');
                }
            }

            oci_close($conn);

        ?>
    </body>
</html>