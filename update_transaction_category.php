<?php
include 'db/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $transaction_id = $_POST['transaction_id'];
    $category = $_POST['category'] ?: NULL;

    $check_query = "SELECT user_id FROM transactions WHERE id = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("i", $transaction_id);
    $check_stmt->execute();
    $transaction = $check_stmt->get_result()->fetch_assoc();

    if ($transaction && $transaction['user_id'] == $user_id) {
        $update_query = "UPDATE transactions SET category = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("si", $category, $transaction_id);
        $update_stmt->execute();
    }
}

header("Location: transaction_analytics.php");
exit();
?>