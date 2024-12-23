<?php
include 'plugin/header.php';
include 'db.php';

$artwork_id = isset($_GET['artwork_id']) ? intval($_GET['artwork_id']) : null;

if (!$artwork_id) {
    die('Invalid artwork ID.');
}

// Fetch artwork details, including the image path
$stmt = $conn->prepare("SELECT artwork_id, user_id, title, description, image_path, year, medium, dimensions, price, is_available 
                        FROM artworks WHERE artwork_id = ?");
$stmt->bind_param("i", $artwork_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Artwork not found.');
}

$artwork = $result->fetch_assoc();

// Check if artwork is available
if (!$artwork['is_available']) {
    die('Sorry, this artwork has already been sold.');
}

// Check if the artwork is already in a transaction
$stmt = $conn->prepare("SELECT transaction_id, artwork_id, buyer_id, transaction_date, is_confirmed, buyer_contact 
                        FROM transactions WHERE artwork_id = ?");
$stmt->bind_param("i", $artwork_id);
$stmt->execute();
$transaction_result = $stmt->get_result();

// If there's an existing transaction, inform the user
if ($transaction_result->num_rows > 0) {
    die('This artwork is already part of an active transaction.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $buyer_name = $_POST['buyer_name'];
    $buyer_email = $_POST['buyer_email'];
    $buyer_contact = $_POST['buyer_contact'];

    if (empty($buyer_name) || empty($buyer_email) || empty($buyer_contact)) {
        die('Please fill in all fields.');
    }

    // Insert buyer into the users table if not exists
    $stmt = $conn->prepare("INSERT INTO users (username, email) VALUES (?, ?) ON DUPLICATE KEY UPDATE user_id=LAST_INSERT_ID(user_id)");
    $stmt->bind_param("ss", $buyer_name, $buyer_email);
    $stmt->execute();
    $buyer_id = $conn->insert_id;

    // Insert transaction record
    $stmt = $conn->prepare("INSERT INTO transactions (artwork_id, buyer_id, transaction_date, buyer_contact) VALUES (?, ?, NOW(), ?)");
    $stmt->bind_param("iis", $artwork_id, $buyer_id, $buyer_contact);

    if ($stmt->execute()) {
        // Update artwork availability
        $update_stmt = $conn->prepare("UPDATE artworks SET is_available = 0 WHERE artwork_id = ?");
        $update_stmt->bind_param("i", $artwork_id);
        $update_stmt->execute();

        echo '<p>Thank you for your purchase! The artist will confirm your purchase shortly.</p>';
    } else {
        echo '<p>Error processing your transaction. Please try again later.</p>';
    }

    $stmt->close();
    $conn->close();
    exit();
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buy Now</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            text-align: center;
            color: #2c3e50;
        }

        .artwork-image {
            width: 100%;
            height: auto;
            border-radius: 10px;
            margin: 20px 0;
        }

        .form {
            display: flex;
            flex-direction: column;
        }

        .form input, .form button {
            padding: 10px;
            margin: 10px 0;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form input:focus {
            border-color: #3498db;
            outline: none;
        }

        .form button {
            background-color: #3498db;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .form button:hover {
            background-color: #2980b9;
        }

        p {
            text-align: center;
            font-size: 16px;
        }

        .error {
            color: red;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Buy Artwork</h1>
        <h2><?= htmlspecialchars($artwork['title']) ?></h2>
        <img src="<?= htmlspecialchars($artwork['image_path']) ?>" alt="<?= htmlspecialchars($artwork['title']) ?>" class="artwork-image">
        <p><strong>Price:</strong> <?= $artwork['price'] ? '₱' . number_format($artwork['price'], 2) : 'Contact for pricing' ?></p>
        <p><strong>Description:</strong> <?= htmlspecialchars($artwork['description']) ?></p>
        <p><strong>Year:</strong> <?= htmlspecialchars($artwork['year']) ?></p>
        <p><strong>Medium:</strong> <?= htmlspecialchars($artwork['medium']) ?></p>
        <p><strong>Dimensions:</strong> <?= htmlspecialchars($artwork['dimensions']) ?></p>

        <form class="form" action="buy_now.php?artwork_id=<?= $artwork_id ?>" method="POST">
            <label for="buyer_name">Your Name:</label>
            <input type="text" id="buyer_name" name="buyer_name" required>

            <label for="buyer_email">Your Email:</label>
            <input type="email" id="buyer_email" name="buyer_email" required>

            <label for="buyer_contact">Your Contact Number:</label>
            <input type="text" id="buyer_contact" name="buyer_contact" required>

            <button type="submit">Confirm Purchase</button>
        </form>
    </div>
</body>
</html>
