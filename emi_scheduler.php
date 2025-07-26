<?php
include 'db/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch active loans
$loan_query = "SELECT * FROM loans WHERE user_id = ? AND status = 'Approved'";
$loan_stmt = $conn->prepare($loan_query);
$loan_stmt->bind_param("i", $user_id);
$loan_stmt->execute();
$loans = $loan_stmt->get_result();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $loan_id = $_POST['loan_id'];
    $emi_amount = floatval($_POST['emi_amount']);
    $due_date = $_POST['due_date'];
    $auto_deduct = isset($_POST['auto_deduct']) ? 1 : 0;

    // Validate inputs
    $loan_check = $conn->prepare("SELECT remaining_amount FROM loans WHERE id = ? AND user_id = ?");
    $loan_check->bind_param("ii", $loan_id, $user_id);
    $loan_check->execute();
    $loan = $loan_check->get_result()->fetch_assoc();

    if ($loan && $emi_amount > 0 && $emi_amount <= $loan['remaining_amount'] && strtotime($due_date) > time()) {
        $insert_query = "INSERT INTO loan_schedules (loan_id, user_id, emi_amount, due_date, auto_deduct) VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $conn->prepare($insert_query);
        $insert_stmt->bind_param("iidsi", $loan_id, $user_id, $emi_amount, $due_date, $auto_deduct);
        $insert_stmt->execute();
        echo "<script>alert('EMI schedule added successfully!'); window.location.href='emi_scheduler.php';</script>";
    } else {
        echo "<script>alert('Invalid EMI amount or due date.');</script>";
    }
}

// Fetch existing schedules
$schedule_query = "SELECT ls.*, l.amount FROM loan_schedules ls JOIN loans l ON ls.loan_id = l.id WHERE ls.user_id = ?";
$schedule_stmt = $conn->prepare($schedule_query);
$schedule_stmt->bind_param("i", $user_id);
$schedule_stmt->execute();
$schedules = $schedule_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMI Scheduler</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f4f4f9; }
        header { background: #007bff; color: #fff; padding: 1rem; text-align: center; }
        .container { padding: 1rem; max-width: 800px; margin: 0 auto; }
        .card { background: #fff; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); }
        .btn { padding: 0.5rem 1rem; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        input, select { padding: 0.5rem; margin: 0.5rem 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 0.5rem; border: 1px solid #ddd; }
        h2 { color: #007bff; text-align: center; }
    </style>
</head>
<body>
<header>
    <h1>EMI Scheduler</h1>
</header>
<div class="container">
    <h2>Create EMI Schedule</h2>
    <form method="post">
        <label for="loan_id">Select Loan:</label>
        <select name="loan_id" required>
            <?php while ($loan = $loans->fetch_assoc()): ?>
                <option value="<?= $loan['id'] ?>">Loan ID: <?= $loan['id'] ?> (₹<?= number_format($loan['amount'], 2) ?>)</option>
            <?php endwhile; ?>
        </select><br>
        <label for="emi_amount">EMI Amount:</label>
        <input type="number" name="emi_amount" min="1" step="0.01" required><br>
        <label for="due_date">Due Date:</label>
        <input type="date" name="due_date" required><br>
        <label><input type="checkbox" name="auto_deduct"> Enable Auto-Deduction</label><br>
        <button type="submit" class="btn">Add Schedule</button>
    </form>

    <h2>Existing Schedules</h2>
    <?php if ($schedules->num_rows > 0): ?>
        <table>
            <tr><th>Loan ID</th><th>EMI Amount</th><th>Due Date</th><th>Status</th><th>Auto-Deduct</th></tr>
            <?php while ($schedule = $schedules->fetch_assoc()): ?>
                <tr>
                    <td><?= $schedule['loan_id'] ?></td>
                    <td>₹<?= number_format($schedule['emi_amount'], 2) ?></td>
                    <td><?= $schedule['due_date'] ?></td>
                    <td><?= $schedule['status'] ?></td>
                    <td><?= $schedule['auto_deduct'] ? 'Yes' : 'No' ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No schedules found.</p>
    <?php endif; ?>
    <p><a href="dashboard.php" class="btn">Back to Dashboard</a></p>
</div>
</body>
</html>