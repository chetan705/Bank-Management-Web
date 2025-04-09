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
        background: linear-gradient(135deg, #e0eafc, #cfdef3);
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    header {
        background: linear-gradient(to right, #00509e, #003f7f);
        color: white;
        padding: 20px 0;
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        letter-spacing: 1px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
    }

    .form-container {
        width: 100%;
        max-width: 450px;
        margin: 100px auto 20px;
        padding: 40px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        text-align: center;
        animation: slideInFromLeft 1.0s ease-out forwards;
    }

    @keyframes slideInFromLeft {
        from { 
            opacity: 0; 
            transform: translateX(-350px); 
        }
        to { 
            opacity: 1; 
            transform: translateX(0); 
        }
    }

    .form-container img {
        max-width: 100px;
        height: auto;
        border-radius: 50%;
        border: 3px solid #00509e;
        margin-bottom: 25px;
        transition: transform 0.3s ease;
    }

    .form-container img:hover {
        transform: scale(1.1);
    }

    .form-container h2 {
        text-align: center;
        color: #333;
        font-size: 28px;
        margin-bottom: 25px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .form-container label {
        font-size: 16px;
        color: #444;
        margin-bottom: 8px;
        display: block;
        font-weight: 500;
        text-align: left;
    }

    .form-container input {
        width: 100%;
        padding: 12px;
        margin: 10px 0 20px;
        border: 2px solid #ddd;
        border-radius: 6px;
        box-sizing: border-box;
        font-size: 16px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-container input:focus {
        border-color: #00509e;
        box-shadow: 0 0 5px rgba(0, 80, 158, 0.3);
        outline: none;
    }

    .form-container button {
        width: 100%;
        padding: 14px;
        background: linear-gradient(to right, #00509e, #003f7f);
        border: none;
        border-radius: 6px;
        color: white;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .form-container button:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 63, 127, 0.4);
    }

    .form-container p {
        text-align: center;
        font-size: 14px;
        color: #666;
        margin-top: 20px;
    }

    .form-container .error {
        color: #e74c3c;
        font-size: 14px;
        text-align: center;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .form-container a {
        color: #00509e;
        text-decoration: none;
        font-weight: bold;
        transition: color 0.3s ease;
    }

    .form-container a:hover {
        color: #003f7f;
        text-decoration: underline;
    }

    @media screen and (max-width: 1200px) {
        .form-container {
            max-width: 90%;
            padding: 35px;
        }
        .form-container img {
            max-width: 90px;
        }
    }

    @media screen and (max-width: 768px) {
        .form-container {
            margin: 80px auto 20px;
            padding: 25px;
        }
        .form-container h2 {
            font-size: 24px;
        }
        .form-container label {
            font-size: 14px;
        }
        .form-container input {
            padding: 10px;
            font-size: 14px;
        }
        .form-container button {
            padding: 12px;
            font-size: 14px;
        }
        .form-container p {
            font-size: 12px;
        }
        .form-container img {
            max-width: 80px;
        }
    }

    @media screen and (max-width: 480px) {
        .form-container {
            margin: 70px auto 15px;
            padding: 20px;
        }
        .form-container h2 {
            font-size: 20px;
        }
        .form-container label {
            font-size: 12px;
        }
        .form-container input {
            padding: 8px;
            font-size: 12px;
        }
        .form-container button {
            padding: 10px;
            font-size: 12px;
        }
        .form-container p {
            font-size: 11px;
        }
        .form-container .error {
            font-size: 12px;
        }
        .form-container img {
            max-width: 60px;
        }
        header {
            font-size: 18px;
            padding: 15px 0;
        }
    }
</style>

<section class="form-container">
    <form method="POST">
        <img src="assests/image1.jpg" alt="BankPro Logo">
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