<?php
include 'db_connection.php';

// Check if there's a search query via POST
$search = isset($_POST['search']) ? $_POST['search'] : '';

// Pagination setup
$limit = 10; // Number of students per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// SQL query to search for students or fetch all students
if (!empty($search)) {
    // Search query
    $query = "
        SELECT 
            s.student_id, 
            s.name, 
            sy.year_label AS school_year, 
            s.year_level, 
            t.teacher_name AS assigned_teacher
        FROM 
            students s
        LEFT JOIN 
            school_years sy ON s.school_year_id = sy.id
        LEFT JOIN 
            teachers t ON s.teacher_id = t.teacher_id
        WHERE 
            s.student_id LIKE ? 
        LIMIT ? OFFSET ?";
    $searchTerm = "%" . $search . "%";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("sii", $searchTerm, $limit, $offset);
} else {
    // Fetch all students
    $query = "
        SELECT 
            s.student_id, 
            s.name, 
            sy.year_label AS school_year, 
            s.year_level, 
            t.teacher_name AS assigned_teacher
        FROM 
            students s
        LEFT JOIN 
            school_years sy ON s.school_year_id = sy.id
        LEFT JOIN 
            teachers t ON s.teacher_id = t.teacher_id
        LIMIT ? OFFSET ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ii", $limit, $offset);
}
$stmt->execute();
$result = $stmt->get_result();
$students = $result->fetch_all(MYSQLI_ASSOC);

// Get total number of students to calculate pagination
$totalQuery = "SELECT COUNT(*) AS total FROM students WHERE student_id LIKE ?";
$totalStmt = $connection->prepare($totalQuery);
if (!empty($search)) {
    $totalStmt->bind_param("s", $searchTerm);
} else {
    $searchTerm = '%'; // If no search, get all students
    $totalStmt->bind_param("s", $searchTerm);
}
$totalStmt->execute();
$totalResult = $totalStmt->get_result();
$totalStudents = $totalResult->fetch_assoc()['total'];
$totalPages = ceil($totalStudents / $limit);

?>

<h2>Student List</h2>
<div class="student-list-container">
    <div class="search-container">
        <form method="POST" action="">
            <input type="text" id="search-bar" name="search" placeholder="Search reference no..." value="<?php echo htmlspecialchars($search); ?>" />
            <button type="submit">Search</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Student ID</th>
                <th>Name</th>
                <th>School Year</th>
                <th>Year Level</th>
                <th>Assigned Teacher</th> <!-- Changed from Teacher ID to Assigned Teacher -->
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="student-table-body">
            <?php if (count($students) > 0): ?>
                <?php foreach ($students as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['school_year']); ?></td> <!-- Updated to show year_label -->
                        <td><?php echo htmlspecialchars($row['year_level']); ?></td>
                        <td><?php echo htmlspecialchars($row['assigned_teacher']); ?></td> <!-- Updated to show teacher_name -->
                        <td>
                        <a href="student_profile.php?student_id=<?php echo htmlspecialchars($row['student_id']); ?>">View Details</a>
                        <a href="#" class="delete-student" data-student-id="<?php echo htmlspecialchars($row['student_id']); ?>">Delete</a>
                    </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No students found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination links -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
</div>

<script src="index.js"></script>

<style>
    .search-container {
        margin: 20px;
    }

    #search-bar {
        width: 40%;
        padding: 10px;
        font-size: 14px;
        border: 1px solid #2f4388;
        border-radius: 4px;
    }

    .student-list-container {
        position: relative;
        width: 100%;
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        margin: 5px 0;
    }

    table {
        width: 100%;
        table-layout: auto;
    }

    thead {
        background-color: #2C3E50;
        color: white;
        font-size: 16px;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    tbody tr:nth-child(even) {
        background-color: #ffffff;
    }

    tbody tr:hover {
        background-color: #ddd;
    }

    th {
        padding: 12px;
        background-color: #34495E;
        color: white;
    }

    .pagination a {
        margin: 0 5px;
        padding: 8px 16px;
        text-decoration: none;
        color: #007BFF;
        border: 1px solid #ddd;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .pagination a.active {
        background-color: maroon;
        color: white;
    }

    .pagination a:hover {
        background-color: #0056b3;
        color: white;
    }
</style>
