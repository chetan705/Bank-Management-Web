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
        font-family: 'Roboto', 'Arial', sans-serif;
        background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
        margin: 0;
        padding: 0;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    header {
        background: linear-gradient(to right, #2c3e50, #1a2a44);
        color: white;
        padding: 25px 0;
        text-align: center;
        font-size: 28px;
        font-weight: 700;
        letter-spacing: 1.5px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        position: fixed;
        width: 100%;
        top: 0;
        z-index: 1000;
    }

    .form-container {
        width: 100%;
        max-width: 650px;
        margin: 120px auto 30px;
        padding: 40px;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        text-align: center;
        position: relative;
        animation: bounceIn 1.4s ease-out;
    }

    @keyframes bounceIn {
        0% { opacity: 0; transform: scale(0.8); }
        60% { opacity: 1; transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .form-container .image-wrapper {
        position: relative;
        display: inline-block;
        margin-bottom: 30px;
    }

    .form-container img {
        max-width: 300px;
        width: 100%;
        height: auto;
        border-radius: 10px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        transition: transform 0.4s ease, box-shadow 0.4s ease;
    }

    .form-container img:hover {
        transform: scale(1.08);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.35);
    }

    .form-container .image-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(44, 62, 80, 0.2);
        border-radius: 10px;
        z-index: 1;
    }

    .form-container h2 {
        color: #2c3e50;
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 25px;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .form-container label {
        font-size: 16px;
        color: #2c3e50;
        margin-bottom: 8px;
        display: block;
        font-weight: 500;
        text-align: left;
    }

    .form-container input,
    .form-container select,
    .form-container textarea {
        width: 100%;
        padding: 12px 15px;
        margin: 8px 0 20px;
        border: 2px solid #e0e0e0;
        border-radius: 6px;
        box-sizing: border-box;
        font-size: 16px;
        background: #fafafa;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-container input:focus,
    .form-container select:focus,
    .form-container textarea:focus {
        border-color: #2c3e50;
        box-shadow: 0 0 8px rgba(44, 62, 80, 0.3);
        background: #fff;
        outline: none;
    }

    .form-container textarea {
        resize: vertical;
        height: 100px;
    }

    .form-container button {
        width: 100%;
        padding: 15px;
        background: linear-gradient(to right, #2c3e50, #1a2a44);
        border: none;
        border-radius: 6px;
        color: white;
        font-size: 17px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
    }

    .form-container button:hover {
        background: linear-gradient(to right, #1a2a44, #132139);
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
    }

    .form-container p {
        text-align: center;
        font-size: 15px;
        color: #666;
        margin-top: 20px;
    }

    .form-container .error {
        color: #e63946;
        font-size: 15px;
        text-align: center;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .form-container a {
        color: #2c3e50;
        text-decoration: none;
        font-weight: 600;
        transition: color 0.3s ease;
    }

    .form-container a:hover {
        color: #1a2a44;
        text-decoration: underline;
    }

    @media screen and (max-width: 1200px) {
        .form-container {
            max-width: 600px;
            padding: 35px;
        }
        .form-container img {
            max-width: 260px;
        }
    }

    @media screen and (max-width: 768px) {
        .form-container {
            max-width: 90%;
            margin: 100px auto 20px;
            padding: 30px;
        }
        .form-container h2 {
            font-size: 28px;
        }
        .form-container label {
            font-size: 15px;
        }
        .form-container input,
        .form-container select,
        .form-container textarea {
            padding: 10px 12px;
            font-size: 15px;
        }
        .form-container button {
            padding: 13px;
            font-size: 16px;
        }
        .form-container p {
            font-size: 14px;
        }
        .form-container img {
            max-width: 220px;
        }
    }

    @media screen and (max-width: 480px) {
        .form-container {
            margin: 90px auto 15px;
            padding: 20px;
        }
        .form-container h2 {
            font-size: 24px;
        }
        .form-container label {
            font-size: 14px;
        }
        .form-container input,
        .form-container select,
        .form-container textarea {
            padding: 8px 10px;
            font-size: 14px;
        }
        .form-container button {
            padding: 12px;
            font-size: 15px;
        }
        .form-container p {
            font-size: 13px;
        }
        .form-container .error {
            font-size: 14px;
        }
        .form-container img {
            max-width: 180px;
        }
        header {
            font-size: 24px;
            padding: 20px 0;
        }
    }
</style>

<section class="form-container">
    <form method="POST">
        <div class="image-wrapper">
            <img src="assests/download1.jpg" alt="BankPro Registration">
            <div class="image-overlay"></div>
        </div>
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