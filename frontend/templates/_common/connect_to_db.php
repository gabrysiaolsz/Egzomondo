<?php
    $user = 'wz418498';
    $password = 'IO2021';
    $db = '//labora.mimuw.edu.pl/LABS';
    $conn = oci_connect($user, $password, $db);

    if (!$conn) {
        echo "oci_connect failed</br>";
        $e = oci_error();
        echo $e['message'];
        return;
    }
?>
