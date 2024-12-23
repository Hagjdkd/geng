<?php
include 'plugin/header.php';
include 'db.php';

// Get the logged-in user's ID
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Redirect to the login page if the user is not logged in
if ($user_id === null) {
    header('Location: login.php');
    exit();
}

// Fetch pending transactions for confirmation
$pending_transactions_stmt = $conn->prepare("SELECT 
    t.transaction_id, 
    a.artwork_id, 
    a.title, 
    a.price, 
    u.username AS buyer_name, 
    u.email AS buyer_email
FROM 
    transactions t
JOIN 
    artworks a ON t.artwork_id = a.artwork_id
JOIN 
    users u ON t.buyer_id = u.user_id
WHERE 
    a.user_id = ? AND t.is_confirmed = 0");
$pending_transactions_stmt->bind_param("i", $user_id);
$pending_transactions_stmt->execute();
$pending_transactions = $pending_transactions_stmt->get_result();

// Handle confirmation
$confirmation_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transaction_id'])) {
    $transaction_id = intval($_POST['transaction_id']);

    // Debug transaction ID
    if (!$transaction_id) {
        die('Invalid transaction ID.');
    }

    // Confirm the transaction
    $confirm_stmt = $conn->prepare("UPDATE transactions SET is_confirmed = 1, artist_confirmation = NOW() WHERE transaction_id = ?");
    $confirm_stmt->bind_param("i", $transaction_id);

    if ($confirm_stmt->execute()) {
        // Get buyer's name after confirmation
        $get_buyer_stmt = $conn->prepare("SELECT u.username FROM transactions t JOIN users u ON t.buyer_id = u.user_id WHERE t.transaction_id = ?");
        $get_buyer_stmt->bind_param("i", $transaction_id);
        $get_buyer_stmt->execute();
        $buyer_result = $get_buyer_stmt->get_result();
        $buyer_name = $buyer_result->fetch_assoc()['username'];

        // Display confirmation message
        $confirmation_message = "Transaction confirmed! Purchased item by $buyer_name.";
        
        // Mark the artwork as sold
        $update_artwork_stmt = $conn->prepare("UPDATE artworks SET is_available = 0 WHERE artwork_id = (SELECT artwork_id FROM transactions WHERE transaction_id = ?)");
        $update_artwork_stmt->bind_param("i", $transaction_id);

        if ($update_artwork_stmt->execute()) {
            echo '<p>Artwork status updated to sold.</p>';
        } else {
            echo '<p>Error updating artwork status: ' . $conn->error . '</p>';
        }
    } else {
        echo '<p>Error confirming the transaction: ' . $conn->error . '</p>';
    }
}

$pending_transactions_stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist Confirmation Page</title>
    <link rel="stylesheet" href="styles_modal.css">
</head>
<body>
    <h1>Pending Transactions</h1>
    <?php if ($confirmation_message): ?>
        <p><?= htmlspecialchars($confirmation_message) ?></p>
    <?php endif; ?>
    <table border="1" cellspacing="0" cellpadding="10">
        <thead>
            <tr>
                <th>Artwork Title</th>
                <th>Price</th>
                <th>Buyer Name</th>
                <th>Buyer Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($pending_transactions->num_rows > 0): ?>
                <?php while ($transaction = $pending_transactions->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($transaction['title']) ?></td>
                        <td><?= $transaction['price'] ? '₱' . number_format($transaction['price'], 2) : 'Not Set' ?></td>
                        <td><?= htmlspecialchars($transaction['buyer_name']) ?></td>
                        <td><?= htmlspecialchars($transaction['buyer_email']) ?></td>
                        <td>
                            <form action="artist_confirmation_page.php" method="POST" style="display: inline;">
                                <input type="hidden" name="transaction_id" value="<?= $transaction['transaction_id'] ?>">
                                <button type="submit">Confirm</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No pending transactions found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
