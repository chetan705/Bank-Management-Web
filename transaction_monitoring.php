<?php 
include 'includes/header.php'; 
include 'db/db_connection.php'; 
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

$transactions_query = "SELECT * FROM transactions";
$transactions = $conn->query($transactions_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Monitoring</title>
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding-top: 40px;
        }

        .transaction-monitoring {
            width: 100%;
            max-width: 1200px;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            margin: auto;
        }

        .transaction-monitoring h2 {
            font-size: 32px;
            color: #0056b3;
            margin-bottom: 30px;
            font-weight: 600;
            text-align: center;
        }

        /* Table Styling */
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .table thead {
            background-color: #0056b3;
            color: white;
        }

        .table th, .table td {
            padding: 15px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .table th {
            font-size: 18px;
        }

        .table td {
            font-size: 16px;
        }

        /* Action Links */
        a {
            color: #0056b3;
            text-decoration: none;
            font-size: 16px;
            margin-top: 20px;
            display: inline-block;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #003366;
            text-decoration: underline;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .transaction-monitoring {
                padding: 20px;
            }

            .table th, .table td {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <section class="transaction-monitoring">
        <h2>Transaction Monitoring</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>User ID</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($transaction = $transactions->fetch_assoc()): ?>
                    <tr>
                        <td><?= $transaction['id'] ?></td>
                        <td><?= $transaction['user_id'] ?></td>
                        <td><?= $transaction['type'] ?></td>
                        <td>₹<?= number_format($transaction['amount'], 2) ?></td>
                        <td><?= date("d M Y", strtotime($transaction['date'])) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <p><a href="admin_dashboard.php">Back to Dashboard</a></p>
    </section>
</body>
</html>

