<?php
include 'db_connection.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    $stmt = $connection->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        // Return a success response in JSON format
        echo json_encode([
            'status' => 'success',
            'message' => 'Announcement deleted successfully.',
            'id' => $id,
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
}
?>
