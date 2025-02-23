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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $account_number = $_POST['account_number'];
    $amount = $_POST['amount'];
    $withdraw_reason = $_POST['withdraw_reason'];
    $notes = $_POST['notes'];

    if ($amount <= 0) {
        echo "Invalid amount.";
    } elseif ($amount > $user['balance']) {
        echo "Insufficient balance.";
    } else {
    
        $new_balance = $user['balance'] - $amount;
        $update_query = "UPDATE users SET balance = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("di", $new_balance, $user_id);
        $update_stmt->execute();

        $transaction_query = "INSERT INTO transactions (user_id, type, amount, reason, notes, date) VALUES (?, 'withdrawal', ?, ?, ?, NOW())";
        $transaction_stmt = $conn->prepare($transaction_query);
        $transaction_stmt->bind_param("idss", $user_id, $amount, $withdraw_reason, $notes);
        $transaction_stmt->execute();

        header("Location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw Funds</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #28a745;
            color: #fff;
            border: none;
            cursor: pointer;
            padding: 10px;
            font-weight: bold;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Withdraw Funds</h2>
        <form method="post">
            <div class="form-group">
                <label for="account-number">Account Number:</label>
                <input type="number" id="account-number" name="account_number" value="<?= htmlspecialchars($user['account_number']) ?>" readonly required>
            </div>
            <div class="form-group">
                <label for="amount">Amount to Withdraw (INR):</label>
                <input type="number" id="amount" name="amount" required>
            </div>
            <div class="form-group">
                <label for="withdraw-reason">Reason for Withdrawal:</label>
                <select id="withdraw-reason" name="withdraw_reason" required>
                    <option value="emergency">Emergency</option>
                    <option value="investment">Investment</option>
                    <option value="personal-expenses">Personal Expenses</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="notes">Additional Notes (optional):</label>
                <textarea id="notes" name="notes" rows="4" style="width: 100%; border: 1px solid #ddd; border-radius: 4px;"></textarea>
            </div>
            <input type="submit" value="Withdraw Funds">
        </form>
    </div>
</body>
</html>
