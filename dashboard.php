<?php
include 'db/db_connection.php'; 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; 
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "User not found.";
    exit();
}

$user = $result->fetch_assoc();

$loan_query = "SELECT * FROM loans WHERE user_id = ?";
$loan_stmt = $conn->prepare($loan_query);
$loan_stmt->bind_param("i", $user_id);
$loan_stmt->execute();
$loans = $loan_stmt->get_result();

$fd_query = "SELECT * FROM fixed_deposits WHERE user_id = ?";
$fd_stmt = $conn->prepare($fd_query);
$fd_stmt->bind_param("i", $user_id);
$fd_stmt->execute();
$fixed_deposits = $fd_stmt->get_result();

$transaction_query = "SELECT * FROM transactions WHERE user_id = ? ORDER BY date DESC";
$transaction_stmt = $conn->prepare($transaction_query);
$transaction_stmt->bind_param("i", $user_id);
$transaction_stmt->execute();
$transactions = $transaction_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Bank Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Welcome, <?= htmlspecialchars($user['name']) ?>!</h1>
    </header>
    <nav>
        <ul>
            <li><a href="dashboard.php" class="active">Home</a></li>
            
            <li><a href="loan_management.php">Loan Management</a></li>
            <li><a href="fixed_deposit_management.php">Fixed Deposits</a></li>
            <li><a href="logout.php?role=user">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="dashboard">
            <div class="card summary">
                <h3>Account Summary</h3>
                <p><strong>Account Number:</strong> <?= htmlspecialchars($user['account_number']) ?></p>
                <p><strong>Account Type:</strong> <?= ucfirst(htmlspecialchars($user['account_type'])) ?></p>
                <p><strong>Current Balance:</strong> ₹<?= number_format($user['balance'], 2) ?></p>
            </div>
            <div class="card actions">
                <h3>Quick Actions</h3>
                <div class="quick-links">
                    <a href="deposit.php" class="btn">Deposit Funds</a>
                    <a href="withdraw.php" class="btn">Withdraw Funds</a>
                    <a href="transfer.php" class="btn">Transfer Money</a>
                </div>
            </div>

            <div class="card loan">
                <h3>Loan Management</h3>
                <a href="loan_application.php" class="btn">Apply for a Loan</a> <!-- Changed this line -->
                <h4>Active Loans</h4>
                <?php if ($loans->num_rows > 0): ?>
                    <ul>
                        <?php while ($loan = $loans->fetch_assoc()): ?>
                            <li>
                                Loan Amount: ₹<?= number_format($loan['amount'], 2) ?> | 
                                Status: <?= ucfirst($loan['status']) ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No active loans.</p>
                <?php endif; ?>
            </div>

            <div class="card deposit">
                <h3>Fixed Deposits</h3>
                <a href="fixed_deposit.php" class="btn">Open a New Fixed Deposit</a> <!-- Changed this line -->
                <h4>Active Fixed Deposits</h4>
                <?php if ($fixed_deposits->num_rows > 0): ?>
                    <ul>
                        <?php while ($fd = $fixed_deposits->fetch_assoc()): ?>
                            <li>
                                Deposit Amount: ₹<?= number_format($fd['amount'], 2) ?> | 
                                Maturity Date: <?= htmlspecialchars($fd['maturity_date']) ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p>No active fixed deposits.</p>
                <?php endif; ?>
            </div>

            <div class="card transactions">
                <h3>Recent Transactions</h3>
                <?php if ($transactions->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($transaction = $transactions->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($transaction['id']) ?></td>
                                    <td><?= ucfirst(htmlspecialchars($transaction['type'])) ?></td>
                                    <td>₹<?= number_format($transaction['amount'], 2) ?></td>
                                    <td><?= htmlspecialchars($transaction['date']) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No recent transactions.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer>
        <p>Bank Management System &copy; 2024</p>
    </footer>
</body>
</html>
