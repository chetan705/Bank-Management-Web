<?php 
include 'includes/header.php'; 
include 'db/db_connection.php'; 

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $query = "SELECT id, email FROM users WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid login credentials.";
    }
    $stmt->close();
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
        max-width: 400px;
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

    .form-container input {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 16px;
    }

    .form-container input[type="email"], 
    .form-container input[type="password"] {
        margin-bottom: 20px;
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
        <h2>Login</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </form>
</section>


