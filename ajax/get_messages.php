<?php
session_start();
require_once '../config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit();
}

if (!isset($_GET['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing user ID']);
    exit();
}

$current_user_id = $_SESSION['user_id'];
$other_user_id = $_GET['user_id'];

$stmt = $conn->prepare("
    SELECT m.*, 
           u1.username as sender_username,
           u2.username as receiver_username
    FROM messages m
    JOIN users u1 ON m.sender_id = u1.id
    JOIN users u2 ON m.receiver_id = u2.id
    WHERE (m.sender_id = ? AND m.receiver_id = ?)
       OR (m.sender_id = ? AND m.receiver_id = ?)
    ORDER BY m.created_at ASC
");
$stmt->bind_param("iiii", $current_user_id, $other_user_id, $other_user_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = [
        'id' => $row['id'],
        'sender_id' => $row['sender_id'],
        'receiver_id' => $row['receiver_id'],
        'message' => $row['message'],
        'created_at' => $row['created_at'],
        'sender_username' => $row['sender_username'],
        'receiver_username' => $row['receiver_username']
    ];
}

echo json_encode(['success' => true, 'messages' => $messages]); 