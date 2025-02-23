<?php
session_start();
include 'db/db_connection.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

$report_data = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $report_type = $_POST['report_type'];
    $year = $_POST['year'];
    $month = isset($_POST['month']) ? $_POST['month'] : null;

    $transactions_date_column = 'date'; 
    $loans_date_column = 'loan_date';   
    $fixed_deposits_date_column = 'date_created'; 

    if ($report_type === 'monthly' && $month) {
        $query = "SELECT 
                    (SELECT COUNT(*) FROM transactions WHERE YEAR($transactions_date_column) = $year AND MONTH($transactions_date_column) = $month) AS total_transactions,
                    (SELECT COUNT(*) FROM loans WHERE YEAR($loans_date_column) = $year AND MONTH($loans_date_column) = $month) AS total_loans,
                    (SELECT COUNT(*) FROM fixed_deposits WHERE YEAR($fixed_deposits_date_column) = $year AND MONTH($fixed_deposits_date_column) = $month) AS total_deposits";
    } else {
        $query = "SELECT 
                    (SELECT COUNT(*) FROM transactions WHERE YEAR($transactions_date_column) = $year) AS total_transactions,
                    (SELECT COUNT(*) FROM loans WHERE YEAR($loans_date_column) = $year) AS total_loans,
                    (SELECT COUNT(*) FROM fixed_deposits WHERE YEAR($fixed_deposits_date_column) = $year) AS total_deposits";
    }
    $result = $conn->query($query);
    if ($result) {
        $report_data = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Financial Reports</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
        }
        header {
            background-color: #003366;
            color: white;
            padding: 15px 0;
            text-align: center;
        }
        section {
            margin: 20px auto;
            max-width: 800px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-top: 0;
            color: #003366;
        }
        form div {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #003366;
            color: white;
            cursor: pointer;
        }
        button:hover {
            background-color: #00509e;
        }
        .report-results {
            margin-top: 20px;
        }
        .report-results ul {
            list-style-type: none;
            padding: 0;
        }
        .report-results ul li {
            background: #f9f9f9;
            margin: 5px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        a {
            color: #003366;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        footer {
            background-color: #003366;
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<header>
    <h1>Admin Dashboard - Financial Reports</h1>
</header>
<section>
    <h2>Generate Financial Reports</h2>
    <form method="POST">
        <div>
            <label for="report_type">Report Type:</label>
            <select name="report_type" id="report_type" required>
                <option value="monthly">Monthly</option>
                <option value="annual">Annual</option>
            </select>
        </div>
        <div>
            <label for="year">Year:</label>
            <input type="number" name="year" id="year" min="2000" max="<?= date('Y') ?>" required>
        </div>
        <div id="month-section" style="display: none;">
            <label for="month">Month:</label>
            <select name="month" id="month">
                <option value="">Select Month</option>
                <option value="1">January</option>
                <option value="2">February</option>
                <option value="3">March</option>
                <option value="4">April</option>
                <option value="5">May</option>
                <option value="6">June</option>
                <option value="7">July</option>
                <option value="8">August</option>
                <option value="9">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>
        </div>
        <button type="submit">Generate Report</button>
    </form>

    <?php if ($report_data): ?>
        <section class="report-results">
            <h3>Report Results</h3>
            <ul>
                <li>Total Transactions: <?= $report_data['total_transactions'] ?></li>
                <li>Total Loans Issued: <?= $report_data['total_loans'] ?></li>
                <li>Total Fixed Deposits Created: <?= $report_data['total_deposits'] ?></li>
            </ul>
        </section>
    <?php endif; ?>
    <p><a href="admin_dashboard.php">Back to Dashboard</a></p>
</section>
<footer>
    <p>&copy; 2024 BankPro. All rights reserved.</p>
</footer>
<script>
    const reportType = document.getElementById('report_type');
    const monthSection = document.getElementById('month-section');

    reportType.addEventListener('change', function () {
        if (this.value === 'monthly') {
            monthSection.style.display = 'block';
        } else {
            monthSection.style.display = 'none';
        }
    });
</script>
</body>
</html>
