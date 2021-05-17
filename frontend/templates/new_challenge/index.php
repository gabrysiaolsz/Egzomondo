<?php
    session_start();
    $redirect_to_login = substr($_SERVER["REQUEST_URI"], 0, -14)."log-in";
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != TRUE) {
        header('location:'.$redirect_to_login);
    }
?>
<html>
    <head>
        <title>Add new challenge</title>
        <meta charset="UTF-8">
        <link rel="stylesheet" type="text/css" href="../../style/css/global-style.css" />
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar-style.css" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <script src="https://kit.fontawesome.com/67c66657c7.js"></script>
    </head>
    <body>
        <!-- NAVBAR -->
        <div id="navbar">
            <nav>
                <a href="../home">
                    <div class="logo">
                        <img src="../../style/img/logo_icon.png" id="logo-icon-normal">
                        <img src="../../style/img/logo_icon_hover.png" id="logo-icon-hover">
                        <div id="logo-text">Egzomondo</div>
                    </div>
                </a>
                <ol>
                    <li><a href="../profile">My profile</a></li>
                    <li><a href="../home">Home</a></li>
                    <li><a href="../about">About us</a></li>
                    <li><a href="../new_challenge">New Challenge</a></li>
                    <li><a href="../activity">New Activity</a></li>
                </ol>
                <div class="search_box">
                    <input type="search" placeholder="Search">
                    <a href="#"><span class="fa fa-search"></span></a>
                </div>
            </nav>
        </div>
        <form action="new_challenge.php" method="POST">
            <!-- ACTIVITY -->
            <div class="w3-row-padding w3-center w3-margin-top">
                <div class="w3-third">
                    <div class="w3-card w3-container">
                        <h3>Activity type</h3><br>
                        <i class="fa fa-heartbeat w3-margin-bottom" style="font-size:120px" aria-hidden="true"></i>
                        <p>Choose challenge name and activity type.</p>
                        <div>
                            <label for="challenge_name">Name:</label>
                            <input type="text" name="challenge_name" class="w3-container" required="">
                            <br /><br />
                            <label for="activity_type">Activity:</label>
                            <select name="activity_type" class="w3-container">
                                <option value="bieg">Running</option>
                                <option value="plywanie">Swimming</option>
                                <option value="rower">Cycling</option>
                                <option value="wszystkie">All activity</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="w3-third">
                    <div class="w3-card w3-container">
                        <h3>Duration</h3><br>
                        <i class="fa fa-clock-o w3-margin-bottom" style="font-size:120px" aria-hidden="true"></i>
                        <p>Enter start and end date.</p>
                        <div>
                            <label for="start_time">Start date:</label>
                            <input type="date" name="start_time" class="w3-container" required="">
                            <br /><br />
                            <label for="end_time">End date:</label>
                            <input type="date" name="end_time" class="w3-container" required="">
                        </div>
                    </div>
                </div>

                <div class="w3-third">
                    <div class="w3-card w3-container">
                        <h3>Goal</h3><br>
                        <i class="fa fa-line-chart w3-margin-bottom" style="font-size:120px" aria-hidden="true"></i>
                        <p>Enter unit and numerical goal:</p>
                        <div>
                            <label for="objective">Goal:</label>
                            <input type="number" value="5" name="objective" min="1" max="100" required="">
                            <select name="objective_unit">
                                <option value="km">km</option>
                                <option value="min">min</option>
                                <option value="kcal">kcal</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <button type="submit" class="submit-btn">Add challenge</button>
            </div>
        </form>
    </body>
</html>