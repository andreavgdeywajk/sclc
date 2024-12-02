<?php

include 'db_connection.php';

// Assume $currentSchoolYearId is defined earlier in your code
$currentSchoolYearId = 1; // Example value; replace with actual logic

// Fetch total number of students for the current school year
$totalStudentsQuery = "SELECT COUNT(*) as total_students FROM students WHERE school_year_id = ?";
$stmt = $connection->prepare($totalStudentsQuery);
$stmt->bind_param('i', $currentSchoolYearId);
$stmt->execute();
$totalStudentsResult = $stmt->get_result();
$totalStudents = $totalStudentsResult->fetch_assoc()['total_students'];
$stmt->close();

// Fetch total number of students for each year level
$yearLevels = ['Kindergarten', 'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5', 'Grade 6'];

$studentsQuery = "
    SELECT year_level, COUNT(*) as total_students 
    FROM students 
    WHERE school_year_id = ? 
    GROUP BY year_level
";

$stmt = $connection->prepare($studentsQuery);
$stmt->bind_param('i', $currentSchoolYearId);
$stmt->execute();
$result = $stmt->get_result();

// Initialize all year levels to 0
$studentsPerLevel = array_fill_keys($yearLevels, 0);

// Populate the studentsPerLevel array with the query results
while ($row = $result->fetch_assoc()) {
    $studentsPerLevel[$row['year_level']] = $row['total_students'];
}

$stmt->close();

// Prepare data for JavaScript
$studentsPerLevelData = json_encode(array_values($studentsPerLevel)); // Encode data for JavaScript
$yearLevelsData = json_encode(array_keys($studentsPerLevel)); // Encode year level names for JavaScript

$connection->close(); // Close the database connection
?>

<div class="dashboard-container">
    <div class="total-section">
        <h2>Total Number of Students</h2>
        <div class="card total-card">
            <h3><?php echo htmlspecialchars($totalStudents); ?> Students Enrolled</h3>
        </div>
    </div>

    <div class="year-level-section">
        <h2>Students per Year Level</h2>
        <div class="card-container">
            <div class="card">
                <h3><?php echo htmlspecialchars('Kindergarten: ' . ($studentsPerLevel['Kindergarten'] > 0 ? $studentsPerLevel['Kindergarten'] . ' Students' : 'No registered students')); ?></h3>
            </div>
            <?php foreach ($studentsPerLevel as $level => $count): ?>
                <?php if ($level !== 'Kindergarten'): ?>
                    <div class="card">
                        <h3>
                            <?php 
                            echo htmlspecialchars($level . ': ' . ($count > 0 ? $count . ' Students' : 'No registered students')); 
                            ?>
                        </h3>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="chart-section">
        <h2>Total Students by Year Level</h2>
        <canvas id="studentsChart" width="400" height="200"></canvas>
    </div>
</div>
<?php include 'index.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Prepare data for the chart
    const labels = <?php echo $yearLevelsData; ?>; // Year levels
    const data = <?php echo $studentsPerLevelData; ?>; // Student counts

    const ctx = document.getElementById('studentsChart').getContext('2d');
    const studentsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Number of Students',
                data: data,
                backgroundColor: '#008080', // Teal color
                borderColor: '#005757', // Darker teal for borders
                borderWidth: 1,
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Students'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Year Level'
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: '#333'
                    }
                }
            }
        }
    });
</script>

<style>
    /* Global Styles */
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Montserrat', sans-serif;
        background-color: #f0f0f0;
    }

    /* Dashboard Container */
    .dashboard-container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 20px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    /* Total Section */
    .total-section {
        margin-bottom: 40px;
    }

    .total-section h2 {
        margin-bottom: 10px;
        color: #333;
    }

    /* Year Level Section */
    .year-level-section {
        margin-bottom: 20px;
    }

    .year-level-section h2 {
        margin-bottom: 10px;
        color: #333;
    }

    .card-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
    }

    .card {
        flex: 1 1 calc(25% - 15px);
        margin: 5px;
        padding: 15px;
        background-color: #ffe6e6; /* Light maroon */
        border: 1px solid #800000; /* Maroon */
        border-radius: 8px;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        text-align: center;
        font-size: 0.9em;
        transition: transform 0.3s, box-shadow 0.3s; /* Add transitions */
    }

    .card:hover {
        transform: translateY(-5px); /* Lift effect */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* Stronger shadow on hover */
    }

    .card h3 {
        margin-bottom: 5px;
        color: #800000; /* Maroon */
    }

    /* Chart Section */
    .chart-section {
        margin-top: 40px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .card {
            flex: 1 1 calc(50% - 10px);
        }
    }

    @media (max-width: 480px) {
        .dashboard-container {
            margin: 10px auto;
            padding: 10px;
        }
        .card {
            flex: 1 1 calc(100% - 10px);
            padding: 8px;
        }
    }
</style>
