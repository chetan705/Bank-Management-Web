<?php
include 'db/db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['admin'] = $username;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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

        .login-page {
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

        .login-page img {
            max-width: 100px;
            height: auto;
            border-radius: 50%;
            border: 3px solid #00509e;
            margin-bottom: 25px;
            transition: transform 0.6s ease;
        }

        .login-page img:hover {
            transform: scale(1.1);
        }

        .login-page h2 {
            text-align: center;
            color: #333;
            font-size: 28px;
            margin-bottom: 25px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .login-page form div {
            margin-bottom: 20px;
            text-align: left;
        }

        .login-page label {
            font-size: 16px;
            color: #444;
            margin-bottom: 8px;
            display: block;
            font-weight: 500;
        }

        .login-page input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 2px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border-color 0.5s ease, box-shadow 0.5s ease;
        }

        .login-page input:focus {
            border-color: #00509e;
            box-shadow: 0 0 5px rgba(0, 80, 158, 0.3);
            outline: none;
        }

        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(to right, #00509e, #003f7f);
            border: none;
            border-radius: 6px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.5s ease, box-shadow 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 63, 127, 0.4);
        }

        .error {
            color: #e74c3c;
            font-size: 14px;
            text-align: center;
            margin-top: 20px;
            font-weight: 500;
        }

        @media screen and (max-width: 1200px) {
            .login-page {
                max-width: 90%;
                padding: 35px;
            }
            .login-page img {
                max-width: 90px;
            }
        }

        @media screen and (max-width: 768px) {
            .login-page {
                margin: 80px auto 20px;
                padding: 25px;
            }
            .login-page h2 {
                font-size: 24px;
            }
            .login-page label {
                font-size: 14px;
            }
            .login-page input {
                padding: 10px;
                font-size: 14px;
            }
            .btn {
                padding: 12px;
                font-size: 14px;
            }
            .login-page img {
                max-width: 80px;
            }
        }

        @media screen and (max-width: 480px) {
            .login-page {
                margin: 70px auto 15px;
                padding: 20px;
            }
            .login-page h2 {
                font-size: 20px;
            }
            .login-page label {
                font-size: 12px;
            }
            .login-page input {
                padding: 8px;
                font-size: 12px;
            }
            .btn {
                padding: 10px;
                font-size: 12px;
            }
            .error {
                font-size: 12px;
            }
            .login-page img {
                max-width: 60px;
            }
            header {
                font-size: 18px;
                padding: 15px 0;
            }
        }
    </style>
</head>
<body>
    <header>Admin Panel</header>
    <section class="login-page">
        <img src="assests/image1.jpg" alt="Admin Logo">
        <h2>Admin Login</h2>
        <form method="POST">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
    </section>
</body>
</html>