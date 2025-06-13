<?php
session_start();
require 'database.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Hitung jumlah artikel dan kategori
$jumlahArtikel = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM articles"));
$jumlahKategori = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM category"));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
        }
        .stats {
            display: flex;
            gap: 20px;
            margin-top: 30px;
        }
        .stats .card {
            flex: 1;
            text-align: center;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            position: relative;
        }
        .kelola-btn {
            margin-top: 15px;
        }
        .navbar {
            background-color:rgb(27, 117, 252);
            padding: 15px;
            color: white;
        }
        .navbar .logout-btn {
            background-color:rgb(2, 2, 2);
            color: white;
            border: none;
        }
        .navbar .logout-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<!-- Navbar -->
<div class="navbar position-relative text-center">
    <h4 class="m-0 w-100"><strong>Dashboard Admin</strong></h4>
    <a href="logout.php" class="btn logout-btn btn-sm position-absolute end-0 top-50 translate-middle-y me-3">Logout</a>
</div>

<div class="container py-4">

    <!-- Statistik -->
    <div class="stats">
        <!-- Artikel -->
        <div class="card">
            <h3><?= $jumlahArtikel ?></h3>
            <p>Artikel</p>
            <a href="dashboard_admin.php" class="btn btn-primary kelola-btn">
                <i class="bi bi-pencil-square"></i> Kelola Artikel
            </a>
        </div>

        <!-- Kategori -->
        <div class="card">
            <h3><?= $jumlahKategori ?></h3>
            <p>Kategori</p>
            <a href="edit_kategori.php" class="btn btn-primary kelola-btn">
                <i class="bi bi-pencil-square"></i> Kelola Kategori
            </a>
        </div>
    </div>

</div>
</body>
</html>