<?php
include '../db.php';

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the artwork_id is provided
if (!isset($_GET['id'])) {
    die("Invalid request. Artwork ID is missing.");
}

$artwork_id = (int)$_GET['id'];

// Fetch artwork details
$stmt = $conn->prepare("SELECT `artwork_id`, `user_id`, `title`, `description`, `image_path`, `year`, `medium`, `dimensions`, `price`, `is_available` FROM `artworks` WHERE `artwork_id` = ? AND `user_id` = ?");
$stmt->bind_param("ii", $artwork_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Artwork not found or you do not have permission to edit it.");
}

$artwork = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect updated data
    $title = $_POST['title'];
    $description = $_POST['description'];
    $year = $_POST['year'];
    $medium = $_POST['medium'];
    $dimensions = $_POST['dimensions'];
    $price = !empty($_POST['price']) ? $_POST['price'] : null;
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    // Handle image upload
    $image_path = $artwork['image_path']; // Default to the current image
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = './uploads/';
        $upload_file = $upload_dir . basename($_FILES['image']['name']);
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_file)) {
            $image_path = $upload_file;
        } else {
            die("Error uploading image.");
        }
    }

    // Update query
    $update_stmt = $conn->prepare("UPDATE `artworks` SET `title` = ?, `description` = ?, `image_path` = ?, `year` = ?, `medium` = ?, `dimensions` = ?, `price` = ?, `is_available` = ? WHERE `artwork_id` = ? AND `user_id` = ?");
    $update_stmt->bind_param("sssssissii", $title, $description, $image_path, $year, $medium, $dimensions, $price, $is_available, $artwork_id, $user_id);

    if ($update_stmt->execute()) {
        header("Location: ../product.php");
        exit();
    } else {
        die("Error updating artwork: " . $update_stmt->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Artwork</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f9;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="text"],
        input[type="number"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        input[type="file"] {
            margin-bottom: 15px;
        }
        input[type="checkbox"] {
            margin-left: 5px;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .submit-btn {
            background-color: #28a745;
            color: white;
        }
        .cancel-btn {
            background-color: #dc3545;
            color: white;
        }
        .button-group {
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <h1>Edit Artwork</h1>
    <form action="" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($artwork['title']) ?>" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?= htmlspecialchars($artwork['description']) ?></textarea>

        <label for="year">Year:</label>
        <input type="text" id="year" name="year" value="<?= htmlspecialchars($artwork['year']) ?>" required>

        <label for="medium">Medium:</label>
        <input type="text" id="medium" name="medium" value="<?= htmlspecialchars($artwork['medium']) ?>" required>

        <label for="dimensions">Dimensions:</label>
        <input type="text" id="dimensions" name="dimensions" value="<?= htmlspecialchars($artwork['dimensions']) ?>" required>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" value="<?= htmlspecialchars($artwork['price']) ?>">

        <label for="image">Artwork Image:</label>
        <input type="file" id="image" name="image" accept="image/*">

        <label for="is_available">Available:</label>
        <input type="checkbox" id="is_available" name="is_available" <?= $artwork['is_available'] ? 'checked' : '' ?>>

        <div class="button-group">
            <button type="submit" class="submit-btn">Update Artwork</button>
            <a href="../product.php" class="cancel-btn" style="text-decoration: none; padding: 10px 20px; text-align: center; border-radius: 4px; display: inline-block;">Cancel</a>
        </div>
    </form>
</body>
</html>
