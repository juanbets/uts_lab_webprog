<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['list_id']) || !isset($_POST['task_description'])) {
    header('Location: dashboard.php');
    exit;
}

$list_id = $_POST['list_id'];
$task_description = $_POST['task_description'];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("INSERT INTO tasks (task_description, list_id) 
                       SELECT ?, ? FROM lists WHERE list_id = ? AND user_id = ?");
$result = $stmt->execute([$task_description, $list_id, $list_id, $user_id]);

if ($result) {
    header("Location: manage_list.php?list_id=$list_id");
} else {
    header("Location: manage_list.php?list_id=$list_id&error=add_failed");
}
exit;