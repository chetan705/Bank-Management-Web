<?php
include 'db/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$fd_id = $_POST['fd_id'];

$query = "SELECT * FROM fixed_deposits WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $fd_id, $user_id);
$stmt->execute();
$fd = $stmt->get_result()->fetch_assoc();

if ($fd) {
    $amount = $fd['amount'];
    $interest_rate = $fd['interest_rate'];
    $penalty_rate = 2; 

    $penalty_amount = $amount - ($amount * ($penalty_rate / 100));

    $balance_query = "SELECT balance FROM users WHERE id = ?";
    $balance_stmt = $conn->prepare($balance_query);
    $balance_stmt->bind_param("i", $user_id);
    $balance_stmt->execute();
    $user_balance = $balance_stmt->get_result()->fetch_assoc()['balance'];

    $new_balance = $user_balance + $penalty_amount;
    $update_balance_query = "UPDATE users SET balance = ? WHERE id = ?";
    $update_balance_stmt = $conn->prepare($update_balance_query);
    $update_balance_stmt->bind_param("di", $new_balance, $user_id);
    $update_balance_stmt->execute();

    $delete_fd_query = "DELETE FROM fixed_deposits WHERE id = ?";
    $delete_fd_stmt = $conn->prepare($delete_fd_query);
    $delete_fd_stmt->bind_param("i", $fd_id);
    $delete_fd_stmt->execute();

    header("Location: fixed_deposit_management.php?message=FD%20broken%20successfully.");
} else {
    echo "FD not found.";
}
