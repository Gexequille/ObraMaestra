<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> User Profile | Obra Maestra</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <a href="logout.php">Log Out</a>

    <?php
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: signin.php");
        exit;
    }
    ?>
</body>
</html>
