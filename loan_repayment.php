<?php 
include 'includes/header.php'; 
include 'db/db_connection.php'; 
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$email = $_SESSION['user'];
$query = "SELECT * FROM users WHERE email = '$email'";
$result = $conn->query($query);
$user = $result->fetch_assoc();

$loan_query = "SELECT * FROM loans WHERE user_id = {$user['id']} AND status != 'Paid'";
$loans = $conn->query($loan_query);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $loan_id = $_POST['loan_id'];
    $repayment_amount = $_POST['repayment_amount'];

    $loan_data = $conn->query("SELECT * FROM loans WHERE id = $loan_id")->fetch_assoc();

    if ($repayment_amount > $user['balance']) {
        $error = "Insufficient balance. Please try again.";
    } elseif ($repayment_amount > $loan_data['amount']) {
        $error = "Repayment exceeds remaining loan amount.";
    } else {
        $new_balance = $user['balance'] - $repayment_amount;
        $remaining_loan = $loan_data['amount'] - $repayment_amount;

        $update_user_query = "UPDATE users SET balance = $new_balance WHERE id = {$user['id']}";
        $update_loan_query = $remaining_loan == 0 ? 
            "UPDATE loans SET amount = 0, status = 'Paid' WHERE id = $loan_id" :
            "UPDATE loans SET amount = $remaining_loan WHERE id = $loan_id";

        if ($conn->query($update_user_query) && $conn->query($update_loan_query)) {
            header("Location: dashboard.php?success=Repayment successful");
        } else {
            $error = "Repayment failed. Please try again.";
        }
    }
}
?>

<section class="form-container">
    <form method="POST">
        <h2>Repay Loan</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <label>Select Loan:</label>
        <select name="loan_id" required>
            <?php while ($loan = $loans->fetch_assoc()): ?>
                <option value="<?= $loan['id'] ?>">
                    Loan ID: <?= $loan['id'] ?> | Amount: ₹<?= $loan['amount'] ?> | EMI: ₹<?= $loan['emi'] ?>
                </option>
            <?php endwhile; ?>
        </select>
        <label>Repayment Amount:</label>
        <input type="number" name="repayment_amount" min="1" required>
        <button type="submit">Repay</button>
        <p><a href="dashboard.php">Back to Dashboard</a></p>
    </form>
</section>

<?php include 'includes/footer.php'; ?>
