Bank Management System 🏦
A sleek and secure web app for managing banking tasks, built with PHP and MySQL. Features a user-friendly dashboard, transaction analytics with charts, and CSV export. Perfect for learning or demo purposes!

✨ Features

Secure Login: User authentication with PHP sessions.
Dashboard: View account details, recent transactions, loans, and fixed deposits.
Transaction Analytics:
Filter transactions by type or date.
Interactive bar, pie, and line charts using Chart.js.
Download reports as CSV.


Responsive UI: Mobile-friendly design with hamburger menu.
Custom Styling: Powered by CSS and Font Awesome icons.

🚀 Tech Stack

Frontend: HTML, CSS (styles.css), JavaScript (Chart.js v4.4.3)
Backend: PHP 8.2, MySQL
Tools: XAMPP, VS Code, Git

📂 Project Structure
Bank-Management-Web/
├── assests/
│   └── download1.jpg      # Bank logo
├── db/
│   └── db_connection.php  # DB config (ignored in .gitignore)
├── dashboard.php          # Main user dashboard
├── transaction_analytics.php # Transaction analytics page
├── styles.css             # Custom styles
├── .gitignore             # Ignores sensitive files
└── README.md              # You're here!

🛠️ Local Setup

Clone Repo:git clone https://github.com/chetan705/Bank-Management-Web.git
cd Bank-Management-Web


Move to XAMPP:
Copy folder to C:\xampp\htdocs\.


Setup Database:
In phpMyAdmin, create database bank_system.
Import backup.sql (create tables for users, transactions, loans, fixed_deposits).
Create db/db_connection.php:<?php
$host = 'localhost';
$dbname = 'bank_system';
$username = 'root';
$password = '';
$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
date_default_timezone_set('Asia/Kolkata');
?>




Run:
Start XAMPP (Apache, MySQL).
Open http://localhost/Bank-Management-Web/dashboard.php.
Login with test credentials.


🔍 Troubleshooting

Charts not loading? Check console (F12): console.log(typeof Chart); (should be function).
Database error? Verify db/db_connection.php credentials.
Logo missing? Ensure assests/download1.jpg is uploaded with 644 permissions.
CSV issues? Check for whitespace before headers in transaction_analytics.php.

🤝 Contributing

Fork the repo.
Create a branch: git checkout -b my-feature.
Commit: git commit -m "Add feature".
Push: git push origin my-feature.
Submit a pull request.

Contact Developer: Chetan LinkedIn: https://www.linkedin.com/in/chetansharma20/ 
GitHub: https://github.com/chetan705 
Let’s build something impactful together! Feel free to reach out with questions or collaboration ideas.
