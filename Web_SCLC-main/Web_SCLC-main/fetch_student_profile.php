<?php
include 'db_connection.php';

// Check if reference_no is provided
if (isset($_GET['student_id'])) {
    $reference_no = $_GET['student_id'];

    // Fetch the student's full profile using reference_no
    $query = "SELECT * FROM students WHERE student_id = '$student_id'";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $student = mysqli_fetch_assoc($result);
        ?>
        <div class="profile-section">
            <p><strong>Reference No:</strong> <?php echo htmlspecialchars($student['reference_no']); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($student['name']); ?></p>
            <p><strong>School Year:</strong> <?php echo htmlspecialchars($student['school_year_id']); ?></p>
            <p><strong>Year Level:</strong> <?php echo htmlspecialchars($student['year_level']); ?></p>
            <p><strong>Teacher ID:</strong> <?php echo htmlspecialchars($student['teacher_id']); ?></p>
        </div>
        <?php
    } else {
        echo "<p>No student found with this reference number.</p>";
    }
} else {
    echo "<p>Reference number not provided.</p>";
}
?>
