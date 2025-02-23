<?php
include 'db/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['repay_loan'])) {
    $loan_id = $_POST['loan_id'];
    $amount = $_POST['amount'];

    $query = "SELECT * FROM loans WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $loan_id, $user_id);
    $stmt->execute();
    $loan = $stmt->get_result()->fetch_assoc();

    if ($loan && isset($loan['remaining_amount']) && $amount <= $loan['remaining_amount']) {
        $conn->begin_transaction();
        $new_balance = $loan['remaining_amount'] - $amount;

        $update_loan = "UPDATE loans SET remaining_amount = ? WHERE id = ?";
        $stmt = $conn->prepare($update_loan);
        $stmt->bind_param("di", $new_balance, $loan_id);
        $stmt->execute();

        $user_balance = "UPDATE users SET balance = balance - ? WHERE id = ?";
        $stmt = $conn->prepare($user_balance);
        $stmt->bind_param("di", $amount, $user_id);
        $stmt->execute();

        $conn->commit();
        echo "<script>alert('Loan repayment successful!');</script>";
    } else {
        echo "<script>alert('Invalid repayment amount or insufficient balance.');</script>";
    }
}

$query = "SELECT * FROM loans WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$loans = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f4f4f9; }
        header, nav { background: #007bff; color: #fff; padding: 1rem; text-align: center; }
        .container { padding: 1rem; }
        .card { background: #fff; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); }
        .btn { padding: 0.5rem 1rem; background: #007bff; color: white; border: none; border-radius: 5px; text-decoration: none; display: inline-block; }
    </style>
</head>
<body>
<header>
    <h1>Loan Management</h1>
</header>
<div class="container">
    <h2>Active Loans</h2>
    <?php if ($loans->num_rows > 0): ?>
        <?php while ($loan = $loans->fetch_assoc()): ?>
            <div class="card">
                <p><strong>Loan ID:</strong> <?= $loan['id'] ?></p>
                <p><strong>Loan Amount:</strong> ₹<?= $loan['amount'] ?></p>
                <p><strong>Remaining Amount:</strong> ₹<?= isset($loan['remaining_amount']) ? $loan['remaining_amount'] : '0.00' ?></p>
                <form method="post">
                    <input type="hidden" name="loan_id" value="<?= $loan['id'] ?>">
                    <label for="amount">Repay Amount:</label>
                    <input type="number" name="amount" required>
                    <button type="submit" name="repay_loan" class="btn">Repay Loan</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No active loans.</p>
    <?php endif; ?>
</div>
</body>
</html>
