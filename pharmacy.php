<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['user_type'] !== 'pharmacy') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pharmacy Landing Page</title>
    <style>
        /* Add your custom CSS styles here */
    </style>
</head>
<body>
    <header>
        <div>Welcome, <?php echo $_SESSION['username']; ?></div>
        <!-- Add profile picture display here -->
    </header>

    <!-- Add pharmacy-specific content here -->

    <footer>
        <a href="logout.php">Logout</a>
    </footer>
</body>
</html>
