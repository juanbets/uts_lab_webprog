<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['list_name'])) {
    $list_name = $_POST['list_name'];
    $stmt = $pdo->prepare("INSERT INTO lists (list_name, user_id) VALUES (?, ?)");
    $stmt->execute([$list_name, $user_id]);

    header('Location: dashboard.php');
    exit;
}

if (isset($_GET['delete_list'])) {
    $list_id = $_GET['delete_list'];

    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE list_id = ?");
        $stmt->execute([$list_id]);

        $stmt = $pdo->prepare("DELETE FROM lists WHERE list_id = ? AND user_id = ?");
        $stmt->execute([$list_id, $user_id]);

        $pdo->commit();

        header('Location: dashboard.php');
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = "Gagal menghapus daftar: " . $e->getMessage();
    }
}

$stmt = $pdo->prepare("SELECT * FROM lists WHERE user_id = ?");
$stmt->execute([$user_id]);
$lists = $stmt->fetchAll();

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$filter_query = "";
if ($filter === 'completed') {
    $filter_query = " AND tasks.completed = 1";
} elseif ($filter === 'incomplete') {
    $filter_query = " AND tasks.completed = 0";
}

$search_results = [];
if (isset($_GET['search']) || isset($_GET['filter'])) {
    $search_term = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : '%';
    $stmt = $pdo->prepare("SELECT tasks.*, lists.list_name FROM tasks 
                           JOIN lists ON tasks.list_id = lists.list_id 
                           WHERE lists.user_id = ? AND tasks.task_description LIKE ?" . $filter_query);
    $stmt->execute([$user_id, $search_term]);
    $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Todo App</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <style>
        .completed {
            text-decoration: line-through;
            color: gray;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

        <?php if (isset($error_message)): ?>
            <p class="alert alert-danger"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form action="" method="GET" class="search-form mb-4">
            <input type="text" name="search" placeholder="Cari tugas..." class="form-control mb-2" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            <select name="filter" class="form-select mb-2">
                <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Semua</option>
                <option value="completed" <?= $filter === 'completed' ? 'selected' : '' ?>>Selesai</option>
                <option value="incomplete" <?= $filter === 'incomplete' ? 'selected' : '' ?>>Belum Selesai</option>
            </select>
            <button type="submit" class="btn btn-primary">Cari</button>
        </form>

        <h2>Daftar to do list</h2>
        <table id="taskTable" class="table table-striped table-bordered" style="width:100%">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama List</th>
                    <th>Deskripsi Tugas</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($search_results as $task) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . htmlspecialchars($task['list_name']) . "</td>";
                    echo "<td class='" . ($task['completed'] ? 'completed' : '') . "'>" . htmlspecialchars($task['task_description']) . "</td>";
                    echo "<td>" . ($task['completed'] ? 'Selesai' : 'Belum Selesai') . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <h2>Daftar matakuliah</h2>
        <table id="taskTable" class="table table-striped table-bordered" style="width:100%">
        <ul>
            <?php foreach ($lists as $list): ?>
                <li>
                    <a href="manage_list.php?list_id=<?= $list['list_id'] ?>"><?= htmlspecialchars($list['list_name']) ?></a>
                    <a href="dashboard.php?delete_list=<?= $list['list_id'] ?>" class="delete btn btn-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus daftar ini?');">Hapus</a>
                </li>
            <?php endforeach; ?>
        </ul>

        <h2>Buat Daftar Baru</h2>
        <form action="dashboard.php" method="POST">
            <input type="text" name="list_name" placeholder="Nama Daftar" class="form-control mb-2" required>
            <button type="submit" class="btn btn-success">Buat Daftar</button>
        </form>

        <div class="nav-links mt-4">
            <a href="profile.php" class="btn btn-info">Edit Profil</a>
            <a href="logout.php" class="btn btn-secondary">Keluar</a>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#taskTable').DataTable({
                "pagingType": "full_numbers",
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 75, 100],
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ entri",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    }
                }
            });
        });
    </script>
</body>
</html>
