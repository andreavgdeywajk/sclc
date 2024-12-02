<?php
$host = '153.92.15.25';
$username = 'u843230181_SclcApp';
$password = 'Sclc1111';
$database = 'u843230181_SCLC';

// Create a connection
$connection = mysqli_connect($host, $username, $password, $database);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ($_POST['teacher_name'] as $teacherId => $teacherName) {
        $yearLevel = mysqli_real_escape_string($connection, $_POST['year_level'][$teacherId]);
        $teacherName = mysqli_real_escape_string($connection, $teacherName);
        $subjects = mysqli_real_escape_string($connection, $_POST['subjects'][$teacherId]);

        // Update the teacher information in the database
        $updateQuery = "UPDATE teachers SET teacher_name = '$teacherName', year_level = '$yearLevel', subjects = '$subjects' WHERE teacher_id = '$teacherId'";
        
        if (!mysqli_query($connection, $updateQuery)) {
            echo "Error updating record: " . mysqli_error($connection);
        }
    }

    // Redirect back to the teachers page after saving
    header("Location: index.php");
    exit();
}

// Close the database connection
mysqli_close($connection);
?>
