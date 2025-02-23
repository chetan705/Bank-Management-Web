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

$fixed_deposits_query = "SELECT * FROM fixed_deposits WHERE user_id = {$user['id']}";
$fixed_deposits = $conn->query($fixed_deposits_query);
?>

<section class="form-container">
    <h2>My Fixed Deposits</h2>
    <table class="table">
        <thead>
            <tr>
                <th>FD ID</th>
                <th>Amount</th>
                <th>Tenure (Months)</th>
                <th>Maturity Amount</th>
                <th>Maturity Date</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($fd = $fixed_deposits->fetch_assoc()): ?>
                <tr>
                    <td><?= $fd['id'] ?></td>
                    <td>₹<?= $fd['amount'] ?></td>
                    <td><?= $fd['tenure'] ?></td>
                    <td>₹<?= round($fd['maturity_amount'], 2) ?></td>
                    <td><?= $fd['maturity_date'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</section>

<?php include 'includes/footer.php'; ?>
