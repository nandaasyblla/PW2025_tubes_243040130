<?php
session_start();
require 'database.php';

// Cek login admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Hapus kategori jika diminta
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = mysqli_prepare($conn, "DELETE FROM category WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: edit_kategori.php");
    exit;
}

// Ambil data kategori
$result = mysqli_query($conn, "SELECT * FROM category ORDER BY id DESC");
$kategoriList = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f6f9;
        }
        .navbar {
            background-color: rgb(27, 117, 252);
            padding: 15px;
            color: white;
        }
        .navbar .btn-light {
            background-color: #fff;
            color: #000;
        }
        .navbar .btn-light:hover {
            background-color: #ddd;
        }
        .card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        table {
            margin-top: 20px;
        }
        .table thead {
            background-color: rgb(27, 117, 252);
            color: white;
        }
        .btn-sm i {
            margin-right: 4px;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar position-relative text-center">
    <h4 class="m-0 w-100"><strong>Kelola Kategori Artikel</strong></h4>
    <a href="dashboard_admin.php" class="btn btn-light btn-sm position-absolute end-0 top-50 translate-middle-y me-3">
        ← Kembali ke Dashboard
    </a>
</div>

<div class="container py-4">
    <div class="card">
        <h5 class="fw-bold text-center mb-4">Daftar Kategori</h5>

        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th style="width: 10%;">ID</th>
                    <th style="width: 60%;">Nama Kategori</th>
                    <th style="width: 30%;">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($kategoriList as $kategori): ?>
                <tr>
                    <td><?= $kategori['id'] ?></td>
                    <td><?= htmlspecialchars($kategori['name']) ?></td>
                    <td>
                        <a href="form_edit_kategori.php?id=<?= $kategori['id'] ?>" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-square"></i> Edit
                        </a>
                        <a href="edit_kategori.php?hapus=<?= $kategori['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus kategori ini?')">
                            <i class="bi bi-trash3"></i> Hapus
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>