<?php

include 'plugin/header.php';
include 'db.php';

// Check if the user is logged in
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($user_id === null) {
    header('Location: login.php');
    exit();
}

// Fetch artworks purchased by the current user
$purchased_artworks_stmt = $conn->prepare(
    "SELECT 
        t.transaction_id, 
        a.artwork_id, 
        a.title, 
        a.price, 
        u.username AS seller_name, 
        t.transaction_date, 
        t.is_confirmed 
     FROM 
        transactions t
     JOIN 
        artworks a ON t.artwork_id = a.artwork_id
     JOIN 
        users u ON a.user_id = u.user_id
     WHERE 
        t.buyer_id = ?"
);
$purchased_artworks_stmt->bind_param("i", $user_id);
$purchased_artworks_stmt->execute();
$purchased_artworks = $purchased_artworks_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchased Artworks</title>
   <style>
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
    <h2>Purchased Artworks</h2>
    <table border="1" cellspacing="0" cellpadding="10">
        <thead>
            <tr>
                <th>Artwork Title</th>
                <th>Seller Name</th>
                <th>Price</th>
                <th>Purchase Date</th>
                <th>Confirmation</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($purchased_artworks->num_rows > 0): ?>
                <?php while ($artwork = $purchased_artworks->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($artwork['title']) ?></td>
                        <td><?= htmlspecialchars($artwork['seller_name']) ?></td>
                        <td><?= $artwork['price'] ? '₱' . number_format($artwork['price'], 2) : 'Not Set' ?></td>
                        <td><?= htmlspecialchars($artwork['transaction_date']) ?></td>
                        <td><?= $artwork['is_confirmed'] ? 'Confirmed' : 'Pending Confirmation' ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No purchases found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
