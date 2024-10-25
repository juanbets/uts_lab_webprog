<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['list_id'])) {
    header('Location: dashboard.php');
    exit;
}

$list_id = $_GET['list_id'];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM lists WHERE list_id = ? AND user_id = ?");
$stmt->execute([$list_id, $user_id]);
$list = $stmt->fetch();

if (!$list) {
    header('Location: dashboard.php');
    exit;
}

// Fungsi untuk menandai tugas sebagai selesai atau belum selesai
if (isset($_GET['toggle_task'])) {
    $task_id = $_GET['toggle_task'];
    $stmt = $pdo->prepare("UPDATE tasks SET completed = NOT completed WHERE task_id = ? AND list_id = ?");
    $stmt->execute([$task_id, $list_id]);
    header("Location: manage_list.php?list_id=$list_id");
    exit;
}

// Ambil semua tugas untuk daftar ini
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE list_id = ? ORDER BY completed ASC, task_id DESC");
$stmt->execute([$list_id]);
$tasks = $stmt->fetchAll();

// Pesan error
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'add_failed') {
        $error_message = "Gagal menambahkan tugas.";
    } elseif ($_GET['error'] == 'delete_failed') {
        $error_message = "Gagal menghapus tugas.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Daftar: <?= htmlspecialchars($list['list_name']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Kelola Daftar: <?= htmlspecialchars($list['list_name']) ?></h1>

        <?php if (isset($error_message)): ?>
            <p class="error"><?= $error_message ?></p>
        <?php endif; ?>

        <ul class="task-list">
            <?php foreach ($tasks as $task): ?>
                <li class="<?= $task['completed'] ? 'completed' : '' ?>">
                    <span class="task-description"><?= htmlspecialchars($task['task_description']) ?></span>
                    <div class="task-actions">
                        <a href="?list_id=<?= $list_id ?>&toggle_task=<?= $task['task_id'] ?>" class="toggle-btn">
                            <?php if ($task['completed']): ?>
                                <i class="fas fa-check-circle"></i>
                            <?php else: ?>
                                <i class="far fa-circle"></i>
                            <?php endif; ?>
                        </a>
                        <a href="delete_task.php?task_id=<?= $task['task_id'] ?>&list_id=<?= $list_id ?>" class="delete-btn" onclick="return confirm('Apakah Anda yakin ingin menghapus tugas ini?');">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <h2>Tambah Tugas Baru</h2>
        <form action="add_task.php" method="POST">
            <input type="hidden" name="list_id" value="<?= $list_id ?>">
            <input type="text" name="task_description" placeholder="Deskripsi Tugas" required>
            <button type="submit">Tambah Tugas</button>
        </form>

        <a href="dashboard.php" class="back-link">Kembali ke Dashboard</a>
    </div>
</body>
</html>
