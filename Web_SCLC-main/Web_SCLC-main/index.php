<?php
// Starting PHP session to handle any future session data
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit;
}
if (isset($_SESSION['success_message'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['success_message'] . "</div>";
    unset($_SESSION['success_message']); // Clear the message after displaying
}

include 'db_connection.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shechaniah Academic Management System</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@700&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=LineAwesome:wght@500&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@600;700&display=swap" />
    <link rel="stylesheet" href="index.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>
    <header>
        <div class="header-content">
            <div class="left-section">
                <img src="img/SHECHANIAH LOGO 1.png" alt="School Logo" class="logo" />
                <div class="text-section">
                    <span class="shechaniah-academic-management-system">Shechaniah Information <br> Management System</span>
                    <span class="divider">|</span>
                    <span class="header-title">Dashboard</span> 
                </div>
            </div>
            <div class="right-section">
                <div class="user-info">
                    <span class="joeme-ochia">Joeme Ochia</span>
                    <span class="admin">Admin</span>
                </div>
            </div>
        </div>
    </header>

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <ul>
            <li><a href="#" onclick="loadContent('dashboard.php', 'Dashboard')">Dashboard</a></li>
            <li><a href="#" onclick="loadContent('announcement.php', 'Announcement')">Announcement</a></li>
            <li><a href="#" onclick="loadContent('student_list.php', 'Student List')">Student List</a></li>
            <li><a href="#" onclick="loadContent('teachers.php', 'Teachers')">Teachers</a></li>
            <li><a href="#" onclick="loadContent('register_student.php', 'Register')">Register</a></li>
            <li><a href="logout.php" class="btn btn-danger">Logout</a></li>
        </ul>
    </aside>

    <!-- MAIN CONTENT -->
    <div class="main-content" id="main-content">
        <!-- Content will be dynamically loaded here -->
       
    </div>

    <script src="index.js"></script>
</body>
</html>