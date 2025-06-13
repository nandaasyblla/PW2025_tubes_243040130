<?php
session_start();
require 'database.php';
header('Content-Type: application/json');

if ($_SESSION['role'] !== 'admin' || !isset($_POST['id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$id = intval($_POST['id']);
mysqli_query($conn, "DELETE FROM articles WHERE id = $id");

echo json_encode(['success' => true]);