<?php 
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

    if ($action === 'approve') {
        $conn->begin_transaction();

        $loan_query = "SELECT user_id, amount, remaining_amount FROM loans WHERE id = ?";
        $loan_stmt = $conn->prepare($loan_query);
        $loan_stmt->bind_param("i", $loan_id);
        $loan_stmt->execute();
        $loan = $loan_stmt->get_result()->fetch_assoc();

        if ($loan) {
            $user_id = $loan['user_id'];
            $loan_amount = $loan['amount'];
            $remaining_amount = $loan['remaining_amount'] ?? $loan_amount;

            $update_loan_query = "UPDATE loans SET status = ?, remaining_amount = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_loan_query);
            $update_stmt->bind_param("sdi", $status, $remaining_amount, $loan_id);
            $update_stmt->execute();

            $update_user_query = "UPDATE users SET balance = balance + ? WHERE id = ?";
            $user_stmt = $conn->prepare($update_user_query);
            $user_stmt->bind_param("di", $loan_amount, $user_id);
            $user_stmt->execute();

            $conn->commit();
        } else {
            $conn->rollback();
        }
    } else {
        $update_loan_query = "UPDATE loans SET status = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_loan_query);
        $update_stmt->bind_param("si", $status, $loan_id);
        $update_stmt->execute();
    }
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
            min-width: 80px;
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

        @media (max-width: 1200px) {
            .loan-approvals {
                max-width: 90%;
                padding: 30px;
            }
            .table th, .table td {
                padding: 12px;
                min-width: 70px;
                font-size: 16px;
            }
            .btn-approve, .btn-reject {
                padding: 8px 12px;
                font-size: 14px;
            }
        }

        @media (max-width: 768px) {
            .loan-approvals {
                max-width: 95%;
                padding: 20px;
            }
            .loan-approvals h2 {
                font-size: 28px;
                margin-bottom: 20px;
            }
            .table th, .table td {
                padding: 10px;
                min-width: 60px;
                font-size: 14px;
            }
            .btn-approve, .btn-reject {
                padding: 6px 10px;
                font-size: 12px;
            }
            a {
                font-size: 14px;
                margin-top: 15px;
            }
        }

        @media (max-width: 480px) {
            .loan-approvals {
                max-width: 95%;
                padding: 15px;
            }
            .loan-approvals h2 {
                font-size: 24px;
                margin-bottom: 15px;
            }
            .table th, .table td {
                padding: 8px;
                min-width: 50px;
                font-size: 12px;
            }
            .btn-approve, .btn-reject {
                padding: 5px 8px;
                font-size: 10px;
            }
            a {
                font-size: 13px;
                margin-top: 12px;
            }
        }

        @media (max-width: 200px) {
            .loan-approvals {
                max-width: 100%;
                padding: 10px;
            }
            .loan-approvals h2 {
                font-size: 18px;
                margin-bottom: 10px;
            }
            .table th, .table td {
                padding: 5px;
                min-width: 30px;
                font-size: 8px;
            }
            .btn-approve, .btn-reject {
                padding: 3px 6px;
                font-size: 8px;
            }
            a {
                font-size: 10px;
                margin-top: 8px;
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