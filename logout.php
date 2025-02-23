<?php
session_start();
session_unset();
session_destroy();

if (isset($_GET['role']) && $_GET['role'] === 'admin') {
    
    header("Location: admin_login.php?message=Logged out successfully.");
} else {

    header("Location: login.php?message=Logged out successfully.");
}

exit;
?>
