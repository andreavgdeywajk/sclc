<?php
$host = '153.92.15.25';
$username = 'u843230181_SclcApp';
$password = 'Sclc1111';
$database = 'u843230181_SCLC';

// Create a connection
$connection = mysqli_connect($host, $username, $password, $database);

// Check the connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Get teacher_id from POST data
$teacher_id = $_POST['teacher_id'];

// Prepare the statement to delete the teacher
$sql = "DELETE FROM teachers WHERE teacher_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $teacher_id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => $stmt->error]);
}

// Close connections
$stmt->close();
$connection->close();
?>
