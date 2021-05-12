<?php
    session_start();
    $conn = oci_connect('wz418498','IO2021',"//labora.mimuw.edu.pl/LABS");
    if (!$conn) {
        echo "oci_connect failed\n";
        $e = oci_error();
        echo $e['message'];
    }
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
                    <li><a href="#">My profile</a></li>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">About us</a></li>
                </ol>
                <div class="search_box">
                    <input type="search" placeholder="Search">
                    <a href="#"><span class="fa fa-search"></span></a>
                </div>
            </nav>
        </div>
        <div id="below-navbar">
            <div id="container">
                <!-- Profile picture and name -->
                <div id="pfp-and-name">
                    <div id="pfp-container">
                        <img src="../../style/img/default-pfp.png" />
                    </div>
                    <div id="name-container">
                        <?php echo $_SESSION['login']; ?>
                    </div>
                </div>
                <!-- User info -->
                <div id="user-info">
                    <div id="user-info-box">
                        <div id="user-info-display">
                        <?php
                                $stid = oci_parse($conn, 
                                    "SELECT waga, wzrost, plec FROM Konto WHERE login='".$_SESSION['login']."'");
                                oci_execute($stid);

                                $row = oci_fetch_array($stid, OCI_BOTH  + OCI_RETURN_NULLS);
                                echo '<i class="fas fa-weight"></i></i>: '.$row[0].' kg<br />';
                                echo '<i class="fas fa-arrows-alt-v"></i>: '.$row[1].' cm<br />';
                                echo '<i class="fas fa-venus-mars"></i>: ';
                                if ($row[2] == 0) echo 'Female';
                                else echo 'Male';
                                echo '<br />';
                            ?>
                            <button onclick="editBtn()">Edit</button>
                        </div>
                        <div id="user-info-edit" style="display: none;">
                            <input type="number" placeholder="Weight (kg)" min="0" step="1" value="<?php echo $row[0]; ?>" /><br />
                            <input type="number" placeholder="Height (cm)" min="0" step="1" value="<?php echo $row[1]; ?>" /><br />
                            <input type="radio" id="male" name="sex" value="male" <?php if ($row[2] == 1) echo 'checked="checked"'; ?> />
                            <label for="male">Male</label>
                            <input type="radio" id="female" name="sex" value="female" <?php if ($row[2] == 0) echo 'checked="checked"'; ?> />
                            <label for="female">Female</label><br />
                            <button type="submit">Save</button>
                            <button onclick="cancelBtn()">Cancel</button>
                        </div>
                    </div>
                </div>
                <!-- Stats -->
                <div id="stats">
                    <div id="stats-flexbox">
                        <div class="stats-box-elem">
                            <div class="discipline-img">
                                <img src="../../style/img/disciplines/swimming.png" />
                            </div>
                            <div class="discipline-value">10km</div>
                        </div>
                        <div class="stats-box-elem">
                            <div class="discipline-img">
                                <img src="../../style/img/disciplines/cycling.png" />
                            </div>
                            <div class="discipline-value">10km</div>
                        </div>
                        <div class="stats-box-elem">
                            <div class="discipline-img">
                                <img src="../../style/img/disciplines/running.png" />
                            </div>
                            <div class="discipline-value">10km</div>
                        </div>
                        <div class="stats-box-elem">
                            <div class="discipline-img">
                                <img src="../../style/img/disciplines/ice-skating.png" />
                            </div>
                            <div class="discipline-value">10km</div>
                        </div>
                    </div>
                </div>
                <!-- Friends -->
                <div id="friends">
                    <div id="friends-flexbox">
                        <?php
                            $stid = oci_parse($conn, "
                                SELECT K.LOGIN from KONTO K LEFt JOIN
                                (
                                    SELECT *
                                    from KONTO
                                            LEFT JOIN
                                        (SELECT *
                                            from ZNAJOMI
                                            UNION
                                            SELECT ZNAJOMY2, ZNAJOMY1
                                            from ZNAJOMI) P
                                        ON KONTO.id = P.ZNAJOMY1
                                    WHERE KONTO.LOGIN = '".$_SESSION['login']."'
                                ) T
                                ON K.ID = T.ZNAJOMY2 WHERE T.LOGIN is not null
                            ");
                            oci_execute($stid);

                            while ($row = oci_fetch_array($stid, OCI_BOTH  + OCI_RETURN_NULLS)) {
                                echo '
                                    <div class="friends-box-elem">
                                        <a href="#">
                                            <div class="friends-box-elem-link">
                                                <div class="friends-pfp-container">
                                                    <img src="../../style/img/default-pfp.png" />
                                                </div>
                                                <div class="friends-name-container">
                                                    '.$row[0].'
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                ';
                            }
                        ?>
                    </div>
                </div>
                <!-- Challenges -->
                <div id="challenges">
                    <div id="challenges-flex">
                        <div class="challenges-box-elem">
                            <div class="challenges-box-progress-bar" style="width: 75%;"></div>
                            <div class="challenges-box-name">A Great Challenge</div>
                        </div>
                        <div class="challenges-box-elem">
                            <div class="challenges-box-progress-bar" style="width: 55%;"></div>
                            <div class="challenges-box-name">Another Amazing Challenge</div>
                        </div>
                        <div class="challenges-box-elem">
                            <div class="challenges-box-progress-bar" style="width: 0%;"></div>
                            <div class="challenges-box-name">Too Hard To Even Begin</div>
                        </div>
                        <div class="challenges-box-elem">
                            <div class="challenges-box-progress-bar" style="width: 15%;"></div>
                            <div class="challenges-box-name">Test Challenge</div>
                        </div>
                        <div class="challenges-box-elem">
                            <div class="challenges-box-progress-bar" style="width: 100%;"></div>
                            <div class="challenges-box-name">This One Was Actually Completed!</div>
                        </div>
                        <div class="challenges-box-elem">
                            <div class="challenges-box-progress-bar" style="width: 60%;"></div>
                            <div class="challenges-box-name">Just A Casual Challenge</div>
                        </div>
                        <div class="challenges-box-elem">
                            <div class="challenges-box-progress-bar" style="width: 15%;"></div>
                            <div class="challenges-box-name">Another Test Challenge</div>
                        </div>
                        <div class="challenges-box-elem">
                            <div class="challenges-box-progress-bar" style="width: 15%;"></div>
                            <div class="challenges-box-name">Another Test Challenge</div>
                        </div>
                        <div class="challenges-box-elem">
                            <div class="challenges-box-progress-bar" style="width: 15%;"></div>
                            <div class="challenges-box-name">Another Test Challenge</div>
                        </div>
                        <div class="challenges-box-elem">
                            <div class="challenges-box-progress-bar" style="width: 15%;"></div>
                            <div class="challenges-box-name">Another Test Challenge</div>
                        </div>
                        <div class="challenges-box-elem">
                            <div class="challenges-box-progress-bar" style="width: 15%;"></div>
                            <div class="challenges-box-name">Another Test Challenge</div>
                        </div>
                        <div class="challenges-box-elem">
                            <div class="challenges-box-progress-bar" style="width: 15%;"></div>
                            <div class="challenges-box-name">Another Test Challenge</div>
                        </div>
                        <div class="challenges-box-elem">
                            <div class="challenges-box-progress-bar" style="width: 15%;"></div>
                            <div class="challenges-box-name">Another Test Challenge</div>
                        </div>
                        <div class="challenges-box-elem">
                            <div class="challenges-box-progress-bar" style="width: 15%;"></div>
                            <div class="challenges-box-name">Another Test Challenge</div>
                        </div>
                        <div class="challenges-box-elem">
                            <div class="challenges-box-progress-bar" style="width: 15%;"></div>
                            <div class="challenges-box-name">Another Test Challenge</div>
                        </div>
                    </div>
                </div>
            </div>        
        </div>
    </body>
</html>
<?php oci_close($conn); ?>
