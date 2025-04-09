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
            animation: scaleIn 0.6s ease-out;
        }

        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
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
            animation: flipIn 0.7s ease-out forwards;
        }

        .admin-card:nth-child(1) { animation-delay: 0.2s; }
        .admin-card:nth-child(2) { animation-delay: 0.4s; }
        .admin-card:nth-child(3) { animation-delay: 0.6s; }
        .admin-card:nth-child(4) { animation-delay: 0.8s; }

        @keyframes flipIn {
            from { opacity: 0; transform: rotateX(-90deg); }
            to { opacity: 1; transform: rotateX(0); }
        }

        .admin-card:hover {
            transform: translateY(-5px) scale(1.03);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            animation: bounce 0.5s ease infinite alternate;
        }

        @keyframes bounce {
            from { transform: translateY(-5px) scale(1.03); }
            to { transform: translateY(-10px) scale(1.03); }
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
            transition: background-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        .admin-card a:hover {
            background-color: #003366;
            transform: scale(1.05);
            box-shadow: 0 0 10px rgba(0, 86, 179, 0.7);
            animation: pulseGlow 0.6s infinite alternate;
        }

        @keyframes pulseGlow {
            from { transform: scale(1.05); box-shadow: 0 0 10px rgba(0, 86, 179, 0.7); }
            to { transform: scale(1.08); box-shadow: 0 0 15px rgba(0, 86, 179, 0.9); }
        }

        a.logout {
            display: inline-block;
            text-decoration: none;
            color: #1e90ff;
            font-size: 16px;
            margin-top: 30px;
            font-weight: 500;
            position: relative;
            transition: color 0.3s ease;
            animation: slideInLeft 0.5s ease-out;
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }

        a.logout::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background-color: #003366;
            transition: width 0.3s ease;
        }

        a.logout:hover::after {
            width: 100%;
        }

        a.logout:hover {
            color: #003366;
        }

        footer {
            margin-top: 30px;
            text-align: center;
            font-size: 14px;
            color: grey;
            font-weight: 400;
            animation: slideInUp 0.5s ease-out;
        }

        footer a {
            color: #1e90ff;
            text-decoration: none;
            margin: 0 12px;
            font-size: 16px;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color:white;
            text-decoration: underline;
        }

        @media (max-width: 1200px) {
            .admin-dashboard {
                max-width: 90%;
                padding: 30px;
            }
            .admin-content {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
            }
            .admin-card a {
                font-size: 16px;
                padding: 12px 18px;
            }
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
            .admin-dashboard h2 {
                font-size: 24px;
            }
            footer {
                font-size: 12px;
            }
            footer a {
                font-size: 14px;
                margin: 0 8px;
            }
        }

        @media (max-width: 480px) {
            .admin-dashboard {
                padding: 15px;
            }
            .admin-card {
                padding: 15px;
            }
            .admin-card a {
                font-size: 14px;
                padding: 8px 12px;
            }
            .admin-dashboard h2 {
                font-size: 20px;
                margin-bottom: 20px;
            }
            a.logout {
                font-size: 14px;
                margin-top: 20px;
            }
            footer {
                font-size: 10px;
            }
            footer a {
                font-size: 12px;
                margin: 0 6px;
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