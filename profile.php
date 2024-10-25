<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $new_password = $_POST['new_password'];

    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->execute([$username, $email, $user_id]);

    if (!empty($new_password)) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $user_id]);
    }

    $_SESSION['username'] = $username;
    $success_message = "Profil berhasil diperbarui!";
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Edit Profil</h2>
        <?php if(isset($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>
        <form action="profile.php" method="POST">
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" placeholder="Nama Pengguna" required>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="Email" required>
            <input type="password" name="new_password" placeholder="Kata Sandi Baru (kosongkan jika tidak ingin mengubah)">
            <button type="submit">Perbarui Profil</button>
        </form>
        <a href="dashboard.php">Kembali ke Dashboard</a>
    </div>
</body>
</html>
