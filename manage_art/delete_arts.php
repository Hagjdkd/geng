<?php
include '../plugin/header.php';
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

// Delete artwork query
$stmt = $conn->prepare("DELETE FROM `artworks` WHERE `artwork_id` = ? AND `user_id` = ?");
$stmt->bind_param("ii", $artwork_id, $user_id);

if ($stmt->execute()) {
    header("Location: ../product.php");
    exit();
} else {
    die("Error deleting artwork: " . $stmt->error);
}
?>
