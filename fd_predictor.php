<?php
include 'db/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$result = null;
$suggestions = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['calculate'])) {
    $amount = floatval($_POST['amount']);
    $tenure = intval($_POST['tenure']);
    $interest_rate = floatval($_POST['interest_rate']);

    if ($amount > 0 && $tenure > 0 && $interest_rate > 0) {
        $maturity_value = $amount * pow(1 + $interest_rate / 100, $tenure / 12);
        $result = [
            'amount' => $amount,
            'tenure' => $tenure,
            'interest_rate' => $interest_rate,
            'maturity_value' => $maturity_value
        ];

        // Suggestions
        $suggestions[] = ['tenure' => $tenure, 'rate' => $interest_rate, 'maturity' => $maturity_value];
        $suggestions[] = ['tenure' => $tenure + 12, 'rate' => $interest_rate + 0.5, 'maturity' => $amount * pow(1 + ($interest_rate + 0.5) / 100, ($tenure + 12) / 12)];
        $suggestions[] = ['tenure' => $tenure - 6, 'rate' => $interest_rate - 0.5, 'maturity' => $amount * pow(1 + ($interest_rate - 0.5) / 100, ($tenure - 6) / 12)];

        // Dynamic rate suggestion
        $rate_suggestion = $amount > 50000 ? "Consider 5.5% for amounts above ₹50,000." : "Try 4.5% for smaller deposits.";
    } else {
        echo "<script>alert('Invalid inputs.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FD Maturity Predictor</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background: #f4f7fa; color: #333; }
        header { background: linear-gradient(90deg, #003087, #005b96); color: white; padding: 2rem; text-align: center; }
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 1rem; }
        .card { background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); padding: 1.5rem; margin-bottom: 2rem; }
        .btn { 
            background: #005b96; 
            color: white; 
            padding: 0.8rem 1.5rem; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 500; 
            text-decoration: none; 
            display: inline-block; 
            transition: transform 0.2s, background 0.2s; 
        }
        .btn:hover { 
            background: #003087; 
            transform: scale(1.05); 
        }
        .btn-download { margin-bottom: 1rem; } /* Spacing for Download Summary */
        .btn-fd { margin-top: 0.5rem; } /* Spacing for Open New FD */
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-weight: 500; margin-bottom: 0.5rem; }
        .form-group input, .slider { width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 1rem; border: 1px solid #ddd; text-align: left; }
        .suggestion { background: #e6f0fa; padding: 1rem; border-radius: 8px; margin: 1rem 0; }
        .preview { background: #e6f0fa; padding: 1rem; border-radius: 8px; margin: 1rem 0; text-align: center; }
    </style>
</head>
<body>
<header>
    <h1>FD Maturity Predictor</h1>
</header>
<div class="container">
    <div class="card">
        <h2>Predict Your FD</h2>
        <form method="post" id="fdForm">
            <input type="hidden" name="calculate" value="1">
            <div class="form-group">
                <label for="amount">Amount (₹)</label>
                <input type="range" class="slider" id="amount" name="amount" min="1000" max="1000000" value="10000" oninput="updateValue('amount', this.value); calculatePreview()">
                <span id="amount_value">₹10,000</span>
            </div>
            <div class="form-group">
                <label for="tenure">Tenure (Months)</label>
                <input type="range" class="slider" id="tenure" name="tenure" min="6" max="60" value="12" oninput="updateValue('tenure', this.value); calculatePreview()">
                <span id="tenure_value">12 months</span>
            </div>
            <div class="form-group">
                <label for="interest_rate">Interest Rate (%)</label>
                <input type="number" id="interest_rate" name="interest_rate" value="5" step="0.1" required oninput="calculatePreview()">
            </div>
            <div class="preview" id="preview">Estimated Maturity: ₹10,500.00</div>
            <button type="submit" class="btn">Calculate</button>
        </form>
    </div>

    <?php if ($result): ?>
        <div class="card">
            <h2>Results</h2>
            <table>
                <tr><th>Parameter</th><th>Value</th></tr>
                <tr><td>Amount</td><td>₹<?php echo number_format($result['amount'], 2); ?></td></tr>
                <tr><td>Tenure</td><td><?php echo $result['tenure']; ?> months</td></tr>
                <tr><td>Interest Rate</td><td><?php echo $result['interest_rate']; ?>%</td></tr>
                <tr><td>Maturity Value</td><td>₹<?php echo number_format($result['maturity_value'], 2); ?></td></tr>
            </table>
            <p class="suggestion"><?php echo $rate_suggestion; ?></p>
            <h3>Compare Scenarios</h3>
            <table>
                <tr><th>Tenure</th><th>Rate</th><th>Maturity</th></tr>
                <?php foreach ($suggestions as $sug): ?>
                    <tr>
                        <td><?php echo $sug['tenure']; ?> months</td>
                        <td><?php echo $sug['rate']; ?>%</td>
                        <td>₹<?php echo number_format($sug['maturity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <button onclick="downloadSummary()" class="btn btn-download">Download Summary</button>
            <p class="btn-fd"><a href="fixed_deposit.php" class="btn">Open New FD</a></p>
        </div>
    <?php endif; ?>
    <p><a href="dashboard.php" class="btn">Back to Dashboard</a></p>
</div>
<script>
function updateValue(id, value) {
    document.getElementById(`${id}_value`).innerText = id === 'amount' ? `₹${parseInt(value).toLocaleString()}` : `${value} months`;
}
function calculatePreview() {
    const amount = parseFloat(document.getElementById('amount').value);
    const tenure = parseInt(document.getElementById('tenure').value);
    const rate = parseFloat(document.getElementById('interest_rate').value) || 5;
    if (amount > 0 && tenure > 0 && rate > 0) {
        const maturity = amount * Math.pow(1 + rate / 100, tenure / 12);
        document.getElementById('preview').innerText = `Estimated Maturity: ₹${maturity.toLocaleString('en-IN', { minimumFractionDigits: 2 })}`;
    } else {
        document.getElementById('preview').innerText = 'Enter valid values';
    }
}
function downloadSummary() {
    const result = <?php echo json_encode($result ?: []); ?>;
    const suggestions = <?php echo json_encode($suggestions ?: []); ?>;
    let content = `FD Maturity Prediction\n\n`;
    content += `Amount: ₹${result.amount ? result.amount.toLocaleString('en-IN', { minimumFractionDigits: 2 }) : ''}\n`;
    content += `Tenure: ${result.tenure} months\n`;
    content += `Interest Rate: ${result.interest_rate}%\n`;
    content += `Maturity Value: ₹${result.maturity_value ? result.maturity_value.toLocaleString('en-IN', { minimumFractionDigits: 2 }) : ''}\n\n`;
    content += `Comparison Scenarios:\n`;
    suggestions.forEach(sug => {
        content += `- Tenure: ${sug.tenure} months, Rate: ${sug.rate}%, Maturity: ₹${sug.maturity.toLocaleString('en-IN', { minimumFractionDigits: 2 })}\n`;
    });
    const blob = new Blob([content], { type: 'text/plain' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'fd_prediction.txt';
    a.click();
    URL.revokeObjectURL(url);
}
calculatePreview(); // Initial preview
</script>
</body>
</html>