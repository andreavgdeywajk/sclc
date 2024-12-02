<?php
// Include database connection
include 'db_connection.php';

// Check database connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Handle form submission for posting an announcement
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['announcement-title']) && isset($_POST['announcement-content'])) {
        $title = htmlspecialchars(trim($_POST['announcement-title']));
        $content = htmlspecialchars(trim($_POST['announcement-content']));

        $stmt = $connection->prepare("INSERT INTO announcements (title, content, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ss", $title, $content);

        if ($stmt->execute()) {
            echo "<p>Announcement posted successfully!</p>";
        } else {
            echo "Error posting announcement: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Form fields are not set.";
    }
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $connection->prepare("DELETE FROM announcements WHERE id = ?");
    $stmt->bind_param("i", $delete_id);

    if ($stmt->execute()) {
        echo "<p>Announcement deleted successfully!</p>";
        // Redirect to avoid re-submission on refresh
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error deleting announcement: " . $stmt->error;
    }

    $stmt->close();
}


// Fetch all announcements
$sql = "SELECT id, title, content, created_at FROM announcements ORDER BY id DESC"; 
$result = $connection->query($sql);

// Check if the query was successful
if ($result === false) {
    echo "Error fetching announcements: " . $connection->error; 
} else {
    ?>

   <!-- Existing HTML structure -->
<div class="container">
    <div class="form-container">
        <h2>Announcements</h2>
        <form id="announcement-form" method="POST" action="">
            <input type="text" name="announcement-title" id="announcement-title" placeholder="Title" required>
            <textarea name="announcement-content" id="announcement-content" placeholder="Content" required></textarea>
            <button type="submit">Post Announcement</button>
        </form>
    </div>

    <div class="announcement-list">
        <h2>Previous Announcements</h2>
        <div class="announcement-table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['content']); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                <td>
                                    <button type="button" class="delete-button" onclick="openModal(<?php echo $row['id']; ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No announcements found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

    <!-- Modal for Delete Confirmation -->
<div id="deleteModal" style="display:none;">
    <div style="background-color:white; padding:20px; border:1px solid #ccc; position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); z-index:1000;">
        <h3>Confirm Deletion</h3>
        <p>Are you sure you want to delete this announcement?</p>
        <button id="deleteConfirm" onclick="deleteAnnouncement()">Yes, Delete</button>
        <button id="deleteCancel" onclick="closeModal()">Cancel</button>
    </div>
    <div style="position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5); z-index:999;"></div>
</div>

    <script>
        let deleteId = 0;

        function openModal(id) {
            deleteId = id;
            document.getElementById("deleteModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("deleteModal").style.display = "none";
        }

        function deleteAnnouncement() {
    console.log("Deleting announcement with ID: " + deleteId);
    window.location.href = "?delete_id=" + deleteId;
}

    </script>

    <?php
}
?>


<style>
    /* Import Nunito Sans font */
    @import url('https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;700&display=swap');
    /* General styles for the container */
    .container {
        max-width: 800px;  /* Limit the width of the container */
        margin: 0 auto;    /* Center the container */
        padding: 20px;     /* Add some padding */
        background-color: #f9f9f9; /* Light background color */
        border-radius: 8px; /* Rounded corners */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    }

    /* Form styles */
    .form-container {
        margin-bottom: 20px; /* Space between form and table */
    }

    #announcement-form {
        display: flex; /* Flexbox for better layout */
        flex-direction: column; /* Stack elements vertically */
    }

    #announcement-title, #announcement-content {
        padding: 10px; /* Padding for inputs */
        margin-bottom: 10px; /* Space between inputs */
        border: 1px solid #ccc; /* Border style */
        border-radius: 4px; /* Rounded corners */
        font-size: 16px; /* Increase font size */
    }

    button[type="submit"] {
        padding: 10px; /* Padding for the submit button */
        background-color: #007bff; /* Bootstrap primary color */
        color: white; /* Text color */
        border: none; /* Remove border */
        border-radius: 4px; /* Rounded corners */
        cursor: pointer; /* Change cursor on hover */
        font-size: 16px; /* Increase font size */
        transition: background-color 0.3s; /* Smooth background transition */
    }

    button[type="submit"]:hover {
        background-color: #0056b3; /* Darker shade on hover */
    }

    /* Announcement table styles */
    .announcement-table-container {
        overflow-x: auto; /* Allow horizontal scroll */
    }

    table {
        width: 100%; /* Full width of container */
        border-collapse: collapse; /* Merge borders */
        margin-top: 10px; /* Space above the table */
    }

    th, td {
        padding: 12px; /* Padding for table cells */
        border: 1px solid #ccc; /* Cell border */
        text-align: left; /* Left align text */
    }

    th {
        background-color: #007bff; /* Table header background color */
        color: white; /* Header text color */
    }

    tr:nth-child(even) {
        background-color: #f2f2f2; /* Zebra striping */
    }

    tr:hover {
        background-color: #e6f7ff; /* Highlight row on hover */
    }

    /* Delete button styles */
    .delete-button {
        padding: 8px 12px; /* Padding for delete button */
        background-color: #dc3545; /* Bootstrap danger color */
        color: white; /* Text color */
        border: none; /* Remove border */
        border-radius: 4px; /* Rounded corners */
        cursor: pointer; /* Change cursor on hover */
        transition: background-color 0.3s; /* Smooth background transition */
    }

    .delete-button:hover {
        background-color: #c82333; /* Darker shade on hover */
    }
</style>
