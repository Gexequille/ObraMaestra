<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}

// Fetch additional user data if needed
include 'db.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Obra Maestra</title>
</head>
<body>
    <header>
        <h1>Dashboard</h1>
        <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
    </header>

    <nav>
        <a href="profile.php">Account</a>
    </nav>
</body>
</html>

