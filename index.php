<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang di Aplikasi Todo</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Selamat Datang di Aplikasi Todo</h1>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php"><button>Ke Dashboard</button></a>
        <?php else: ?>
            <a href="login.php"><button>Masuk</button></a>
            <a href="register.php"><button>Daftar</button></a>
        <?php endif; ?>
    </div>
</body>
</html>