<?php include 'includes/header.php'; ?>
<?php include 'db/db_connection.php'; ?>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $account_type = $_POST['account_type'];
    $pin = $_POST['pin'];

    do {
        $account_number = rand(1000000000, 9999999999);
        $check_query = "SELECT account_number FROM users WHERE account_number = '$account_number'";
        $result = $conn->query($check_query);
    } while ($result->num_rows > 0);

    $query = "INSERT INTO users (name, email, password, phone, address, account_type, balance, account_number, pin) 
              VALUES ('$name', '$email', '$password', '$phone', '$address', '$account_type', 0, '$account_number', '$pin')";

    if ($conn->query($query)) {
        header("Location: login.php?success=1");
    } else {
        $error = "Registration failed. Please try again.";
    }
}
?>

<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f4f9;
        margin: 0;
        padding: 0;
    }

    header {
        background-color: #00509e;
        color: white;
        padding: 15px 0;
        text-align: center;
        font-size: 20px;
    }

    .form-container {
        width: 100%;
        max-width: 500px;
        margin: 50px auto;
        padding: 30px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .form-container h2 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    .form-container label {
        font-size: 16px;
        color: #333;
        margin-bottom: 5px;
        display: block;
    }

    .form-container input,
    .form-container select,
    .form-container textarea {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 16px;
    }

    .form-container textarea {
        resize: vertical;
        height: 100px;
    }

    .form-container button {
        width: 100%;
        padding: 12px;
        background-color: #00509e;
        border: none;
        border-radius: 4px;
        color: white;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .form-container button:hover {
        background-color: #003f7f;
    }

    .form-container p {
        text-align: center;
        font-size: 14px;
        color: #777;
    }

    .form-container .error {
        color: #e74c3c;
        font-size: 14px;
        text-align: center;
        margin-bottom: 20px;
    }

    .form-container a {
        color: #00509e;
        text-decoration: none;
    }

    .form-container a:hover {
        text-decoration: underline;
    }
</style>

<section class="form-container">
    <form method="POST">
        <h2>Register</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <label>Full Name:</label>
        <input type="text" name="name" required>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <label>Phone Number:</label>
        <input type="text" name="phone" required>
        <label>Address:</label>
        <textarea name="address" required></textarea>
        <label>Account Type:</label>
        <select name="account_type" required>
            <option value="Savings">Savings</option>
            <option value="Current">Current</option>
        </select>
        <label>PIN:</label>
        <input type="text" name="pin" maxlength="4" required>
        <button type="submit">Register</button>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </form>
</section>


