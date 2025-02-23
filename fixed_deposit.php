<?php
include 'db/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];
    $tenure = $_POST['tenure'];
    $interest_rate = $_POST['interest_rate'];
    $account_type = $_POST['account_type'];
    $notes = $_POST['notes'];

    if ($amount <= 0 || $tenure <= 0) {
        echo "Invalid amount or tenure.";
        exit();
    }

    $balance_query = "SELECT balance FROM users WHERE id = ?";
    $balance_stmt = $conn->prepare($balance_query);
    $balance_stmt->bind_param("i", $user_id);
    $balance_stmt->execute();
    $balance_result = $balance_stmt->get_result();
    $user_balance = $balance_result->fetch_assoc()['balance'];

    if ($user_balance >= $amount) {
        $insert_fd_query = "INSERT INTO fixed_deposits (user_id, amount, tenure, interest_rate, account_type, notes, creation_date, maturity_date) VALUES (?, ?, ?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? MONTH))";
        $insert_fd_stmt = $conn->prepare($insert_fd_query);
        $insert_fd_stmt->bind_param("iiiiiss", $user_id, $amount, $tenure, $interest_rate, $account_type, $notes, $tenure);
        $insert_fd_stmt->execute();

        $new_balance = $user_balance - $amount;
        $update_balance_query = "UPDATE users SET balance = ? WHERE id = ?";
        $update_balance_stmt = $conn->prepare($update_balance_query);
        $update_balance_stmt->bind_param("di", $new_balance, $user_id);
        $update_balance_stmt->execute();

        header("Location: dashboard.php");
        exit();
    } else {
        echo "Insufficient balance to create a fixed deposit.";
    }
}
?>

<section class="form-container">
    <form method="POST">
        <h2>Create Fixed Deposit</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        
        <label>Amount:</label>
        <input type="number" name="amount" min="1000" required>
        
        <label>Tenure (Months):</label>
        <input type="number" name="tenure" min="1" required>
        
        <label>Interest Rate:</label>
        <input type="number" name="interest_rate" value="5" readonly>

        <label>Account Type:</label>
        <select name="account_type" required>
            <option value="savings">Savings Account</option>
            <option value="current">Current Account</option>
        </select>

        <label>Additional Notes:</label>
        <textarea name="notes" placeholder="Any special instructions?"></textarea>

        <button type="submit">Create</button>
        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </form>
</section>

<style>

.form-container {
    width: 100%;
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 10px;
    background-color: #f9f9f9;
}

.form-container h2 {
    text-align: center;
    color: #333;
}

.form-container label {
    font-size: 16px;
    margin-bottom: 5px;
    color: #555;
    display: block;
}

.form-container input, .form-container select, .form-container textarea {
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
}

.form-container button {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.form-container button:hover {
    background-color: #45a049;
}

.form-container .error {
    color: red;
    font-weight: bold;
    text-align: center;
}

.form-container p {
    text-align: center;
    margin-top: 15px;
}

.form-container a {
    color: #007BFF;
    text-decoration: none;
}

.form-container a:hover {
    text-decoration: underline;
}
</style>
