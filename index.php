<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

<section class="welcome-banner">
    <div class="banner-content">
        <h1>Welcome to BankPro</h1>
        <p>Your Reliable Partner in Banking Solutions</p>
        <a href="login.php" class="cta-button">Login</a>
        <a href="register.php" class="cta-button-alt">Register</a>
    </div>
</section>

<section class="highlights">
    <div class="highlight-item">
        <img src="assests/download.jpg" alt="Account Management">
        <h3>Account Management</h3>
        <p>Manage your savings and current accounts effortlessly.</p>
    </div>
    <div class="highlight-item">
        <img src="assests/download1.jpg" alt="Loans">
        <h3>Loans</h3>
        <p>Apply for loans and manage repayments easily.</p>
    </div>
    <div class="highlight-item">
        <img src="assests/download2.jpg" alt="Fixed Deposits">
        <h3>Fixed Deposits</h3>
        <p>Grow your wealth with attractive interest rates.</p>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Arial', sans-serif;
    }

    body {
        background-color: #f4f7fc;
        color: #333;
        line-height: 1.6;
    }

    header, nav {
        background-color: #2d3b55;
        color: #fff;
        padding: 1rem 2rem;
    }

    h1, h2 {
        font-size: 2.5rem;
        text-align: center;
    }

    nav ul {
        list-style: none;
        display: flex;
        justify-content: center;
        gap: 1.5rem;
    }

    nav ul li a {
        color: #fff;
        text-decoration: none;
        font-size: 1.1rem;
        padding: 0.5rem 1rem;
        transition: background-color 0.3s;
    }

    nav ul li a:hover, .cta-button:hover, .cta-button-alt:hover {
        background-color: #1d2d45;
    }

    .cta-button, .cta-button-alt {
        text-decoration: none;
        padding: 0.8rem 2rem;
        background-color: #1e90ff;
        color: #fff;
        border-radius: 50px;
        margin: 1rem 0;
        display: inline-block;
        transition: background-color 0.3s;
    }

    .cta-button-alt {
        background-color: #ff7f50;
    }

    .welcome-banner {
        background: linear-gradient(135deg, #2d3b55, #1e90ff);
        color: #fff;
        text-align: center;
        padding: 5rem 1rem;
    }

    .banner-content h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    .banner-content p {
        font-size: 1.3rem;
        margin-bottom: 2rem;
    }

    .highlights {
        display: flex;
        justify-content: space-around;
        margin: 4rem 1rem;
    }

    .highlight-item {
        background: #fff;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        width: 30%;
        padding: 1.5rem;
        border-radius: 10px;
        text-align: center;
        transition: transform 0.3s ease;
    }

    .highlight-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .highlight-item img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 10px;
    }

    .highlight-item h3 {
        font-size: 1.8rem;
        margin-top: 1rem;
        color: #333;
    }

    .highlight-item p {
        font-size: 1rem;
        margin-top: 0.5rem;
        color: #777;
    }

    footer {
        background-color: #2d3b55;
        color: #fff;
        text-align: center;
        padding: 1rem 0;
    }

    footer p {
        font-size: 1rem;
        margin: 0;
    }
</style>
