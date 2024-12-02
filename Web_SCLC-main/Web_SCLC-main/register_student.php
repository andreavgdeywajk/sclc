<?php
include 'db_connection.php';

// Fetch school years from the database
$schoolYearsQuery = "SELECT id, year_label FROM school_years";
$schoolYearsResult = mysqli_query($connection, $schoolYearsQuery);

// Fetch teachers from the database
$teachersQuery = "SELECT teacher_id, teacher_name FROM teachers"; 
$teachersResult = mysqli_query($connection, $teachersQuery);

// Initialize filter variables
$selectedYearLevel = isset($_POST['year_level']) ? mysqli_real_escape_string($connection, $_POST['year_level']) : '';

// Build the SQL query for registered students with filtering and sorting
$studentsQuery = "
    SELECT 
        s.student_id, 
        s.name, 
        sy.year_label AS school_year, 
        s.year_level, 
        t.teacher_name AS assigned_teacher,
        LOWER(REPLACE(s.name, ' ', '')) AS username, 
        UPPER(s.name) AS password
    FROM 
        students s
    LEFT JOIN 
        school_years sy ON s.school_year_id = sy.id
    LEFT JOIN 
        teachers t ON s.teacher_id = t.teacher_id
    WHERE 
        ('$selectedYearLevel' = '' OR s.year_level = '$selectedYearLevel') 
    ORDER BY 
        sy.year_label DESC, 
        FIELD(s.year_level, 'Kindergarten', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'), 
        s.name ASC
";

$studentsResult = mysqli_query($connection, $studentsQuery);

// Initialize a success message variable
$successMessage = '';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['filter'])) {
    $name = mysqli_real_escape_string($connection, $_POST['name']);
    $school_year_id = mysqli_real_escape_string($connection, $_POST['school_year_id']);
    $year_level = mysqli_real_escape_string($connection, $_POST['year_level']);
    $teacher_id = mysqli_real_escape_string($connection, $_POST['teacher_id']); 

    // Prepare the SQL query to insert a new student
    $query = "INSERT INTO students (name, school_year_id, year_level, teacher_id) VALUES ('$name', '$school_year_id', '$year_level', '$teacher_id')";

    if (mysqli_query($connection, $query)) {
        $successMessage = "New student registered successfully!";
        $_POST = [];

         // After processing, redirect to index.php
    header("Location: index.php");
    exit();

    } else {
        echo "Error: " . mysqli_error($connection);
    }
}

?>

<h2>Register Student</h2>
<div class="container">
    <div class="form-container">
        <form action="register_student.php" method="POST">
            <?php if ($successMessage): ?>
                <p style="color: green;"><?php echo $successMessage; ?></p>
            <?php endif; ?>

            <label for="name">Student Name:</label>
            <input type="text" id="name" name="name" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>

            <label for="school_year">School Year:</label>
            <select id="school_year" name="school_year_id" required>
                <option value="">Select School Year</option>
                <?php while ($row = mysqli_fetch_assoc($schoolYearsResult)): ?>
                    <option value="<?php echo htmlspecialchars($row['id']); ?>" <?php echo (isset($_POST['school_year_id']) && $_POST['school_year_id'] == $row['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['year_label']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label for="year_level">Year Level:</label>
            <select id="year_level" name="year_level" required>
                <option value="">Select Year Level</option>
                <?php 
                $levels = ['Kindergarten', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
                foreach ($levels as $level): ?>
                    <option value="<?php echo $level; ?>" <?php echo (isset($_POST['year_level']) && $_POST['year_level'] == $level) ? 'selected' : ''; ?>>
                        <?php echo $level; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="teacher">Assigned Teacher:</label>
            <select id="teacher" name="teacher_id">
                <option value="">Select Teacher (optional)</option>
                <?php while ($row = mysqli_fetch_assoc($teachersResult)): ?>
                    <option value="<?php echo htmlspecialchars($row['teacher_id']); ?>" <?php echo (isset($_POST['teacher_id']) && $_POST['teacher_id'] == $row['teacher_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['teacher_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <button type="submit">Register</button>
        </form>
    </div>

    <div class="table-container">
        <h2>Registered Students</h2>
        <form action="register_student.php" method="POST">
            <label for="year_level">Filter by Year Level:</label>
            <select id="year_level" name="year_level">
                <option value="">Select Year Level</option>
                <?php 
                $levels = ['Kindergarten', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];
                foreach ($levels as $level): ?>
                    <option value="<?php echo $level; ?>" <?php echo ($selectedYearLevel == $level) ? 'selected' : ''; ?>>
                        <?php echo $level; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="filter">Filter</button>
        </form>

        <table>
            <tr>
                <th>Student Name</th>
                <th>School Year</th>
                <th>Year Level</th>
                <th>Assigned Teacher</th>
                <th>Username</th> <!-- New Username Column -->
            </tr>
            <?php while ($row = mysqli_fetch_assoc($studentsResult)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['school_year']); ?></td>
                    <td><?php echo htmlspecialchars($row['year_level']); ?></td>
                    <td><?php echo htmlspecialchars($row['assigned_teacher']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td> <!-- Display Username -->
                </tr>
            <?php endwhile; ?>
        </table>
        
    </div>
</div>

<style>
    /* Overall container for the entire section */
    .container {
        display: flex;
        flex-wrap: wrap;
        background-color: #f9f9f9; /* Light gray background */
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin: 5px 0;
        gap: 20px; /* Added gap between elements */
    }

    /* Form container to register students */
    .form-container {
        flex: 1;
        margin: 20px;
        background-color: #ffffff; /* Changed to white for cleaner form look */
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        max-width: 500px; /* Limit width for better form presentation */
    }

    /* Container for the table listing students */
    .table-container {
        flex: 1;
        margin: 20px;
        background-color: #ffffff; 
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    /* Style for the login form container */
    .login-container {
        margin: 20px auto;
        padding: 20px;
        max-width: 400px; /* Limit the size for better alignment */
        background-color: #ffffff; /* Match the background color */
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    /* Header styles */
    h2 {
        font-family: 'Montserrat', sans-serif;
        font-size: 24px;
        margin-bottom: 10px;
        color: #2C3E50; /* Darker shade for text */
    }

    /* Style for form labels */
    label {
        display: block;
        font-weight: bold;
        margin-bottom: 10px;
    }

    /* Style for input fields and select dropdowns */
    input[type="text"], input[type="password"], select {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
    }

    /* Submit button styles */
    button[type="submit"] {
        background-color: maroon;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px; /* Adjust button font size for consistency */
    }

    /* Hover effect for buttons */
    button[type="submit"]:hover {
        background-color: red;
    }

    /* Style for tables */
    table {
        width: 100%;
        border-collapse: collapse; /* Ensures cleaner table appearance */
        margin-bottom: 20px;
    }

    /* Styles for table headers and rows */
    th, td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    th {
        background-color: #34495E;
        color: white;
    }

    /* Zebra striping for table rows */
    tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    /* Hover effect for table rows */
    tbody tr:hover {
        background-color: #ddd;
    }

    /* Style for responsive behavior on smaller screens */
    @media (max-width: 768px) {
        .container {
            flex-direction: column;
            padding: 10px;
        }

        .form-container, .table-container, .login-container {
            margin: 0;
            width: 100%; /* Ensure full width on smaller screens */
        }
    }
</style>
