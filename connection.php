<?php
$servername = "localhost";
$username = "selfaith_atahan";
$password = "0804d[YBPzi.eB";
$dbname = "selfaith_selfaithlete_db";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
