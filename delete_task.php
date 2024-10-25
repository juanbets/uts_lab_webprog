<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['task_id']) || !isset($_GET['list_id'])) {
    header('Location: dashboard.php');
    exit;
}

$task_id = $_GET['task_id'];
$list_id = $_GET['list_id'];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("DELETE FROM tasks 
                       WHERE task_id = ? AND list_id = ? AND list_id IN (SELECT list_id FROM lists WHERE user_id = ?)");
$result = $stmt->execute([$task_id, $list_id, $user_id]);

if ($result) {
    header("Location: manage_list.php?list_id=$list_id");
} else {
    header("Location: manage_list.php?list_id=$list_id&error=delete_failed");
}
exit;