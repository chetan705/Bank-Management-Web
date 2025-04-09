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
    $amount = floatval($_POST['amount']);

    $user_query = "SELECT balance FROM users WHERE id = ?";
    $user_stmt = $conn->prepare($user_query);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user = $user_stmt->get_result()->fetch_assoc();

    $loan_query = "SELECT * FROM loans WHERE id = ? AND user_id = ? AND status = 'Approved'";
    $loan_stmt = $conn->prepare($loan_query);
    $loan_stmt->bind_param("ii", $loan_id, $user_id);
    $loan_stmt->execute();
    $loan = $loan_stmt->get_result()->fetch_assoc();

    if ($loan) {
        $remaining_amount = $loan['remaining_amount'] ?? $loan['amount'];
        if ($amount > 0 && $amount <= $user['balance']) {
            if ($amount <= $remaining_amount) {
                $conn->begin_transaction();

                $new_balance = $user['balance'] - $amount;
                $new_remaining_amount = $remaining_amount - $amount;
                $new_status = $new_remaining_amount <= 0 ? 'Paid' : 'Approved';

                $update_loan = "UPDATE loans SET remaining_amount = ?, status = ? WHERE id = ?";
                $loan_stmt = $conn->prepare($update_loan);
                $loan_stmt->bind_param("dsi", $new_remaining_amount, $new_status, $loan_id);
                $loan_stmt->execute();

                $update_user = "UPDATE users SET balance = ? WHERE id = ?";
                $user_stmt = $conn->prepare($update_user);
                $user_stmt->bind_param("di", $new_balance, $user_id);
                $user_stmt->execute();

                $transaction_query = "INSERT INTO transactions (user_id, type, amount, date, deposit_type, branch, deposit_date, reason, sender_account, receiver_account, transaction_type) VALUES (?, 'withdrawal', ?, NOW(), 'N/A', 'Online', CURDATE(), 'Loan Repayment', ?, 'N/A', 'loan_repayment')";
                $trans_stmt = $conn->prepare($transaction_query);
                $trans_stmt->bind_param("ids", $user_id, $amount, $user_id);
                $trans_stmt->execute();

                $conn->commit();
                echo "<script>alert('Loan repayment successful!'); window.location.href='loan_management.php';</script>";
            } else {
                echo "<script>alert('Repayment amount exceeds remaining loan balance.');</script>";
            }
        } else {
            echo "<script>alert('Insufficient balance to make this repayment.');</script>";
        }
    } else {
        echo "<script>alert('Loan not found or not approved.');</script>";
    }
}

$query = "SELECT * FROM loans WHERE user_id = ? AND status = 'Approved'";
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
        .container { padding: 1rem; max-width: 800px; margin: 0 auto; }
        .card { background: #fff; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); }
        .btn { padding: 0.5rem 1rem; background: #007bff; color: white; border: none; border-radius: 5px; text-decoration: none; display: inline-block; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        input[type="number"] { padding: 0.5rem; width: 150px; margin-right: 10px; }
        h2 { color: #007bff; text-align: center; }
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
                <p><strong>Original Amount:</strong> ₹<?= number_format($loan['amount'], 2) ?></p>
                <p><strong>EMI:</strong> ₹<?= number_format($loan['emi'], 2) ?></p>
                <p><strong>Remaining Amount:</strong> ₹<?= number_format($loan['remaining_amount'] ?? $loan['amount'], 2) ?></p>
                <form method="post">
                    <input type="hidden" name="loan_id" value="<?= $loan['id'] ?>">
                    <label for="amount">Repay Amount:</label>
                    <input type="number" name="amount" min="1" step="0.01" required>
                    <button type="submit" name="repay_loan" class="btn">Repay Loan</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No active loans.</p>
    <?php endif; ?>
    <p><a href="dashboard.php" class="btn">Back to Dashboard</a></p>
</div>
</body>
</html>