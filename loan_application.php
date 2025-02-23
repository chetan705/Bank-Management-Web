<?php 
include 'db/db_connection.php'; 
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $loan_amount = $_POST['loan_amount'];
    $duration = $_POST['duration'];
    $loan_type = $_POST['loan_type'];
    $loan_purpose = $_POST['loan_purpose']; 
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone']; 
    
    $interest_rate = 10; 
    $emi = round(($loan_amount * (1 + $interest_rate / 100)) / $duration, 2); // EMI calculation

    $loan_query = $conn->prepare("INSERT INTO loans (user_id, amount, interest_rate, duration, emi, status, loan_type, loan_purpose, name, email, phone) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $status = 'Pending'; 
    $loan_query->bind_param("idiiissssss", $user_id, $loan_amount, $interest_rate, $duration, $emi, $status, $loan_type, $loan_purpose, $name, $email, $phone);

    if ($loan_query->execute()) {
        header("Location: dashboard.php?success=Loan application submitted successfully");
        exit;
    } else {
        $error = "Failed to apply for a loan. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Application - Bank Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 10px;
        }

        .error {
            color: red;
            font-weight: bold;
        }

        .success {
            color: green;
            font-weight: bold;
        }

        form {
            margin-top: 20px;
        }

        label {
            font-weight: bold;
        }

        input, select {
            margin-bottom: 10px;
            padding: 5px;
        }

        button {
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #f4f4f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container h1 {
            text-align: center;
        }

        .form-container input, .form-container select {
            width: 100%;
            max-width: 100%;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h1>Loan Application</h1>

        <?php if (!empty($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <p class="success"><?= $_GET['success'] ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="name">Full Name:</label>
            <input type="text" name="name" required><br><br>

            <label for="email">Email:</label>
            <input type="email" name="email" required><br><br>

            <label for="phone">Phone Number:</label>
            <input type="tel" name="phone" required><br><br>

            <label for="loan_amount">Loan Amount:</label>
            <input type="number" name="loan_amount" min="1000" required><br><br>

            <label for="duration">Duration (Months):</label>
            <input type="number" name="duration" min="1" required><br><br>

            <label for="loan_type">Loan Type:</label>
            <select name="loan_type" required>
                <option value="Personal">Personal Loan</option>
                <option value="Education">Education Loan</option>
                <option value="Home">Home Loan</option>
            </select><br><br>

            <label for="loan_purpose">Loan Purpose:</label>
            <select name="loan_purpose" required>
                <option value="Business">Business</option>
                <option value="Personal">Personal</option>
                <option value="Education">Education</option>
                <option value="Home">Home</option>
            </select><br><br>

            <button type="submit">Submit Application</button>
        </form>
    </div>

</body>
</html>
