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

// Get teacher_id from the query string
$teacher_id = $_GET['teacher_id'];

// Prepare the statement to fetch teacher details
$sql = "SELECT teacher_name, year_level FROM teachers WHERE teacher_id = ?";
$stmt = $connection->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$stmt->bind_result($teacher_name, $year_level);
$stmt->fetch();

// Prepare to fetch subjects handled by the teacher based on their year level
$sql_subjects = "
    SELECT s.subject_name
    FROM year_level_subjects yls
    JOIN subjects s ON yls.subject_id = s.subject_id
    WHERE yls.year_level = ?";
$stmt_subjects = $connection->prepare($sql_subjects);
$stmt_subjects->bind_param("s", $year_level);
$stmt_subjects->execute();
$result_subjects = $stmt_subjects->get_result();

$subjects = [];
while ($row = $result_subjects->fetch_assoc()) {
    $subjects[] = $row['subject_name'];
}

// Create response
$response = [
    'teacher_name' => $teacher_name,
    'year_level' => $year_level,
    'subjects' => $subjects
];

echo json_encode($response);

// Close connections
$stmt->close();
$stmt_subjects->close();
$connection->close();
?>
