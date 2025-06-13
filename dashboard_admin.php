<?php
session_start();
require 'database.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$articles = mysqli_query($conn, "SELECT * FROM articles ORDER BY id DESC");
$comments = mysqli_query($conn, "
    SELECT comments.*, articles.title 
    FROM comments 
    JOIN articles ON comments.article_id = articles.id 
    ORDER BY comments.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .table-container { display: flex; gap: 30px; flex-wrap: wrap; }
        .table-box { flex: 1; min-width: 400px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .img-thumbnail { max-width: 80px; height: auto; object-fit: cover; }
        .action-buttons .btn { margin-right: 5px; }
        .navbar-brand { font-weight: bold; color: #0d6efd !important; }
        h2 { font-weight: bold; }
        .form-control::placeholder { font-size: 14px; }
    </style>
</head>
<body>

<!-- ✅ Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="#">SmartEdu Admin</a>
        <div class="d-flex">
            <a href="dashboard.php" class="btn btn-outline-secondary me-2">Kembali ke Dashboard</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-4">

    <!-- Judul & Tombol Tambah Artikel -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Dashboard Admin</h2>
        <a href="create.php" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Tambah Artikel
        </a>
    </div>

    <!-- 🔍 Live Search -->
    <div class="mb-4">
        <input type="text" id="searchInput" class="form-control" placeholder="🔍 Cari artikel, penulis, komentar, atau user...">
    </div>

    <div class="table-container">

        <!-- Artikel -->
        <div class="table-box">
            <h4>Artikel</h4>
            <table class="table table-bordered table-hover" id="articleTable">
                <thead class="table-light">
                    <tr>
                        <th>Gambar</th>
                        <th>Judul</th>
                        <th>Penulis</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($a = mysqli_fetch_assoc($articles)): ?>
                        <tr class="article-row">
                            <td>
                                <?php if (!empty($a['image'])): ?>
                                    <img src="img/<?= htmlspecialchars($a['image']) ?>" class="img-thumbnail">
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="judul"><?= htmlspecialchars($a['title']) ?></td>
                            <td class="penulis"><?= htmlspecialchars($a['author']) ?></td>
                            <td><?= htmlspecialchars($a['date_of_writing']) ?></td>
                            <td class="action-buttons">
                                <a href="edit_article.php?id=<?= $a['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i> Edit</a>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $a['id'] ?>" data-type="article"><i class="bi bi-trash3"></i> Hapus</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Komentar -->
        <div class="table-box">
            <h4>Komentar</h4>
            <table class="table table-bordered table-hover" id="commentTable">
                <thead class="table-light">
                    <tr>
                        <th>Artikel</th>
                        <th>User</th>
                        <th>Komentar</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($c = mysqli_fetch_assoc($comments)): ?>
                        <tr class="comment-row">
                            <td class="artikel"><?= htmlspecialchars($c['title']) ?></td>
                            <td class="user"><?= htmlspecialchars($c['username']) ?></td>
                            <td class="komentar"><?= htmlspecialchars($c['comment']) ?></td>
                            <td><?= htmlspecialchars($c['created_at']) ?></td>
                            <td class="action-buttons">
                                <a href="edit_comment.php?id=<?= $c['id'] ?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil-square"></i> Edit</a>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="<?= $c['id'] ?>" data-type="comment"><i class="bi bi-trash3"></i> Hapus</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- ✅ AJAX Delete -->
<script>
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', function () {
        const id = this.dataset.id;
        const type = this.dataset.type;
        const url = type === 'article' ? 'hapus_article.php' : 'hapus_comment.php';

        if (!confirm('Yakin ingin menghapus ' + (type === 'article' ? 'artikel' : 'komentar') + ' ini?')) return;

        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'id=' + encodeURIComponent(id)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                this.closest('tr').remove();
            } else {
                alert('Gagal menghapus: ' + data.message);
            }
        })
        .catch(err => alert('Error: ' + err.message));
    });
});
</script>

<!-- ✅ Live Search Script -->
<script>
document.getElementById('searchInput').addEventListener('input', function () {
    const keyword = this.value.toLowerCase();

    // Artikel
    document.querySelectorAll('#articleTable .article-row').forEach(row => {
        const judul = row.querySelector('.judul').textContent.toLowerCase();
        const penulis = row.querySelector('.penulis').textContent.toLowerCase();
        row.style.display = (judul.includes(keyword) || penulis.includes(keyword)) ? '' : 'none';
    });

    // Komentar
    document.querySelectorAll('#commentTable .comment-row').forEach(row => {
        const artikel = row.querySelector('.artikel').textContent.toLowerCase();
        const user = row.querySelector('.user').textContent.toLowerCase();
        const komentar = row.querySelector('.komentar').textContent.toLowerCase();
        row.style.display = (artikel.includes(keyword) || user.includes(keyword) || komentar.includes(keyword)) ? '' : 'none';
    });
});
</script>

</body>
</html>