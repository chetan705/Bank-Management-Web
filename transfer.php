<?php
session_start();
include('config.php'); 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_account = htmlspecialchars(trim($_POST['sender_account']));
    $receiver_account = htmlspecialchars(trim($_POST['receiver_account']));
    $amount = floatval($_POST['amount']);
    $transfer_purpose = htmlspecialchars(trim($_POST['transfer_purpose']));
    $reason_for_transfer = htmlspecialchars(trim($_POST['reason_for_transfer']));

    if ($amount <= 0) {
        echo "Error: Amount should be greater than zero.";
    } else {
        $conn->begin_transaction();
        try {
            $check_balance_sql = "SELECT balance FROM users WHERE account_number = ?";
            $stmt_balance = $conn->prepare($check_balance_sql);
            $stmt_balance->bind_param("s", $sender_account);
            $stmt_balance->execute();
            $result_balance = $stmt_balance->get_result();

            if ($result_balance->num_rows > 0) {
                $sender = $result_balance->fetch_assoc();
                if ($sender['balance'] < $amount) {
                    throw new Exception("Insufficient balance.");
                }
            } else {
                throw new Exception("Sender account not found.");
            }

            // Insert transaction record
            $insert_sql = "INSERT INTO transactions (user_id, sender_account, receiver_account, amount, transfer_purpose, reason_for_transfer, date) 
                           VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt_insert = $conn->prepare($insert_sql);
            $stmt_insert->bind_param("issdss", $user_id, $sender_account, $receiver_account, $amount, $transfer_purpose, $reason_for_transfer);
            $stmt_insert->execute();

            // Deduct from sender account
            $update_sender_sql = "UPDATE users SET balance = balance - ? WHERE account_number = ?";
            $stmt_update_sender = $conn->prepare($update_sender_sql);
            $stmt_update_sender->bind_param("ds", $amount, $sender_account);
            $stmt_update_sender->execute();

            // Add to receiver account
            $update_receiver_sql = "UPDATE users SET balance = balance + ? WHERE account_number = ?";
            $stmt_update_receiver = $conn->prepare($update_receiver_sql);
            $stmt_update_receiver->bind_param("ds", $amount, $receiver_account);
            $stmt_update_receiver->execute();

            $conn->commit();
            header("Location: dashboard.php?success=Transaction successful");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            echo "Error: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transfer Money</title>
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
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            padding: 10px;
            font-weight: bold;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Transfer Money</h2>
        <form action="transfer.php" method="post">
            <div class="form-group">
                <label for="sender-account">Sender Account Number:</label>
                <input type="number" id="sender-account" name="sender_account" required>
            </div>
            <div class="form-group">
                <label for="receiver-account">Receiver Account Number:</label>
                <input type="number" id="receiver-account" name="receiver_account" required>
            </div>
            <div class="form-group">
                <label for="amount">Amount to Transfer (INR):</label>
                <input type="number" id="amount" name="amount" required>
            </div>
            <div class="form-group">
                <label for="transfer-purpose">Purpose of Transfer:</label>
                <select id="transfer-purpose" name="transfer_purpose" required>
                    <option value="gift">Gift</option>
                    <option value="loan">Loan</option>
                    <option value="payment">Payment</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group">
                <label for="reason-for-transfer">Reason for Transfer:</label>
                <input type="text" id="reason-for-transfer" name="reason_for_transfer" required>
            </div>
            <div class="form-group">
                <label for="confirmation-email">Confirmation Email (optional):</label>
                <input type="email" id="confirmation-email" name="confirmation_email">
            </div>
            <input type="submit" value="Transfer Money">
        </form>
    </div>
</body>
</html>
