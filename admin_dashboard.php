<?php 
include 'includes/header.php'; 
include 'db/db_connection.php'; 
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
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

        .admin-dashboard {
            width: 100%;
            max-width: 1200px;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            margin: auto;
        }

        .admin-dashboard h2 {
            font-size: 32px;
            color: #0056b3;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .admin-content {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .admin-card {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .admin-card a {
            text-decoration: none;
            color: #fff;
            font-weight: bold;
            font-size: 18px;
            background-color: #0056b3;
            padding: 15px 20px;
            border-radius: 6px;
            display: inline-block;
            margin-top: 15px;
            transition: background-color 0.3s ease;
        }

        .admin-card a:hover {
            background-color: #003366;
        }

        a.logout {
            display: inline-block;
            text-decoration: none;
            color: #1e90ff;
            font-size: 16px;
            margin-top: 30px;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        a.logout:hover {
            color: #003366;
            text-decoration: underline;
        }

        footer {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: grey;
            font-weight: 400;
        }

        footer a {
            color: #1e90ff;
            text-decoration: none;
            margin: 0 12px;
            font-size: 16px;
        }

        footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .admin-dashboard {
                padding: 20px;
            }

            .admin-content {
                grid-template-columns: 1fr;
            }

            .admin-card a {
                font-size: 16px;
                padding: 10px 15px;
            }

            footer {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <section class="admin-dashboard">
        <h2>Welcome to the Admin Panel</h2>
        
        <div class="admin-content">
            <div class="admin-card">
                <h3>Manage Users</h3>
                <p>View and manage all users in the system</p>
                <a href="manage_users.php">Manage Now</a>
            </div>

            <div class="admin-card">
                <h3>Loan Approvals</h3>
                <p>Approve or deny loan requests</p>
                <a href="loan_approvals.php">Approve Loans</a>
            </div>
            <div class="admin-card">
                <h3>Monitor Transactions</h3>
                <p>Monitor all bank transactions</p>
                <a href="transaction_monitoring.php">View Transactions</a>
            </div>
            <div class="admin-card">
                <h3>Generate Reports</h3>
                <p>Create reports for the bank's activities</p>
                <a href="generate_reports.php">Generate Reports</a>
            </div>
        </div>
        <a href="logout.php?role=admin" class="logout">Logout</a>

        <footer>
            © 2024 BankPro. All rights reserved.<br>
            <a href="#">Facebook</a> • <a href="#">Twitter</a> • <a href="#">LinkedIn</a>
        </footer>
    </section>
</body>
</html>
