<?php
session_start(); // Start the session

// Database connection
include 'db_connection.php';

// Check database connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input values
    $student_id = $_POST['student_id']; // Ensure this is correctly defined
    $name = $_POST['name'];
    $year_level = $_POST['year_level'];
    $birthdate = $_POST['birthdate'];
    $address = $_POST['address'];
    $nationality = $_POST['nationality'];
    $contact = $_POST['contact'];
    $father_name = $_POST['father_name'];
    $father_occupation = $_POST['father_occupation'];
    $father_contact_num = $_POST['father_contact_num'];
    $mother_name = $_POST['mother_name'];
    $mother_occupation = $_POST['mother_occupation'];
    $mother_contact_num = $_POST['mother_contact_num'];
    $emergency_name = $_POST['emergency_name'];
    $emergency_relationship = $_POST['emergency_relationship'];
    $emergency_contact_number = $_POST['emergency_contact_number'];
    $emergency_home_address = $_POST['emergency_home_address'];
    $classification = $_POST['classification'];

    // Prepare SQL query to insert profile data
    $sql = "INSERT INTO profiles (student_id, name, birthdate, address, nationality, contact, father_name, father_occupation, father_contact_num, mother_name, mother_occupation, mother_contact_num, emergency_name, emergency_relationship, emergency_contact_number, emergency_home_address, classification)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $connection->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: (" . $connection->errno . ") " . $connection->error);
    }

    // Bind parameters using reference_no
    $stmt->bind_param("sssssssssssssssss", $reference_no, $name, $birthdate, $address, $nationality, $contact, $father_name, $father_occupation, $father_contact_num, $mother_name, $mother_occupation, $mother_contact_num, $emergency_name, $emergency_relationship, $emergency_contact_number, $emergency_home_address, $classification);

    if ($stmt->execute()) {
        // Set success message in session
        $_SESSION['success_message'] = "Student profile was successfully created.";
        
        // Redirect to the index page
        header("Location: index.php"); // Change this to the correct URL of your index page
        exit();
    } else {
        echo "Error: " . $stmt->error; // Output the error message
    }
}
?>
