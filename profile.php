<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_account'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $profile_picture = null;

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        $fileName = uniqid() . '-' . basename($_FILES['profile_picture']['name']);
        $uploadFile = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
            $profile_picture = $uploadFile;
        }
    }

    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, profile_picture = ? WHERE id = ?");
    $stmt->execute([$username, $email, $profile_picture, $_SESSION['user_id']]);

    echo "Account updated successfully.";
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_commission'])) {
    // Retrieve form inputs
    $bio = $_POST['bio'] ?? '';
    $terms = $_POST['terms'] ?? '';
    $commission_type = $_POST['commission_type'] ?? '';
    $min_price = $_POST['min_price'] ?? 0;
    $max_price = $_POST['max_price'] ?? 0;
    $delivery_time = $_POST['delivery_time'] ?? 0;
    $slots = $_POST['slots'] ?? 0;
    $additional_info = $_POST['additional_info'] ?? '';

    // Handle sample image uploads
    $uploaded_images = [];
    if (!empty($_FILES['sample_images']['name'][0])) {
        $uploadDir = 'uploads/';
        foreach ($_FILES['sample_images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['sample_images']['error'][$key] === UPLOAD_ERR_OK) {
                $fileName = uniqid() . '-' . basename($_FILES['sample_images']['name'][$key]);
                $uploadFile = $uploadDir . $fileName;

                // Move uploaded file to the upload directory
                if (move_uploaded_file($tmp_name, $uploadFile)) {
                    $uploaded_images[] = $uploadFile;
                }
            }
        }
    }

    // Serialize images for database storage
    $images_serialized = serialize($uploaded_images);

    // Update commission information in the database
    $stmt = $pdo->prepare(
        "UPDATE users SET 
            bio = ?, 
            terms_of_service = ?, 
            commission_type = ?, 
            min_price = ?, 
            max_price = ?, 
            delivery_time = ?, 
            slots = ?, 
            additional_info = ?, 
            sample_images = ? 
        WHERE id = ?"
    );
    $stmt->execute([
        $bio, 
        $terms, 
        $commission_type, 
        $min_price, 
        $max_price, 
        $delivery_time, 
        $slots, 
        $additional_info, 
        $images_serialized, 
        $_SESSION['user_id']
    ]);

    echo "Commission information updated successfully.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_payment'])) {
    // Get the form inputs
    $currency = $_POST['currency'] ?? 'USD'; // Default to USD if not selected
    $paypal_email = $_POST['paypal_email'] ?? '';

    // Update payment settings in the database
    $stmt = $pdo->prepare("UPDATE users SET currency = ?, paypal_email = ? WHERE id = ?");
    $stmt->execute([$currency, $paypal_email, $_SESSION['user_id']]);

    echo "Payment settings updated successfully!";
}

