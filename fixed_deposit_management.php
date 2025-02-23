<?php
include 'db/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM fixed_deposits WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$fixed_deposits = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fixed Deposit Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding-top: 50px;
        }
        .card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 20px;
        }
        .card h3 {
            font-size: 24px;
            margin-bottom: 15px;
        }
        .card ul {
            list-style-type: none;
            padding: 0;
        }
        .card ul li {
            font-size: 16px;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .button-group {
            display: flex;
            gap: 10px;
        }
        .button-group button {
            padding: 10px 20px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .button-group button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h3>Fixed Deposit Management</h3>
            <h4>Your Active Fixed Deposits</h4>
            <?php if ($fixed_deposits->num_rows > 0): ?>
                <ul>
                    <?php while ($fd = $fixed_deposits->fetch_assoc()): ?>
                        <li>
                            Deposit Amount: ₹<?= number_format($fd['amount'], 2) ?> | 
                            Maturity Date: <?= htmlspecialchars($fd['maturity_date']) ?>
                            <div class="button-group">
                                <form method="POST" action="break_fixed_deposit.php">
                                    <input type="hidden" name="fd_id" value="<?= $fd['id'] ?>">
                                    <button type="submit">Break FD</button>
                                </form>
                            </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>You have no active fixed deposits.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
