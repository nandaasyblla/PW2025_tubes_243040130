<?php
session_start();
require 'database.php';

// Cek apakah user admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Ambil kategori dari database
$categories = mysqli_query($conn, "SELECT * FROM category ORDER BY name");

// Proses submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = mysqli_real_escape_string($conn, $_POST['title']);
    $content     = mysqli_real_escape_string($conn, $_POST['content']);
    $author      = mysqli_real_escape_string($conn, $_POST['author']);
    $category_id = intval($_POST['category_id']);

    // Konversi tanggal dari YYYY-MM-DD ke INT (YYYYMMDD)
    $dateInput = $_POST['date_of_writing'];
    $dateInt   = (int) str_replace('-', '', $dateInput);  // contoh: "2025-06-13" => 20250613

    // Upload gambar
    $imageName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $allowed = ['jpg', 'jpeg', 'png'];

        if (in_array(strtolower($ext), $allowed)) {
            $imageName = uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], 'img/' . $imageName);
        } else {
            $error = "Format gambar tidak didukung (hanya .jpg, .jpeg, .png)";
        }
    }

    // Simpan data jika tidak ada error
    if (!isset($error)) {
        $query = "INSERT INTO articles (title, content, author, date_of_writing, image, category_id)
                  VALUES ('$title', '$content', '$author', '$dateInt', '$imageName', '$category_id')";
        mysqli_query($conn, $query);

        header("Location: dashboard_admin.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Artikel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            padding: 40px;
        }
        form {
            background-color: #fff;
            padding: 25px;
            max-width: 600px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #007bff;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
        }
        textarea { resize: vertical; }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover { background-color: #0056b3; }
        .alert { max-width: 600px; margin: 10px auto; }
    </style>
</head>
<body>

<?php if (isset($error)) : ?>
    <div class="alert alert-danger text-center"><?= $error ?></div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <h2>Tambah Artikel</h2>
    <input type="text" name="title" placeholder="Judul Artikel" required>
    <textarea name="content" placeholder="Konten Artikel" rows="6" required></textarea>
    <input type="text" name="author" placeholder="Penulis" required>
    <input type="date" name="date_of_writing" required>

    <select name="category_id" required>
        <option value="">-- Pilih Kategori --</option>
        <?php while ($c = mysqli_fetch_assoc($categories)) : ?>
            <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
        <?php endwhile; ?>
    </select>

    <input type="file" name="image" accept=".jpg,.jpeg,.png" required>

    <button type="submit">Simpan</button>
</form>

</body>
</html>