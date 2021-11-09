<?php 

$servername = 'localhost';
    $user = 'testuser';
    $pw = '123456';
    $dbname = 'project1_pet';

    $connection = mysqli_connect($servername, $user, $pw, $dbname);
    if (!$connection) {
        die("connection failed:" . mysqli_connect_error()); //die == exit
    } $connStatus = 'connected successfully' ;

?>