<?php
session_start();
require 'database.php';

// Cek login admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Pastikan ada ID kategori
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: edit_kategori.php");
    exit;
}

$id = (int) $_GET['id'];

// Ambil data kategori berdasarkan ID
$result = mysqli_query($conn, "SELECT * FROM category WHERE id = $id");
if (mysqli_num_rows($result) !== 1) {
    header("Location: edit_kategori.php");
    exit;
}
$kategori = mysqli_fetch_assoc($result);

// Proses saat form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);

    if ($name === '') {
        $error = "Nama kategori tidak boleh kosong.";
    } else {
        $stmt = mysqli_prepare($conn, "UPDATE category SET name = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $name, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Redirect setelah update berhasil
        header("Location: edit_kategori.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kategori</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background-color: #f8f9fa;">
<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Edit Kategori</h4>
        </div>
        <div class="card-body">
            <?php if (isset($error)) : ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Kategori</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($kategori['name']) ?>" required>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save"></i> Simpan Perubahan
                </button>
                <a href="edit_kategori.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>