// Fetch user data for display
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Obra Maestra</title>
</head>
<body>
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
    </header>

    <nav>
        <a href="#account">Account</a>
        <a href="#commission">Commission Information</a>
        <a href="#notification">Notifications</a>
        <a href="#payment">Payments</a>
    </nav>

    <section id="account">
        <h2>Account</h2>
        <form method="POST" enctype="multipart/form-data">

            <label for="profile_picture">Upload Profile Picture:</label><br>
            <?php if (!empty($user['profile_picture'])): ?>
                <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" width="100"><br>
            <?php endif; ?>
            <input type="file" id="profile_picture" name="profile_picture"><br><br>

            <label for="username">Edit Username:</label><br>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required><br><br>

            <label for="email">Edit Email:</label><br>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required><br><br>

            <button type="submit" name="update_account">Save Changes</button>
        </form>
    </section>

    <section id="commission">
    <h2>Commission Information</h2>
    <form method="POST" enctype="multipart/form-data">
        <!-- Add Bio -->
        <label for="bio">Add Bio:</label><br>
        <textarea id="bio" name="bio" rows="4" cols="50"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea><br><br>

        <!-- Add Terms of Service -->
        <label for="terms">Add Terms of Service:</label><br>
        <textarea id="terms" name="terms" rows="4" cols="50"><?php echo htmlspecialchars($user['terms_of_service'] ?? ''); ?></textarea><br><br>

        <!-- Commission Form -->
        <fieldset>
            <legend>Customize Commission Form</legend>

            <!-- Commission Type -->
            <label for="commission_type">Commission Type:</label><br>
            <input type="text" id="commission_type" name="commission_type" placeholder="e.g., Bust, Full Body"><br><br>

            <!-- Min-Max Price -->
            <label for="price_range">Min-Max Price:</label><br>
            <input type="number" id="min_price" name="min_price" placeholder="Minimum Price">
            <input type="number" id="max_price" name="max_price" placeholder="Maximum Price"><br><br>

            <!-- Estimated Delivery -->
            <label for="delivery_time">Estimated Delivery (days):</label><br>
            <input type="number" id="delivery_time" name="delivery_time" placeholder="Number of Days"><br><br>

            <!-- Number of Slots -->
            <label for="slots">Number of Slots:</label><br>
            <input type="number" id="slots" name="slots" placeholder="Available Slots"><br><br>

            <!-- Additional Info -->
            <label for="additional_info">Additional Info:</label><br>
            <textarea id="additional_info" name="additional_info" rows="4" cols="50"></textarea><br><br>

            <!-- Upload Sample Images -->
            <label for="sample_images">Upload Sample Images (1-5):</label><br>
            <input type="file" id="sample_images" name="sample_images[]" multiple accept="image/*"><br><br>
        </fieldset>

        <!-- Save Button -->
        <button type="submit" name="save_commission">Save Changes</button>
    </form>
</section>

<section id="notification">
    <h2>Notifications</h2>
    <form method="POST">
        <fieldset>
            <legend>Enable Notifications</legend>

            <!-- New Commission Request -->
            <label>
                <input type="checkbox" name="notifications[]" value="new_request"
                <?php echo (isset($user['notifications']) && in_array('new_request', $user['notifications'])) ? 'checked' : ''; ?>>
                New Commission Request
            </label><br>

            <!-- Payment Processed -->
            <label>
                <input type="checkbox" name="notifications[]" value="payment_processed"
                <?php echo (isset($user['notifications']) && in_array('payment_processed', $user['notifications'])) ? 'checked' : ''; ?>>
                Payment Processed
            </label><br>

            <!-- Revision Request -->
            <label>
                <input type="checkbox" name="notifications[]" value="revision_request"
                <?php echo (isset($user['notifications']) && in_array('revision_request', $user['notifications'])) ? 'checked' : ''; ?>>
                Revision Request
            </label><br>

            <!-- Commission Completed -->
            <label>
                <input type="checkbox" name="notifications[]" value="commission_completed"
                <?php echo (isset($user['notifications']) && in_array('commission_completed', $user['notifications'])) ? 'checked' : ''; ?>>
                Commission Completed
            </label><br>
        </fieldset>

        <!-- Save Button -->
        <button type="submit" name="save_notifications">Save Changes</button>
    </form>
</section>

<section id="payment">
    <h2>Payments</h2>
    <form method="POST">
        <fieldset>
            <legend>Payment Settings</legend>

            <!-- Set Currency -->
            <label for="currency">Set Currency:</label><br>
            <select id="currency" name="currency">
                <option value="USD" <?php echo (isset($user['currency']) && $user['currency'] == 'USD') ? 'selected' : ''; ?>>USD</option>
                <option value="EUR" <?php echo (isset($user['currency']) && $user['currency'] == 'EUR') ? 'selected' : ''; ?>>EUR</option>
                <option value="GBP" <?php echo (isset($user['currency']) && $user['currency'] == 'GBP') ? 'selected' : ''; ?>>GBP</option>
                <!-- Add more currencies as needed -->
            </select><br><br>

            <!-- Connect PayPal Account -->
            <label for="paypal_email">Connect PayPal Account:</label><br>
            <input type="email" id="paypal_email" name="paypal_email" placeholder="Enter your PayPal email" value="<?php echo htmlspecialchars($user['paypal_email'] ?? ''); ?>"><br><br>
        </fieldset>

        <!-- Save Button -->
        <button type="submit" name="save_payment">Save Changes</button>
    </form>
</section>

</body>
</html>
