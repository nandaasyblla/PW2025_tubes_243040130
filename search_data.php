<?php
// Hubungkan ke database
require 'database.php';

// Ambil keyword dari URL (pakai metode GET)
$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';

// Siapkan array untuk hasil pencarian
$results = [];

// Jika keyword tidak kosong, lakukan pencarian
if (!empty($keyword)) {
    // Query untuk mencari dari tabel kategori (name) dan artikel (title)
    $query = "
        SELECT name AS result FROM category 
        WHERE name LIKE '%$keyword%'
        UNION
        SELECT title AS result FROM articles 
        WHERE title LIKE '%$keyword%'
        LIMIT 10
    ";
    $search = mysqli_query($conn, $query);

    // Simpan hasil ke array
    while ($row = mysqli_fetch_assoc($search)) {
        $results[] = $row['result'];
    }
}

// Kembalikan hasil dalam format JSON
header('Content-Type: application/json');
echo json_encode($results);
?>