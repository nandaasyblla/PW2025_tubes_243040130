<?php
// konelksi ke database MySQL
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'smart_edu';

$conn = mysqli_connect($host, $user, $pass, $dbname);

// cek koneksi
if (!$conn) {
    die('Connection failed: ' . mysqli_connect_error());
}
?>