<?php
session_start();
require 'database.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID komentar tidak ditemukan.");
}

$id = intval($_GET['id']);
$result = mysqli_query($conn, "SELECT * FROM comments WHERE id = $id");
$comment = mysqli_fetch_assoc($result);

if (!$comment) {
    die("Komentar tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newComment = mysqli_real_escape_string($conn, $_POST['comment']);
    mysqli_query($conn, "UPDATE comments SET comment = '$newComment' WHERE id = $id");
    header("Location: dashboard_admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Komentar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <h3 class="mb-4">Edit Komentar</h3>
    <form method="POST">
        <div class="mb-3">
            <label for="comment" class="form-label">Komentar:</label>
            <textarea class="form-control" id="comment" name="comment" rows="4" required><?= htmlspecialchars($comment['comment']) ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="dashboard_admin.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>