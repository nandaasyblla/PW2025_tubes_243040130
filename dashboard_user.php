<?php
session_start();
require 'database.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Simpan komentar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['article_id'], $_POST['comment'])) {
    $article_id = intval($_POST['article_id']);
    $comment = trim(mysqli_real_escape_string($conn, $_POST['comment']));

    if (!empty($comment)) {
        mysqli_query($conn, "INSERT INTO comments (article_id, username, comment) VALUES ($article_id, '$username', '$comment')");
    }

    header("Location: " . $_SERVER['PHP_SELF'] . "#article-$article_id");
    exit;
}

// Ambil semua artikel
$articles = mysqli_query($conn, "SELECT * FROM articles ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User - Komentar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-image: url('img/backrounddashboard.jpg'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            padding: 20px;
        }

        .container {
            background-color: rgba(210, 246, 252, 0.95);
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .article-comment-wrapper {
            margin-bottom: 40px;
            padding: 20px;
            border-radius: 12px;
            background-color: rgb(249, 253, 253);
            box-shadow: 0 4px 25px rgb(13, 70, 101);
        }

        .article-image {
            max-height: 200px;
            object-fit: contain;
            border-radius: 8px;
            margin-bottom: 15px;
            width: 100%;
        }

        .comment-box textarea {
            resize: none;
        }

        .comment-list {
            background-color: #f1f1f1;
            padding: 15px;
            border-radius: 8px;
            max-height: 300px;
            overflow-y: auto;
        }

        .comment-item {
            margin-bottom: 10px;
        }

        .btn-group-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .article-content {
            text-align: left;
        }

        .search-box {
            width: 50%;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light shadow-sm mb-4">
    <div class="container-fluid px-4">
        <!-- Logo dan Judul -->
        <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="#">
            <i class="bi bi-mortarboard me-2 text-primary"></i> <span class="fw-bold text-primary">SmartEdu</span>
        </a>

        <!-- Form Pencarian -->
        <form class="d-flex search-box mx-auto">
            <input class="form-control me-2" type="search" placeholder="Cari artikel..." aria-label="Search" id="searchInput">
            <button class="btn btn-outline-primary" type="button">
                <i class="bi bi-search"></i>
            </button>
        </form>

        <!-- Tombol Navigasi -->
        <div>
            <a href="index.php" class="btn btn-outline-secondary btn-sm me-2">Home</a>
            <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <h2 class="text-center text-primary fw-bold mb-5">Daftar Artikel Lengkap</h2>

    <div id="articleContainer">
        <?php while ($article = mysqli_fetch_assoc($articles)): ?>
            <div class="article-comment-wrapper row article-item" id="article-<?= $article['id'] ?>">
                <!-- Kolom Artikel -->
                <div class="col-md-6 article-content">
                    <?php if (!empty($article['image']) && file_exists("img/" . $article['image'])): ?>
                        <img src="img/<?= htmlspecialchars($article['image']) ?>" class="article-image" alt="Gambar Artikel">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/800x200?text=No+Image" class="article-image" alt="No Image">
                    <?php endif; ?>

                    <h5 class="text-primary fw-bold"><?= htmlspecialchars($article['title']) ?></h5>
                    <p><strong>Penulis:</strong> <?= htmlspecialchars($article['author']) ?></p>
                    <p><strong>Tanggal Penulisan:</strong> <?= htmlspecialchars($article['date_of_writing']) ?></p>
                    <p><?= nl2br(htmlspecialchars($article['content'])) ?></p>
                </div>

                <!-- Kolom Komentar -->
                <div class="col-md-6">
                    <form method="POST" class="comment-box">
                        <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                        <div class="mb-2">
                            <textarea name="comment" class="form-control" rows="3" placeholder="Tulis komentar..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Kirim Komentar</button>
                    </form>

                    <div class="comment-list mt-3">
                        <?php
                        $article_id = $article['id'];
                        $comments = mysqli_query($conn, "SELECT * FROM comments WHERE article_id = $article_id ORDER BY created_at DESC");
                        if (mysqli_num_rows($comments) > 0):
                            while ($c = mysqli_fetch_assoc($comments)):
                        ?>
                            <div class="comment-item">
                                <strong><?= htmlspecialchars($c['username']) ?></strong>
                                <small class="text-muted"><?= $c['created_at'] ?></small>
                                <p class="mb-1"><?= nl2br(htmlspecialchars($c['comment'])) ?></p>
                                <hr>
                            </div>
                        <?php endwhile; else: ?>
                            <p class="text-muted"><em>Belum ada komentar.</em></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<script>
// LIVE SEARCH
document.getElementById('searchInput').addEventListener('keyup', function () {
    const keyword = this.value.toLowerCase();
    const articles = document.querySelectorAll('.article-item');

    articles.forEach(article => {
        const title = article.querySelector('h5').innerText.toLowerCase();
        const content = article.querySelector('p').innerText.toLowerCase();

        if (title.includes(keyword) || content.includes(keyword)) {
            article.style.display = '';
        } else {
            article.style.display = 'none';
        }
    });
});
</script>

</body>
</html>