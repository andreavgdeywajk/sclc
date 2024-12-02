<?php
include 'db_connection.php';

if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    // Prepare the SQL DELETE statement
    $query = "DELETE FROM students WHERE student_id = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $student_id);

    if ($stmt->execute()) {
        // Deletion was successful
        echo json_encode(['success' => true]);
    } else {
        // Deletion failed
        echo json_encode(['success' => false]);
    }

    $stmt->close();
    $connection->close();
}
?>
