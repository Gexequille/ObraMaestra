<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to the sign-in page
    header("Location: signin.php");
    exit;
}

include 'db.php';

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($_SESSION['username']); ?>'s Profile | Obra Maestra</title>
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    </header>

    <main>
        <p>This is your profile page. Here you can manage your account, view uploaded works, or update your settings.</p>
    </main>

    <footer>
        <a href="logout.php">Log Out</a>
    </footer>
</body>
</html>
