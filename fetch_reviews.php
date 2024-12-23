<?php
include 'db.php';

header('Content-Type: application/json');

if (isset($_GET['artwork_id'])) {
    $artwork_id = intval($_GET['artwork_id']);
    $query = "
        SELECT 
            c.comment_id, 
            c.artwork_id, 
            u.username, 
            c.comment, 
            c.rating, 
            c.created_at
        FROM 
            comments c
        JOIN 
            users u ON c.user_id = u.user_id
        WHERE 
            c.artwork_id = ?
    ";

    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $artwork_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $reviews = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($reviews);
        $stmt->close();
    } else {
        echo json_encode(["error" => "Failed to prepare query"]);
    }
} else {
    echo json_encode(["error" => "No artwork ID provided"]);
}
?>
