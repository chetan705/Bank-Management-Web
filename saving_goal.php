<?php
include 'db/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle form submission (create/edit)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'create' || $_POST['action'] == 'edit') {
        $goal_name = $_POST['goal_name'];
        $target_amount = floatval($_POST['target_amount']);
        $target_date = $_POST['target_date'];
        if ($target_amount > 0 && !empty($goal_name) && strtotime($target_date) > time()) {
            if ($_POST['action'] == 'create') {
                $query = "INSERT INTO savings_goals (user_id, goal_name, target_amount, target_date) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("isds", $user_id, $goal_name, $target_amount, $target_date);
            } else {
                $goal_id = intval($_POST['goal_id']);
                $query = "UPDATE savings_goals SET goal_name = ?, target_amount = ?, target_date = ? WHERE id = ? AND user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sdsii", $goal_name, $target_amount, $target_date, $goal_id, $user_id);
            }
            $stmt->execute();
            echo "<script>alert('Goal saved!'); window.location.href='saving_goal.php';</script>";
        } else {
            echo "<script>alert('Invalid inputs.');</script>";
        }
    } elseif ($_POST['action'] == 'delete') {
        $goal_id = intval($_POST['goal_id']);
        $query = "DELETE FROM savings_goals WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $goal_id, $user_id);
        $stmt->execute();
        echo "<script>alert('Goal deleted!'); window.location.href='saving_goal.php';</script>";
    }
}

// Update current_amount
$goals_query = "SELECT id, target_amount, target_date FROM savings_goals WHERE user_id = ? AND status = 'Active'";
$goals_stmt = $conn->prepare($goals_query);
$goals_stmt->bind_param("i", $user_id);
$goals_stmt->execute();
$goals_result = $goals_stmt->get_result();

while ($goal = $goals_result->fetch_assoc()) {
    $deposit_query = "SELECT SUM(amount) as total FROM transactions WHERE user_id = ? AND type = 'deposit' AND date <= ?";
    $deposit_stmt = $conn->prepare($deposit_query);
    $deposit_stmt->bind_param("is", $user_id, $goal['target_date']);
    $deposit_stmt->execute();
    $total_deposits = $deposit_stmt->get_result()->fetch_assoc()['total'] ?: 0;

    $current_amount = min($total_deposits, $goal['target_amount']);
    $update_query = "UPDATE savings_goals SET current_amount = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("di", $current_amount, $goal['id']);
    $update_stmt->execute();

    if ($current_amount >= $goal['target_amount']) {
        $status_query = "UPDATE savings_goals SET status = 'Completed' WHERE id = ?";
        $status_stmt = $conn->prepare($status_query);
        $status_stmt->bind_param("i", $goal['id']);
        $status_stmt->execute();
    }
}

// Fetch goals
$goals_query = "SELECT * FROM savings_goals WHERE user_id = ? ORDER BY target_date";
$goals_stmt = $conn->prepare($goals_query);
$goals_stmt->bind_param("i", $user_id);
$goals_stmt->execute();
$goals = $goals_stmt->get_result();

