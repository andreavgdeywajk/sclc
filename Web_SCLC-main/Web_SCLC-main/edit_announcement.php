<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Prepare and bind
    $stmt = $connection->prepare("UPDATE announcements SET title = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $content, $id);

    if ($stmt->execute()) {
        // Return a success response in JSON format
        echo json_encode([
            'status' => 'success',
            'message' => 'Announcement updated successfully.',
            'id' => $id,
            'title' => htmlspecialchars($title),
            'content' => htmlspecialchars($content),
        ]);
    } else {
        // Return an error response
        echo json_encode([
            'status' => 'error',
            'message' => 'Error: ' . $stmt->error,
        ]);
    }

    $stmt->close();
    $conn->close();
    exit; // Exit to prevent sending HTML after a successful AJAX response
}
?>
