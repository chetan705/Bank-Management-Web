<?php  
include 'db/db_connection.php'; 
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    // Start transaction to ensure all deletions succeed together
    $conn->begin_transaction();

    try {
        // Delete dependent records first
        $conn->query("DELETE FROM transactions WHERE user_id = $user_id");
        $conn->query("DELETE FROM fixed_deposits WHERE user_id = $user_id");
        $conn->query("DELETE FROM loans WHERE user_id = $user_id");

        // Now delete the user
        $conn->query("DELETE FROM users WHERE id = $user_id");

        // Commit transaction
        $conn->commit();
    } catch (Exception $e) {
        // Rollback in case of an error
        $conn->rollback();
        echo "Error deleting user: " . $e->getMessage();
    }
}

$users_query = "SELECT * FROM users";
$users = $conn->query($users_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
            padding: 20px;
        }

        .manage-users {
            width: 100%;
            max-width: 1200px;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        }

        .manage-users h2 {
            font-size: 24px;
            color: #0056b3;
            margin-bottom: 20px;
            text-align: center;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .table thead {
            background-color: #0056b3;
            color: #fff;
        }

        .table th, .table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: #fff;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        a {
            display: block;
            text-align: center;
            color: #0056b3;
            text-decoration: none;
            margin-top: 15px;
        }

        a:hover {
            text-decoration: underline;
        }

        @media (max-width: 1024px) {
            .table th, .table td {
                font-size: 14px;
                padding: 8px;
            }
        }

        @media (max-width: 768px) {
            .table th, .table td {
                font-size: 12px;
                padding: 6px;
            }
        }

        @media (max-width: 450px) {
            .table-container {
                overflow-x: auto;
            }
            .table th, .table td {
                font-size: 10px;
                padding: 4px;
            }
            .btn-delete {
                padding: 3px 6px;
                font-size: 10px;
            }
        }

        @media (max-width: 200px) {
            .manage-users {
                padding: 15px;
            }
            .manage-users h2 {
                font-size: 16px;
                margin-bottom: 10px;
            }
            .table th, .table td {
                font-size: 8px;
                padding: 3px;
            }
            .btn-delete {
                padding: 2px 4px;
                font-size: 8px;
            }
            a {
                font-size: 10px;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <section class="manage-users">
        <h2>Manage Users</h2>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Account Type</th>
                        <th>Balance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['account_type']) ?></td>
                            <td>₹<?= number_format($user['balance'], 2) ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']) ?>">
                                    <button type="submit" name="delete_user" class="btn-delete">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <a href="admin_dashboard.php">Back to Dashboard</a>
    </section>
</body>
</html>
