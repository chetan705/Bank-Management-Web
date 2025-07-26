<?php
include 'db/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "User not found.";
    exit();
}
$user = $result->fetch_assoc();

$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';
$type_filter = $_POST['type_filter'] ?? '';

if (isset($_POST['download_report'])) {
    if (empty($start_date) || empty($end_date)) {
        echo "Please select both start and end dates.";
        exit();
    }
    if (strtotime($end_date) < strtotime($start_date)) {
        echo "End date cannot be before start date.";
        exit();
    }

    $query = "SELECT id, type, amount, date, category FROM transactions WHERE user_id = ? AND date BETWEEN ? AND ?";
    if (!empty($type_filter)) {
        $query .= " AND type = ?";
    }
    $stmt = $conn->prepare($query);
    if (!empty($type_filter)) {
        $stmt->bind_param("iss", $user_id, $start_date, $end_date, $type_filter);
    } else {
        $stmt->bind_param("iss", $user_id, $start_date, $end_date);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "No transactions found for the selected criteria.";
        exit();
    }

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="transaction_report_' . date('Ymd') . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Transaction ID', 'Type', 'Amount', 'Date', 'Category']);

    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            ucfirst($row['type']),
            number_format($row['amount'], 2),
            $row['date'],
            $row['category'] ?? 'Uncategorized'
        ]);
    }

    fclose($output);
    exit();
}

// Fetch transactions based on filters
$transaction_query = "SELECT id, type, amount, date, category FROM transactions WHERE user_id = ?";
if (!empty($start_date) && !empty($end_date)) {
    $transaction_query .= " AND date BETWEEN ? AND ?";
}
if (!empty($type_filter)) {
    $transaction_query .= " AND type = ?";
}
$transaction_query .= " ORDER BY date DESC";

$transaction_stmt = $conn->prepare($transaction_query);
if (!empty($start_date) && !empty($end_date) && !empty($type_filter)) {
    $transaction_stmt->bind_param("isss", $user_id, $start_date, $end_date, $type_filter);
} elseif (!empty($start_date) && !empty($end_date)) {
    $transaction_stmt->bind_param("iss", $user_id, $start_date, $end_date);
} elseif (!empty($type_filter)) {
    $transaction_stmt->bind_param("is", $user_id, $type_filter);
} else {
    $transaction_stmt->bind_param("i", $user_id);
}
$transaction_stmt->execute();
$transactions = $transaction_stmt->get_result();
$transaction_count = $transactions->num_rows;

// Prepare data for charts
$bar_data = ['Deposit' => 0, 'Withdrawal' => 0, 'Transfer' => 0];
$pie_data = [];
$line_data = [];
$transactions->data_seek(0);
while ($row = $transactions->fetch_assoc()) {
    $type = ucfirst($row['type']);
    $category = $row['category'] ?? 'Uncategorized';
    $amount = $row['amount'];
    $date = date('Y-m-d', strtotime($row['date']));

    // Bar chart: Sum by type
    if (isset($bar_data[$type])) {
        $bar_data[$type] += $amount;
    }

    // Pie chart: Sum by category
    $pie_data[$category] = ($pie_data[$category] ?? 0) + $amount;

    // Line chart: Sum by date
    $line_data[$date] = ($line_data[$date] ?? 0) + $amount;
}

// Sort line chart data by date
ksort($line_data);
$line_labels = json_encode(array_keys($line_data)) ?: json_encode(['No Data']);
$line_values = json_encode(array_values($line_data)) ?: json_encode([0]);

