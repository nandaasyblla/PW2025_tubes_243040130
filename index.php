<?php
session_start();
require 'database.php';

// Ambil semua kategori dan artikel
$kategori = mysqli_query($conn, "SELECT * FROM category ORDER BY name");
$artikelSemua = mysqli_query($conn, "SELECT a.*, c.name AS category_name FROM articles a JOIN category c ON a.category_id = c.id ORDER BY a.id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>SmartEdu - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding-top: 80px;
            background-image: url('img/backroundindex.jpg'); 
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }
        nav.navbar {
            background-color: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .dropdown-menu {
            max-height: 400px;
            overflow-y: auto;
            width: 300px;
        }
        .search-results {
            background-color: white;
            position: absolute;
            z-index: 999;
            width: 300px;
            display: none;
        }
        .search-results li {
            padding: 8px;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
        }
        .search-results li:hover {
            background-color: #f1f1f1;
        }
        .article-section {
            margin-top: 40px;
        }
        .card-img-top {
            height: 180px;
            object-fit: cover;
        }
        .clear-icon {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1000;
            color: #aaa;
        }
        .clear-icon:hover {
            color: #000;
        }
        .welcome-text {
            animation: colorChange 5s infinite;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }
        @keyframes colorChange {
            0%   { color: #007bff; }
            25%  { color: #28a745; }
            50%  { color: #ffc107; }
            75%  { color: #dc3545; }
            100% { color: #007bff; }
        }
    </style>
</head>
<body>

<!-- ✅ Navbar -->
<nav class="navbar fixed-top navbar-expand-lg px-4">
    <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="#">
        <i class="bi bi-mortarboard fs-3 me-2"></i>
        SmartEdu
    </a>

    <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
        <ul class="navbar-nav">
            <!-- EXPLORE Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="exploreDropdown" role="button" data-bs-toggle="dropdown">Explore</a>
                <ul class="dropdown-menu" aria-labelledby="exploreDropdown">
                    <?php while ($k = mysqli_fetch_assoc($kategori)): ?>
                        <li>
                            <a class="dropdown-item category-item fw-bold" href="#" data-kategori="<?= htmlspecialchars($k['name']) ?>">
                                <?= htmlspecialchars($k['name']) ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </li>

            <!-- SEARCH -->
            <li class="nav-item mx-3 position-relative">
                <div class="position-relative">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchInput" class="form-control border-start-0" placeholder="Search articles or categories">
                        <span class="clear-icon" id="clearSearch" style="display: none;"><i class="bi bi-x-lg"></i></span>
                    </div>
                    <ul id="searchResults" class="search-results list-unstyled"></ul>
                </div>
            </li>
        </ul>
    </div>

    <!-- ✅ Tombol Login, Signup & Download PDF -->
    <div class="d-flex align-items-center">
        <button class="btn btn-outline-secondary me-2" onclick="downloadPDF()">Download PDF</button>
        <?php if (isset($_SESSION['username'])): ?>
            <span class="me-3 fw-bold">Hi, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <?php else: ?>
            <a href="login.php" class="btn btn-outline-primary me-2">Log In</a>
            <a href="register.php" class="btn btn-primary">Sign Up</a>
        <?php endif; ?>
    </div>
</nav>

<!-- ✅ Artikel Section -->
<div class="container article-section">
    <h2 class="text-center mb-4 welcome-text"><strong>Selamat datang di SmartEdu</strong></h2>
    <div id="backButtonContainer" class="text-center mb-4" style="display: none;">
        <button class="btn btn-secondary" id="showAllBtn">← Back</button>
    </div>
    <div class="row" id="articleContainer">
        <?php while ($a = mysqli_fetch_assoc($artikelSemua)): ?>
            <div class="col-md-4 mb-4 article-item" data-kategori="<?= htmlspecialchars($a['category_name']) ?>">
                <div class="card h-100">
                    <?php if (!empty($a['image']) && file_exists("img/" . $a['image'])): ?>
                        <img src="img/<?= htmlspecialchars($a['image']) ?>" class="card-img-top" alt="Artikel Image">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/300x180?text=No+Image" class="card-img-top" alt="No Image">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5><?= htmlspecialchars($a['title']) ?></h5>
                        <p class="text-muted"><?= htmlspecialchars($a['category_name']) ?></p>
                        <?php
                        if (isset($_SESSION['role'])) {
                            if ($_SESSION['role'] === 'admin') {
                                $link = "dashboard.php?id=" . $a['id'];
                            } elseif ($_SESSION['role'] === 'user') {
                                $link = "dashboard_user.php?id=" . $a['id'];
                            } else {
                                $link = "login.php";
                            }
                        } else {
                            $link = "login.php";
                        }
                        ?>
                        <a href="<?= $link ?>" class="btn btn-outline-primary btn-sm">Baca Selengkapnya</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- ✅ Script SmartEdu -->
<script>
    const searchInput = document.getElementById('searchInput');
    const resultsBox = document.getElementById('searchResults');
    const clearSearch = document.getElementById('clearSearch');
    const backButton = document.getElementById('showAllBtn');
    const backContainer = document.getElementById('backButtonContainer');

    searchInput.addEventListener('input', function () {
        const keyword = this.value.toLowerCase().trim();
        resultsBox.innerHTML = '';
        resultsBox.style.display = 'none';
        clearSearch.style.display = keyword.length > 0 ? 'block' : 'none';

        document.querySelectorAll('.article-item').forEach(item => {
            const title = item.querySelector('h5').textContent.toLowerCase();
            const category = item.getAttribute('data-kategori').toLowerCase();
            item.style.display = (title.includes(keyword) || category.includes(keyword)) ? 'block' : 'none';
        });
    });

    clearSearch.addEventListener('click', function () {
        searchInput.value = '';
        clearSearch.style.display = 'none';
        resultsBox.style.display = 'none';

        document.querySelectorAll('.article-item').forEach(item => {
            item.style.display = 'block';
        });
    });

    document.querySelectorAll('.category-item').forEach(item => {
        item.addEventListener('click', function (e) {
            e.preventDefault();
            const kategori = this.getAttribute('data-kategori').toLowerCase();
            document.querySelectorAll('.article-item').forEach(art => {
                const artKategori = art.getAttribute('data-kategori').toLowerCase();
                art.style.display = (artKategori === kategori) ? 'block' : 'none';
            });
            backContainer.style.display = 'block';
        });
    });

    backButton.addEventListener('click', function () {
        document.querySelectorAll('.article-item').forEach(art => {
            art.style.display = 'block';
        });
        backContainer.style.display = 'none';
    });
</script>

<!-- ✅ html2pdf.js untuk Download PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>
<script>
function downloadPDF() {
    const element = document.body;
    const opt = {
        margin:       0.2,
        filename:     'smartedu-index.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true },
        jsPDF:        { unit: 'in', format: 'a4', orientation: 'landscape' } // ✅ Landscape mode
    };
    html2pdf().set(opt).from(element).save();
}
</script>

<!-- ✅ Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>