<?php

include 'plugin/header.php';
include 'db.php';

// Check if the user is logged in
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($user_id === null) {
    header('Location: login.php');
    exit();
}

// Handle confirmation action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_artwork_id'])) {
    $artwork_id = intval($_POST['confirm_artwork_id']);

    // Ensure the artwork belongs to the logged-in user
    $confirm_stmt = $conn->prepare(
        "UPDATE transactions t
         JOIN artworks a ON t.artwork_id = a.artwork_id
         SET t.is_confirmed = 1
         WHERE t.artwork_id = ? AND a.user_id = ? AND t.buyer_id IS NOT NULL"
    );
    $confirm_stmt->bind_param("ii", $artwork_id, $user_id);

    if ($confirm_stmt->execute() && $confirm_stmt->affected_rows > 0) {
        $message = "<p>Transaction confirmed successfully!</p>";
    } else {
        $message = "<p>Error confirming transaction: Either the transaction is already confirmed or invalid.</p>";
    }
}
// Handle cancellation action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_artwork_id'])) {
    $artwork_id = intval($_POST['cancel_artwork_id']);

    // Ensure the artwork belongs to the logged-in user
    $cancel_stmt = $conn->prepare(
        "DELETE FROM transactions 
         WHERE artwork_id = ? 
         AND buyer_id IS NOT NULL 
         AND is_confirmed = 0"
    );
    $cancel_stmt->bind_param("i", $artwork_id);

    if ($cancel_stmt->execute() && $cancel_stmt->affected_rows > 0) {
        $message = "<p>Request canceled successfully!</p>";
    } else {
        $message = "<p>Error canceling request: Either the request is already confirmed or invalid.</p>";
    }
}


// Fetch all artworks uploaded by the user
$uploaded_artworks_stmt = $conn->prepare(
    "SELECT 
        a.artwork_id, 
        a.title, 
        a.is_available, 
        a.price, 
        b.username AS buyer_name 
     FROM 
        artworks a
     LEFT JOIN 
        transactions t ON a.artwork_id = t.artwork_id
     LEFT JOIN 
        users b ON t.buyer_id = b.user_id
     WHERE 
        a.user_id = ?"
);
$uploaded_artworks_stmt->bind_param("i", $user_id);
$uploaded_artworks_stmt->execute();
$uploaded_artworks = $uploaded_artworks_stmt->get_result();

// Fetch sold artworks
$sold_artworks_stmt = $conn->prepare(
    "SELECT 
        a.artwork_id, 
        a.title, 
        u.username AS author_name, 
        b.username AS buyer_name, 
        a.price, 
        t.is_confirmed 
     FROM 
        artworks a
     JOIN 
        users u ON a.user_id = u.user_id
     JOIN 
        transactions t ON a.artwork_id = t.artwork_id
     JOIN 
        users b ON t.buyer_id = b.user_id
     WHERE 
        a.user_id = ? AND a.is_available = 0"
);
$sold_artworks_stmt->bind_param("i", $user_id);
$sold_artworks_stmt->execute();
$sold_artworks = $sold_artworks_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales & Transactions</title>
    <style>
      /* General Page Styling */
body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    margin: 0;
    padding: 0;
    background:  #f0d8a8; /* Light sky blue */
    color: #333;
}

h1, h2 {
    text-align: center;
    margin: 20px 0;
    color: #444;
}

table {
    width: 90%;
    margin: 20px auto;
    border-collapse: collapse;
    background-color: #fff;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

table th, table td {
    text-align: left;
    padding: 15px;
    border: 1px solid #ddd;
}

table th {
    background-color: #f4f4f4;
    color: #333;
    font-weight: bold;
}

table tr:nth-child(even) {
    background-color: #f9f9f9;
}

table tr:hover {
    background-color: #f1f1f1;
}

button {
    background-color: #007bff;
    color: #fff;
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

button:hover {
    background-color: #0056b3;
}

form {
    margin: 0;
    display: inline-block;
}

p {
    text-align: center;
    margin: 15px;
    color: #444;
}

/* Responsive Design */
@media (max-width: 768px) {
    table {
        font-size: 14px;
    }

    table th, table td {
        padding: 10px;
    }

    button {
        font-size: 12px;
    }
}

    </style>
</head>
<body>
    <h1>Sales & Transactions</h1>

    <?php if (isset($message)) echo $message; ?>

    <h2>Your Uploaded Artworks</h2>
    <table border="1" cellspacing="0" cellpadding="10">
        <thead>
            <tr>
                <th>Artwork Title</th>
                <th>Price</th>
                <th>Status</th>
                <th>Buyer Name</th>
                <th>Request</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($uploaded_artworks->num_rows > 0): ?>
                <?php while ($artwork = $uploaded_artworks->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($artwork['title']) ?></td>
                        <td><?= $artwork['price'] ? '₱' . number_format($artwork['price'], 2) : 'Not Set' ?></td>
                        <td><?= $artwork['is_available'] ? 'Available' : 'Sold' ?></td>
                        <td><?= $artwork['buyer_name'] ? htmlspecialchars($artwork['buyer_name']) : 'N/A' ?></td>
                        <td>
                            <?php if (!$artwork['is_available']): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="confirm_artwork_id" value="<?= $artwork['artwork_id'] ?>">
                                    <button type="submit">Confirm</button>
                                </form>
                                <form method="POST" action="" style="display: inline-block;">
                                    <input type="hidden" name="cancel_artwork_id" value="<?= $artwork['artwork_id'] ?>">
                                    <button type="submit" style="background-color: #dc3545;">Cancel</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No artworks found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Sold Artworks</h2>
    <table border="1" cellspacing="0" cellpadding="10">
        <thead>
            <tr>
                <th>Artwork Title</th>
                <th>Author Name</th>
                <th>Buyer Name</th>
                <th>Price</th>
                <th>Confirmation</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($sold_artworks->num_rows > 0): ?>
                <?php while ($artwork = $sold_artworks->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($artwork['title']) ?></td>
                        <td><?= htmlspecialchars($artwork['author_name']) ?></td>
                        <td><?= htmlspecialchars($artwork['buyer_name']) ?></td>
                        <td><?= $artwork['price'] ? '₱' . number_format($artwork['price'], 2) : 'Not Set' ?></td>
                        <td><?= $artwork['is_confirmed'] ? 'Confirmed' : 'Not Confirmed' ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No sales found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html> 