// Convert to JSON for Chart.js, with fallback for empty data
$bar_labels = json_encode(array_keys($bar_data)) ?: json_encode(['No Data']);
$bar_values = json_encode(array_values($bar_data)) ?: json_encode([0]);
$pie_labels = json_encode(array_keys($pie_data)) ?: json_encode(['No Data']);
$pie_values = json_encode(array_values($pie_data)) ?: json_encode([0]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction Analytics - Bank Management</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
    <style>
        .filter-container {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        .filter-container select, .filter-container input, .filter-container button {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 14px;
        }
        .filter-container button {
            background-color: #00509e;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .filter-container button:hover {
            background-color: #003f7d;
        }
        .chart-container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
        }
        @media (max-width: 768px) {
            .chart-container {
                max-width: 100%;
            }
            .filter-container {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>
    <header>
        <img src="assests/download1.jpg" alt="Bank Logo" class="logo">
        <h1>Transaction Analytics</h1>
    </header>
    <nav>
        <div class="hamburger">
            <i class="fas fa-bars"></i>
        </div>
        <ul class="nav-menu">
            <li><a href="dashboard.php">Home</a></li>
            <li><a href="loan_management.php">Loan Management</a></li>
            <li><a href="fixed_deposit_management.php">Fixed Deposits</a></li>
            <li><a href="logout.php?role=user">Logout</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="analytics">
            <div class="card transactions">
                <h3>Transaction History (<?php echo $transaction_count; ?>)</h3>
                <form method="post" class="filter-container">
                    <select name="type_filter" id="type_filter">
                        <option value="" <?php echo $type_filter === '' ? 'selected' : ''; ?>>All Types</option>
                        <option value="deposit" <?php echo $type_filter === 'deposit' ? 'selected' : ''; ?>>Deposit</option>
                        <option value="withdrawal" <?php echo $type_filter === 'withdrawal' ? 'selected' : ''; ?>>Withdrawal</option>
                        <option value="transfer" <?php echo $type_filter === 'transfer' ? 'selected' : ''; ?>>Transfer</option>
                    </select>
                    <input type="date" name="start_date" id="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                    <input type="date" name="end_date" id="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                    <button type="submit" name="filter">Filter</button>
                    <button type="submit" name="download_report">Download Report</button>
                </form>
                <?php if ($transactions->num_rows > 0): ?>
                    <table id="transactionTable">
                        <thead>
                            <tr>
                                <th>Transaction ID</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $transactions->data_seek(0);
                            while ($transaction = $transactions->fetch_assoc()): ?>
                                <tr data-type="<?php echo htmlspecialchars($transaction['type']); ?>" data-date="<?php echo htmlspecialchars($transaction['date']); ?>">
                                    <td><?php echo htmlspecialchars($transaction['id']); ?></td>
                                    <td><?php echo ucfirst(htmlspecialchars($transaction['type'])); ?></td>
                                    <td>₹<?php echo number_format($transaction['amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['date']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['category'] ?? 'Uncategorized'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No transactions found.</p>
                <?php endif; ?>
            </div>

            <div class="card chart">
                <h3>Transaction Amounts by Type</h3>
                <div class="chart-container">
                    <?php if (array_sum($bar_data) > 0): ?>
                        <canvas id="barChart"></canvas>
                    <?php else: ?>
                        <p class="no-data">No data available for bar chart.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card chart">
                <h3>Transaction Distribution by Category</h3>
                <div class="chart-container">
                    <?php if (!empty($pie_data)): ?>
                        <canvas id="pieChart"></canvas>
                    <?php else: ?>
                        <p class="no-data">No data available for pie chart.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card chart">
                <h3>Transaction Trends Over Time</h3>
                <div class="chart-container">
                    <?php if (!empty($line_data)): ?>
                        <canvas id="lineChart"></canvas>
                    <?php else: ?>
                        <p class="no-data">No data available for line chart.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>Bank Management System © 2024</p>
    </footer>

    <script>
        // Hamburger menu toggle
        document.querySelector('.hamburger').addEventListener('click', () => {
            document.querySelector('.nav-menu').classList.toggle('active');
        });

        // Debug chart data
        console.log('Bar Labels:', <?php echo $bar_labels; ?>);
        console.log('Bar Values:', <?php echo $bar_values; ?>);
        console.log('Pie Labels:', <?php echo $pie_labels; ?>);
        console.log('Pie Values:', <?php echo $pie_values; ?>);
        console.log('Line Labels:', <?php echo $line_labels; ?>);
        console.log('Line Values:', <?php echo $line_values; ?>);

        // Chart.js setup
        <?php if (array_sum($bar_data) > 0): ?>
        const barCtx = document.getElementById('barChart').getContext('2d');
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: <?php echo $bar_labels; ?>,
                datasets: [{
                    label: 'Amount (₹)',
                    data: <?php echo $bar_values; ?>,
                    backgroundColor: ['#00509e', '#ff6f61', '#28a745'],
                    borderColor: ['#003f7d', '#d55b50', '#218838'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Amount (₹)' } }
                }
            }
        });
        <?php endif; ?>

        <?php if (!empty($pie_data)): ?>
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: <?php echo $pie_labels; ?>,
                datasets: [{
                    data: <?php echo $pie_values; ?>,
                    backgroundColor: ['#007bff', '#dc3545', '#28a745', '#ffc107', '#6f42c1', '#fd7e14'],
                    borderColor: ['#fff'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let sum = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = ((context.parsed / sum) * 100).toFixed(2);
                                return `${context.label}: ₹${context.parsed.toLocaleString()} (${percentage}%)`;
                            }
                        }
                    },
                    datalabels: {
                        formatter: (value, ctx) => {
                            let sum = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = ((value / sum) * 100).toFixed(2) + '%';
                            return percentage;
                        },
                        color: '#fff',
                        font: { weight: 'bold' }
                    }
                }
            },
            plugins: [ChartDataLabels]
        });
        <?php endif; ?>

        <?php if (!empty($line_data)): ?>
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: <?php echo $line_labels; ?>,
                datasets: [{
                    label: 'Total Amount (₹)',
                    data: <?php echo $line_values; ?>,
                    borderColor: '#00509e',
                    backgroundColor: 'rgba(0, 80, 158, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: { title: { display: true, text: 'Date' } },
                    y: { 
                        beginAtZero: true, 
                        title: { display: true, text: 'Amount (₹)' }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `₹${context.parsed.y.toLocaleString()}`;
                            }
                        }
                    }
                }
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>