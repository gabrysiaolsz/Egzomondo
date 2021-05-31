<?php include '../_common/redirect_to_login.php'; ?>
<html>
    <head>
        <title>Add new activity</title>
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="../../style/img/logo_icon.png">
        <link rel="stylesheet" type="text/css" href="../../style/css/global-style.css" />
        <link rel="stylesheet" type="text/css" href="../../style/css/navbar-style.css" />
        <link rel="stylesheet" type="text/css" href="style.css">
        <script src="https://kit.fontawesome.com/67c66657c7.js"></script>
    </head>
    <body>
        <?php include '../_common/navbar.php'; ?>
        <form action="new_activity.php" method="post">
            <!-- ACTIVITY -->
            <div class="w3-row-padding w3-center w3-margin-top">
                <div class="w3-third">
                    <div class="w3-card w3-container">
                        <h3>Activity type</h3><br>
                        <i class="fa fa-heartbeat w3-margin-bottom" style="font-size:120px"></i>
                        <p>Choose the activity type from the list.</p>
                        <select name="activity" class="w3-container">
                            <option value="bieg">Running</option>
                            <option value="plywanie">Swimming</option>
                            <option value="rower">Cycling</option>
                            <option value="dowolna">All</option>
                        </select>
                    </div>
                </div>

                <div class="w3-third">
                    <div class="w3-card w3-container">
                        <h3>Duration</h3><br>
                        <i class="fa fa-clock-o w3-margin-bottom" style="font-size:120px"></i>
                        <p>Enter duration of the activity in minutes.</p>
                        <p>You can choose from 5 up to 180 minutes:</p>
                        <input type="number" name="duration" value="30" class="w3-container" min="5" max="180" required>
                    </div>
                </div>

                <div class="w3-third">
                    <div class="w3-card w3-container">
                        <h3>Distance</h3><br>
                        <i class="fa fa-line-chart w3-margin-bottom" style="font-size:120px"></i>
                        <p>Enter travelled distance in kilometers.</p>
                        <p>You can choose from 1 up to 100 kilometers:</p>
                        <input type="number" name="distance" value="5" class="w3-container" min="1" max="100" required>
                    </div>
                </div>
            </div>
            <div>
                <input type="submit" class="submit-btn" value="Add activity">
            </div>
        </form>
    </body>
</html>
