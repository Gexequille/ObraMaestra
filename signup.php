<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (strlen($password) < 7 || !preg_match('/[A-Z]/', $password) || 
        !preg_match('/\d/', $password) || !preg_match('/[^a-zA-Z\d]/', $password)) {
        echo "Password must be longer than 6 characters and include a capital letter, a number, and a special character.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        try {
            $stmt = $pdo->prepare("INSERT INTO users (email, username, password) VALUES (?, ?, ?)");
            $stmt->execute([$email, $username, $hashedPassword]);
            echo "Sign-up successful! <a href='signin.php'>Sign In</a>";
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                echo "This email is already registered.";
            } else {
                echo "An error occurred: " . htmlspecialchars($e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Sign Up | Obra Maestra</title>
</head>
<body>
    <header>
        <h1>Artist Sign Up</h1>
    </header>

    <form method="POST">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>
        
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        
        <button type="submit">Sign Up</button>
    </form>

</body>
</html>
