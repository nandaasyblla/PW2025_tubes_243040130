<?php
require 'database.php'; 
 
//pwhash
$username = 'nanas';
$password = '020106';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Cek apakah user sudah ada
$result = mysqli_query($conn, "SELECT * FROM user WHERE username = '$username'");
if (mysqli_num_rows($result) > 0) {
    echo "User sudah ada. Silakan hapus dulu dari database jika ingin menambahkan ulang.";
} else {
    $insert = mysqli_query($conn, "INSERT INTO user (username, password) VALUES ('$username', '$hashedPassword')");
    if ($insert) {
        echo "User berhasil ditambahkan dengan password ter-hash!";
    } else {
        echo "Gagal menambahkan user: " . mysqli_error($conn);
    }
}
?>
