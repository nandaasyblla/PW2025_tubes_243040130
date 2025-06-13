<?php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === '' || $password === '') {
        $error = "Username dan password tidak boleh kosong.";
    } else {
        // Cek username sudah dipakai atau belum
        $stmt = mysqli_prepare($conn, "SELECT id FROM user WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "Username sudah digunakan.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $role = 'user'; // default role

            $insert = mysqli_prepare($conn, "INSERT INTO user (username, password, role) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($insert, "sss", $username, $hashed, $role);
            mysqli_stmt_execute($insert);

            header("Location: login.php");
            exit;
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: url('img/backround2.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .register-box {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgb(80, 46, 99);
            width: 100%;
            max-width: 420px;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 14px;
            margin-bottom: 18px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 15px;
        }

        input:focus {
            border-color: #6f42c1;
            outline: none;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #6f42c1;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #5a32a3;
        }

        .link {
            margin-top: 20px;
            text-align: center;
        }

        .link a {
            color: #6f42c1;
            text-decoration: none;
        }

        .link a:hover {
            text-decoration: underline;
        }

        .error {
            color: #dc3545;
            font-size: 14px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="register-box">
    <h2>Daftar Akun</h2>
    <?php if (isset($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="post" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Daftar</button>
    </form>
    <div class="link">
        <p><a href="login.php">Sudah punya akun? Login</a></p>
    </div>
</div>
</body>
</html>
