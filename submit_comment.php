<?php
session_start();
include 'db.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate the session and input data
    if ($user_id === null) {
        header('Location: login.php');
        exit();
    }

    $artwork_id = isset($_POST['artwork_id']) ? intval($_POST['artwork_id']) : null;
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : null;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    if ($artwork_id && $rating && !empty($comment)) {
        // Prepare and bind the query
        $stmt = $conn->prepare("INSERT INTO comments (artwork_id, user_id, comment, rating, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iisi", $artwork_id, $user_id, $comment, $rating);

        if ($stmt->execute()) {
            // Redirect back with success message
            header('Location: ' . $_SERVER['HTTP_REFERER'] . '?success=1');
            exit();
        } else {
            // Redirect back with error message
            header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=1');
            exit();
        }
    } else {
        // Missing input data
        header('Location: ' . $_SERVER['HTTP_REFERER'] . '?error=2');
        exit();
    }
} else {
    // Invalid request method
    header('HTTP/1.1 405 Method Not Allowed');
    exit();
}
?>
