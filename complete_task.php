<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['task_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$task_id = $_GET['task_id'];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("UPDATE tasks SET completed = 1 
                       WHERE task_id = ? AND list_id IN (SELECT list_id FROM lists WHERE user_id = ?)");
$result = $stmt->execute([$task_id, $user_id]);

echo json_encode(['success' => $result]);