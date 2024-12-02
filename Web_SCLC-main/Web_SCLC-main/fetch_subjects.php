<?php
include 'db_connection.php';

if (isset($_GET['year_level'])) {
    $year_level = $_GET['year_level'];
    
    // Prepare and execute SQL query to fetch subjects
    $query = "SELECT * FROM subjects WHERE year_level = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("s", $year_level);
    $stmt->execute();
    $result = $stmt->get_result();

    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }

    echo json_encode($subjects);
} else {
    echo json_encode([]);
}
?>
