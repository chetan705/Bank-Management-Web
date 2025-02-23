<?php 
include 'includes/header.php'; 
include 'db/db_connection.php'; 
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $loan_id = $_POST['loan_id'];
    $action = $_POST['action'];
    $status = $action === 'approve' ? 'Approved' : 'Rejected';
    $update_loan_query = "UPDATE loans SET status = '$status' WHERE id = $loan_id";
    $conn->query($update_loan_query);
}

$loans_query = "SELECT * FROM loans WHERE status = 'Pending'";
$loans = $conn->query($loans_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Approvals</title>
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

        .loan-approvals {
            width: 100%;
            max-width: 1200px;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            margin: auto;
        }

        .loan-approvals h2 {
            font-size: 32px;
            color: #0056b3;
            margin-bottom: 30px;
            font-weight: 600;
            text-align: center;
        }

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

        .btn-approve, .btn-reject {
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-approve {
            background-color: #2ecc71;
            color: white;
        }

        .btn-approve:hover {
            background-color: #27ae60;
        }

        .btn-reject {
            background-color: #e74c3c;
            color: white;
        }

        .btn-reject:hover {
            background-color: #c0392b;
        }

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

        @media (max-width: 768px) {
            .loan-approvals {
                padding: 20px;
            }

            .table th, .table td {
                font-size: 14px;
                padding: 10px;
            }

            .btn-approve, .btn-reject {
                padding: 8px 12px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <section class="loan-approvals">
        <h2>Loan Approvals</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Loan ID</th>
                    <th>User ID</th>
                    <th>Amount</th>
                    <th>EMI</th>
                    <th>Duration</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($loan = $loans->fetch_assoc()): ?>
                    <tr>
                        <td><?= $loan['id'] ?></td>
                        <td><?= $loan['user_id'] ?></td>
                        <td>₹<?= number_format($loan['amount'], 2) ?></td>
                        <td>₹<?= number_format($loan['emi'], 2) ?></td>
                        <td><?= $loan['duration'] ?> months</td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="loan_id" value="<?= $loan['id'] ?>">
                                <button type="submit" name="action" value="approve" class="btn-approve">Approve</button>
                                <button type="submit" name="action" value="reject" class="btn-reject">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <p><a href="admin_dashboard.php">Back to Dashboard</a></p>
    </section>
</body>
</html>
