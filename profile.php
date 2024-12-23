<?php
include 'plugin/header.php';
// Include the database connection
include 'db.php';

// Start session and check if user is logged in

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get the current logged-in user's ID
$current_user_id = $_SESSION['user_id'];

// Fetch current user's details
$sql = "SELECT `user_id`, `username`, `email`, `password`, `created_at`, `user_img` FROM `users` WHERE `user_id` = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $user_img = $_FILES['user_img'];

    // Handle file upload
    if ($user_img['size'] > 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($user_img['name']);
        move_uploaded_file($user_img['tmp_name'], $target_file);
    } else {
        $target_file = $user['user_img']; // Keep the existing image if not updated
    }

    // Update user details in the database
    $update_sql = "UPDATE `users` SET `username` = ?, `email` = ?, `password` = ?, `user_img` = ? WHERE `user_id` = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssssi", $username, $email, $password, $target_file, $current_user_id);
    $update_stmt->execute();

    // Refresh the page to reflect changes
    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background:linear-gradient(135deg, #f0d8a8, #9b1c1c); /* Light sky blue */
            margin: 0;
            
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .profile-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 300px;
            padding: 20px;
            margin: 0 auto;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .profile-card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }

        .profile-card form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .profile-card input[type="text"],
        .profile-card input[type="email"],
        .profile-card input[type="password"],
        .profile-card input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .profile-card button {
            padding: 10px;
            background: linear-gradient(135deg, #e6c200, #b8860b); /* Reverse gold gradient */
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .profile-card button:hover {
          background:  #e6c200; /* Reverse gold gradient */
        }
    </style>
</head>
<body>
    <h1>My Profile</h1>
    <div class="profile-card">
        <?php if (!empty($user['user_img'])): ?>
            <img src="<?= htmlspecialchars($user['user_img']) ?>" alt="Profile Image">
        <?php else: ?>
            <img src="default-profile.png" alt="Default Profile Image">
        <?php endif; ?>
        <form action="profile.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" placeholder="Username" required>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="Email" required>
            <input type="password" name="password" placeholder="New Password (Leave empty to keep current)">
            <input type="file" name="user_img">
            <button type="submit">Update Profile</button>
        </form>
    </div>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
