<?php
include 'db_connection.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    // Prepare and bind
    $stmt = $connection->prepare("INSERT INTO announcements (title, content, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("ss", $title, $content);
    
    if ($stmt->execute()) {
        // Return the new announcement row as HTML
        echo "<tr id='announcement-{$stmt->insert_id}'>";
        echo "<td>" . htmlspecialchars($title) . "</td>";
        echo "<td>" . htmlspecialchars($content) . "</td>";
        echo "<td>" . date('Y-m-d H:i:s') . "</td>";
        echo "<td>";
        echo "<a class='edit-button' href='edit_announcement.php?id={$stmt->insert_id}'>Edit</a>"; // Edit button
        echo "<a class='delete-button' href='#' onclick=\"deleteAnnouncement({$stmt->insert_id})\">Delete</a>"; // Delete button
        echo "</td>";
        echo "</tr>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
