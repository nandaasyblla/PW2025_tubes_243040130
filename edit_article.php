<?php
session_start();
require 'database.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID artikel tidak ditemukan.");
}

$id = intval($_GET['id']);
$artikel = mysqli_query($conn, "SELECT * FROM articles WHERE id = $id");

if (mysqli_num_rows($artikel) !== 1) {
    die("Artikel tidak ditemukan.");
}

$data = mysqli_fetch_assoc($artikel);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = mysqli_real_escape_string($conn, $_POST['title']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);
    $author  = mysqli_real_escape_string($conn, $_POST['author']);
    
    // Konversi tanggal ke format YYYYMMDD
    $date_input = $_POST['date_of_writing'];
    $date = str_replace('-', '', $date_input);

    $image = $data['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowed_types)) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $ext;
            $upload_dir = 'img/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $new_filename);

            if (!empty($data['image']) && file_exists("img/" . $data['image'])) {
                unlink("img/" . $data['image']);
            }

            $image = $new_filename;
        }
    }

    mysqli_query($conn, "
        UPDATE articles 
        SET title='$title', content='$content', author='$author', date_of_writing='$date', image='$image' 
        WHERE id=$id
    ") or die(mysqli_error($conn));

    // Redirect ke dashboard admin setelah simpan
    header("Location: dashboard_admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Artikel</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 40px;
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .form-container {
            max-width: 700px;
            margin: auto;
        }
        .img-preview {
            max-width: 200px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h4 class="mb-4 text-center"><strong>Kelola Artikel dan Komentar</strong></h4>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Judul</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($data['title']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Penulis</label>
            <input type="text" name="author" class="form-control" value="<?= htmlspecialchars($data['author']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Tanggal Penulisan</label>
            <?php
                $tanggal = date('Y-m-d', strtotime($data['date_of_writing']));
            ?>
            <input type="date" name="date_of_writing" class="form-control" value="<?= $tanggal ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Konten</label>
            <textarea name="content" class="form-control" rows="6" required><?= htmlspecialchars($data['content']) ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Gambar Artikel</label><br>
            <?php if (!empty($data['image']) && file_exists("img/" . $data['image'])): ?>
                <img src="img/<?= htmlspecialchars($data['image']) ?>" class="img-thumbnail img-preview">
            <?php else: ?>
                <p class="text-muted">Belum ada gambar.</p>
            <?php endif; ?>
            <input type="file" name="image" class="form-control mt-2" accept="image/*">
            <small class="text-muted">Biarkan kosong jika tidak ingin mengubah gambar.</small>
        </div>

        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
        <a href="dashboard_admin.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>