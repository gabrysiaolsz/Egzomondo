<?php
    $user = 'wz418498';
    $password = 'IO2021';
    $db = '//labora.mimuw.edu.pl/LABS';
    $conn = oci_connect($user, $password, $db);
    $redirect_to_login = substr($_SERVER["REQUEST_URI"], 0, -7)."log-in";
    $redirect_to_new_challenge = substr($_SERVER["REQUEST_URI"], 0, -7)."new_challenge";
    $redirect_to_search = substr($_SERVER["REQUEST_URI"], 0, -7)."search";
    $redirect_to_challenge = substr($_SERVER["REQUEST_URI"], 0, -7)."challenge/?id=";
    $upload_dir = '../../uploads/profilepic/';

    if (!$conn) {
        echo "oci_connect failed\n";
        $e = oci_error();
        echo $e['message'];
    }

    session_start();

    # If you're not logged in, redirect to login page.
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != TRUE) {
        header('location:'.$redirect_to_login);
    }

    if (!isset($_POST['keyword'])) {
        // Do nothing I guess.
    } else {

    }

    // oci_close($conn);
?>
<html>
    <head>
        <title>
            Egzomondo - <?php echo $_SESSION['login']; ?>
        </title>
        <link rel="shortcut icon" href="../../style/img/logo_icon.png">

        <link rel="stylesheet" type="text/css" href="../../style/css/global-style.css" />
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar-style.css" />
        <link rel="stylesheet" type="text/css" href="style.css" />

        <script src="buttons.js"></script>
        <script src="https://kit.fontawesome.com/67c66657c7.js"></script>
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
                    <li><a href="../home">Home</a></li>
                    <li><a href="../about">About us</a></li>
                    <li><a href="../new_challenge">New Challenge</a></li>
                    <li><a href="../activity">New Activity</a></li>
                </ol>
                <div class="search_box">
                    <form action="index.php" method="POST" class="input-group" >
                    <input type="search" name="keyword" placeholder="Search">
                    <!-- <input type="submit"><span class="fa fa-search"></span> -->
                    <button type="submit"><span class="fa fa-search"></span></button>
                    <!-- <a href="<?php echo $redirect_to_search ?>"><span class="fa fa-search"></span></a> -->
                    </form>
                </div>
            </nav>
        </div>
        <div id="below-navbar">
            <div id="container">
                <div id="inner_search">
                    <table style="margin: 0 auto; padding-left:25%;padding-right:25%">
                        <tr>
                            <td colspan=2>
                                <?php if (!isset($_POST['keyword'])) { ?>
                                    <h1 style="text-align:center"> You've searched for nothing </h1>
                                <?php } else { ?>
                                    <h1 style="text-align:center"> You've searched for <?php echo $_POST['keyword']; ?> </h1>
                                <?php } ?>
                            </td>
                        </tr>
                        <tr>
                        <td><h1 style="text-align:center"> Users: </h1></td>
                        <td><h1 style="text-align:center"> Challenges: </h1></td>
                        </tr>
                        <tr><td>
                            <!-- Friends -->
                            <div id="friends" style="height:700px; overflow-y:auto;float:left; display:block;">
                                <div id="friends-flexbox">
                                    <?php
                                        // $query = 
                                        $stid = oci_parse($conn, "
                                            SELECT id, login FROM KONTO WHERE LOGIN LIKE '%$_POST[keyword]%'
                                        ");

                                        oci_execute($stid);

                                        while ($row = oci_fetch_array($stid, OCI_BOTH  + OCI_RETURN_NULLS)) {
                                            $user_id  = $row[0];
                                            $username = $row[1];

                                            echo "
                                                <div id='user-info'>
                                                <div id='user-info-box'>
                                                        <div id='pfp-and-name'>
                                                            <div id='pfp-container'>";
                                            if (file_exists(''.$upload_dir.''.$user_id.'.png')) { 
                                                echo '<img src="../../uploads/profilepic/'.$user_id.'.png">';
                                            } else {
                                                echo '<img src="../../style/img/default-pfp.png">';
                                            } 
                                            echo "          </div>
                                                            <div id='name-container'>
                                                                $username
                                                            </div>
                                                        </div>
                                                    </a></div>
                                                </div>
                                            ";
                                        }
                                    ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <!-- Challenges -->
                            <div id="challenges" style="height:700px;overflow-y:auto;float:right; display:block;">
                                <div id="challenges-flex">
                                    <?php
                                        // $query = 
                                        $stid = oci_parse($conn, "
                                            SELECT w.id, w.nazwa, k.login, w.cel, w.jednostka_celu, w.czas_rozpoczecia, w.czas_ukonczenia 
                                            FROM WYZWANIE w JOIN KONTO k ON w.TWORCA = k.id 
                                            WHERE NAZWA LIKE '%$_POST[keyword]%' AND CZY_PRYWATNE = 0
                                        ");

                                        oci_execute($stid);

                                        while ($row = oci_fetch_array($stid, OCI_BOTH  + OCI_RETURN_NULLS)) {
                                            $challenge_id   = $row[0];
                                            $challenge_name = $row[1];
                                            $creator_name   = $row[2];
                                            $goal           = $row[3];
                                            $unit           = $row[4];
                                            $start_time     = $row[5];
                                            $end_time       = $row[6];

                                            echo "
                                                <div class='challenges-box-elem'>
                                                    <div class='challenges-box-progress-bar' style='width: 0%;'></div>
                                                    <div class='challenges-box-name'><a href='../challenge/?id=$challenge_id'>$challenge_name created by $creator_name.</a></div>
                                                </div>
                                            ";
                                        }
                                    ?>
                                </div>
                            </div>
                        </td></tr>
                    </table>
                </div>
            </div>        
        </div>
    </body>
</html>
<?php oci_close($conn); ?>
