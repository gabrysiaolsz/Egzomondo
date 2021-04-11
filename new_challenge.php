<html>
    <head>
        <title>Creating new challenge</title>
    </head>
    <body>
        <?php
            $conn = oci_connect('wz418498','IO2021',"//labora.mimuw.edu.pl/LABS");
            if (!$conn) {
                echo "oci_connect failed\n";
                $e = oci_error();
                echo $e['message'];
            }
            $error = false;
            $nazwa = $_REQUEST['challenge_name'];
                if(!$nazwa){
                    $error = true;
                    echo "Challenge name cannot be empty";
                }
            $czas_rozpoczecia = $_REQUEST['start_time'];
                
            $czas_ukonczenia = $_REQUEST['end_time'];

            $cel = $_REQUEST['objective'];
                if($cel <= 0){
                    $error = true;
                    echo "Objective has to be greater than zero";
                }
            $jednostka_celu = $_REQUEST['objective_unit'];
            $id_aktywnosci = $_REQUEST['activity_type'];
                $query = "SELECT id FROM Typ_aktywnosci WHERE nazwa = $id_aktywnosci";
                $pars = oci_parse($conn, $query);
                if(oci_fetch($pars)){
                    $id_aktywnosci = oci_result($pars, 'id');
                }else{
                    $error = true;
                    echo "No such activity";
                }
                oci_free_statement($pars);

            
            if(!$error)
            {
                $query = "INSERT INTO WYZWANIE VALUES (null, $nazwa, $czas_rozpoczecia, $czas_ukonczenia, 0, $cel, $jednostka_celu, $id_aktywnosci";
                $pars = oci_parse($conn, $query);
                $err = oci_execute($pars);
                if(!$err){
                    $e = oci_error($pars);
                    var_dump($e);
                }
                oci_commit($conn);
                oci_free_statement($pars);
            }
            
            oci_close($conn);
        ?>
    </body>
</html>