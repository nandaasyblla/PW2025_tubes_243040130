<?php
session_start();
require 'database.php';

$error = '';

// Jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Siapkan dan eksekusi query untuk cari user
    $stmt = mysqli_prepare($conn, "SELECT * FROM user WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Jika user ditemukan
    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Simpan session
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];

            // Redirect berdasarkan role
            if ($user['role'] === 'admin') {
                header("Location: dashboard.php");
            } else {
                header("Location: dashboard_user.php");
            }
            exit;
        } else {
            $error = 'Password salah.';
        }
    } else {
        $error = 'Akun tidak ditemukan.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login SmartEdu</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: url('img/backroundlogin.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0; padding: 0;
            display: flex; align-items: center; justify-content: center;
            height: 100vh;
        }
        .login-box {
            background: #fff;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgb(35, 104, 132);
            width: 100%; max-width: 420px;
        }
        h2 {
            text-align: center;
            color:rgb(30, 65, 104);
            margin-bottom: 25px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%; padding: 12px;
            margin-bottom: 15px;
            border-radius: 10px; border: 1px solid #ccc;
            font-size: 15px;
        }
        button {
            width: 100%; padding: 12px;
            background: #31507f; color: #fff;
            border: none; border-radius: 10px;
            font-size: 16px; font-weight: bold;
            cursor: pointer; transition: background .3s;
        }
        button:hover {
            background: #215073;
        }
        .link {
            margin-top: 18px; text-align: center;
        }
        .link a {
            color: #275576; text-decoration: none;
        }
        .link a:hover {
            text-decoration: underline;
        }
        .error {
            background-color: #f8fbfc;
            color: #cc0000;
            font-size: 14px;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
        }
    
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Login SmartEdu</h2>
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="link">
            <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
        </div>
    </div>
</body>
</html>