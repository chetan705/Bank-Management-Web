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
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = floatval($_POST['amount']);
    $deposit_type = $_POST['deposit_type'];
    $deposit_date = $_POST['deposit_date'];
    $branch = $_POST['branch'];

    if ($amount <= 0 || empty($deposit_type) || empty($deposit_date) || empty($branch)) {
        $error = "Please fill all required fields.";
    } else {
        $new_balance = $user['balance'] + $amount;

        $conn->begin_transaction();
        try {
            $update_query = "UPDATE users SET balance = ? WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("di", $new_balance, $user_id);
            $stmt->execute();

            $transaction_query = "INSERT INTO transactions (user_id, type, amount, date, deposit_type, branch) VALUES (?, 'Deposit', ?, NOW(), ?, ?)";
            $stmt = $conn->prepare($transaction_query);
            $stmt->bind_param("idss", $user_id, $amount, $deposit_type, $branch);
            $stmt->execute();

            $conn->commit();
            header("Location: dashboard.php?success=Deposit successful");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Transaction failed. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit Funds</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        .form-container {
            width: 100%;
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        label {
            display: block;
            margin: 10px 0 5px;
            color: #555;
        }
        input[type="number"],
        input[type="date"],
        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            font-size: 18px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 15px;
        }
        p {
            text-align: center;
            margin-top: 20px;
        }
        p a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }
        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<section class="form-container">
    <form method="POST">
        <h2>Deposit Funds</h2>

        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

        <label>Amount:</label>
        <input type="number" name="amount" min="1" required>

        <label>Deposit Type:</label>
        <select name="deposit_type" required>
            <option value="Cash">Cash</option>
            <option value="Cheque">Cheque</option>
            <option value="Transfer">Transfer</option>
        </select>

        <label>Deposit Date:</label>
        <input type="date" name="deposit_date" value="<?php echo date('Y-m-d'); ?>" required>

        <label>Branch:</label>
        <input type="text" name="branch" required>

        <button type="submit">Deposit</button>
        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </form>
</section>

</body>
</html>
