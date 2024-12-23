<?php
session_start();
include 'db.php';

$error = ""; // Initialize error variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and trim input
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        // Prepare and bind parameters to prevent SQL injection
        $stmt = $conn->prepare("SELECT user_id, username, email, password, created_at, user_img FROM users WHERE email = ?");
        if ($stmt) {
            $stmt->bind_param("s", $email);

            // Execute the query
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();

                // Check if user exists and verify the password
                if ($user && password_verify($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_img'] = $user['user_img'];
                    header("Location: user_dashboard.php");
                    exit();
                } else {
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "Database error: Unable to execute query.";
            }

            $stmt->close();
        } else {
            $error = "Database error: Unable to prepare statement.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="icon" href="uploads/nc.jpg">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <form method="POST" class="form" action="">
        <h2>Login</h2>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES); ?>" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
        <p>Don't have an account? <a href="register.php">Register</a></p>
    </form>
    <a href="index.php">&larr; Back to home page</a>
</div>
</body>
</html>