// Motivational quotes
$quotes = [
    "Small steps lead to big savings!",
    "Your financial future starts today!",
    "Every rupee saved is a step closer to your dreams!"
];
$random_quote = $quotes[array_rand($quotes)];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saving Goal Tracker</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #f4f7fa; color: #333; }
        header { background: linear-gradient(90deg, #003087, #005b96); color: white; padding: 2rem; text-align: center; }
        header h1 { font-size: 2.2rem; font-weight: 600; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); padding: 1.5rem; margin-bottom: 2rem; transition: transform 0.3s; }
        .card:hover { transform: translateY(-5px); }
        .btn { background: #005b96; color: white; padding: 0.8rem 1.5rem; border: none; border-radius: 8px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: 500; }
        .btn:hover { background: #003087; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 0.5rem; }
        .form-group input, .form-group select { width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; }
        .goal-card { display: flex; flex-wrap: wrap; gap: 1rem; }
        .goal-item { flex: 1 1 300px; background: #f9f9f9; border-radius: 12px; padding: 1rem; position: relative; transition: transform 0.5s; }
        .goal-item:hover { transform: rotateY(10deg); }
        .progress-circle { width: 100px; height: 100px; margin: 1rem auto; }
        .quote { text-align: center; font-style: italic; color: #005b96; margin: 1rem 0; }
        .calculator { margin-top: 1rem; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; margin: 10% auto; padding: 2rem; width: 90%; max-width: 500px; border-radius: 12px; }
        .close { float: right; font-size: 1.5rem; cursor: pointer; }
        @media (max-width: 768px) { .goal-item { flex: 1 1 100%; } }
    </style>
</head>
<body>
<header>
    <h1>Saving Goal Tracker</h1>
</header>
<div class="container">
    <div class="card">
        <h2>Create New Goal</h2>
        <form method="post">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label for="goal_name">Goal Name</label>
                <input type="text" name="goal_name" required>
            </div>
            <div class="form-group">
                <label for="target_amount">Target Amount (₹)</label>
                <input type="number" name="target_amount" min="1" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="target_date">Target Date</label>
                <input type="date" name="target_date" required>
            </div>
            <button type="submit" class="btn">Add Goal</button>
        </form>
        <div class="calculator">
            <h3>Savings Calculator</h3>
            <input type="number" id="calc_amount" placeholder="Target Amount" min="1">
            <input type="number" id="calc_months" placeholder="Months" min="1">
            <button onclick="calculateSavings()" class="btn">Calculate</button>
            <p id="calc_result"></p>
        </div>
    </div>

    <div class="card">
        <h2>Your Goals</h2>
        <p class="quote">"<?php echo htmlspecialchars($random_quote); ?>"</p>
        <?php if ($goals->num_rows > 0): ?>
            <div class="goal-card">
                <?php while ($goal = $goals->fetch_assoc()): ?>
                    <?php $progress = ($goal['current_amount'] / $goal['target_amount']) * 100; ?>
                    <div class="goal-item">
                        <h3><?php echo htmlspecialchars($goal['goal_name']); ?></h3>
                        <p>Target: ₹<?php echo number_format($goal['target_amount'], 2); ?></p>
                        <p>Current: ₹<?php echo number_format($goal['current_amount'], 2); ?></p>
                        <p>Date: <?php echo $goal['target_date']; ?></p>
                        <p>Status: <?php echo $goal['status']; ?></p>
                        <canvas class="progress-circle" id="progress_<?php echo $goal['id']; ?>"></canvas>
                        <button onclick="openEditModal(<?php echo $goal['id']; ?>, '<?php echo htmlspecialchars($goal['goal_name']); ?>', <?php echo $goal['target_amount']; ?>, '<?php echo $goal['target_date']; ?>')" class="btn">Edit</button>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="goal_id" value="<?php echo $goal['id']; ?>">
                            <button type="submit" class="btn" style="background:#dc3545;">Delete</button>
                        </form>
                        <script>
                            new Chart(document.getElementById('progress_<?php echo $goal['id']; ?>'), {
                                type: 'doughnut',
                                data: {
                                    datasets: [{ data: [<?php echo $progress; ?>, <?php echo 100 - $progress; ?>], backgroundColor: ['#005b96', '#ddd'] }],
                                    labels: ['Progress', 'Remaining']
                                },
                                options: { cutout: '70%', plugins: { legend: { display: false } } }
                            });
                        </script>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No goals found. Start by creating one!</p>
        <?php endif; ?>
    </div>
    <p><a href="dashboard.php" class="btn">Back to Dashboard</a></p>
</div>

<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeEditModal()">×</span>
        <h2>Edit Goal</h2>
        <form method="post">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="goal_id" id="edit_goal_id">
            <div class="form-group">
                <label for="edit_goal_name">Goal Name</label>
                <input type="text" name="goal_name" id="edit_goal_name" required>
            </div>
            <div class="form-group">
                <label for="edit_target_amount">Target Amount (₹)</label>
                <input type="number" name="target_amount" id="edit_target_amount" min="1" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="edit_target_date">Target Date</label>
                <input type="date" name="target_date" id="edit_target_date" required>
            </div>
            <button type="submit" class="btn">Save Changes</button>
        </form>
    </div>
</div>

<script>
function calculateSavings() {
    const amount = parseFloat(document.getElementById('calc_amount').value);
    const months = parseInt(document.getElementById('calc_months').value);
    if (amount > 0 && months > 0) {
        const monthly = (amount / months).toFixed(2);
        document.getElementById('calc_result').innerText = `Save ₹${monthly}/month for ${months} months.`;
    } else {
        document.getElementById('calc_result').innerText = 'Please enter valid values.';
    }
}
function openEditModal(id, name, amount, date) {
    document.getElementById('edit_goal_id').value = id;
    document.getElementById('edit_goal_name').value = name;
    document.getElementById('edit_target_amount').value = amount;
    document.getElementById('edit_target_date').value = date;
    document.getElementById('editModal').style.display = 'block';
}
function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>
</body>
</html>