<?php
require_once '../config/config.php';
requireUserType('admin');

$conn = getDBConnection();
$success = '';
$error = '';

// Handle add/edit fitness tip
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_tip') {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $category = $_POST['category'] ?? '';
        
        if ($title && $content) {
            $stmt = $conn->prepare("INSERT INTO fitness_tips (title, content, category, created_by) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $title, $content, $category, $_SESSION['user_id']);
            if ($stmt->execute()) {
                $success = 'Fitness tip added successfully';
            } else {
                $error = 'Failed to add tip';
            }
            $stmt->close();
        }
    } elseif ($_POST['action'] === 'delete_tip' && isset($_POST['tip_id'])) {
        $tipId = intval($_POST['tip_id']);
        if ($conn->query("DELETE FROM fitness_tips WHERE id = $tipId")) {
            $success = 'Tip deleted successfully';
        }
    }
}

// Get all fitness tips
$tips = $conn->query("SELECT ft.*, u.username FROM fitness_tips ft LEFT JOIN users u ON ft.created_by = u.id ORDER BY ft.created_at DESC")->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Content - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Personal Fitness Tracker - Admin</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="manage-users.php">Manage Users</a>
                <a href="manage-trainers.php">Manage Trainers</a>
                <a href="manage-content.php">Manage Content</a>
                <a href="activity-logs.php">Activity Logs</a>
                <a href="view-feedback.php">View Feedback</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </header>

        <main>
            <h2>Manage Content</h2>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <section class="dashboard-section">
                <h3>Add Fitness Tip</h3>
                <form method="POST" action="manage-content.php">
                    <input type="hidden" name="action" value="add_tip">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" required>
                    </div>
                    <div class="form-group">
                        <label for="content">Content</label>
                        <textarea id="content" name="content" rows="5" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="category">Category</label>
                        <input type="text" id="category" name="category" placeholder="e.g., Exercise, Nutrition, General">
                    </div>
                    <button type="submit" class="btn btn-primary">Add Tip</button>
                </form>
            </section>
            
            <section class="dashboard-section">
                <h3>Fitness Tips</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Created By</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tips)): ?>
                            <tr><td colspan="6">No tips found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($tips as $tip): ?>
                                <tr>
                                    <td><?php echo $tip['id']; ?></td>
                                    <td><?php echo htmlspecialchars($tip['title']); ?></td>
                                    <td><?php echo htmlspecialchars($tip['category'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($tip['username'] ?? 'N/A'); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($tip['created_at'])); ?></td>
                                    <td>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="delete_tip">
                                            <input type="hidden" name="tip_id" value="<?php echo $tip['id']; ?>">
                                            <button type="submit" class="btn btn-small btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
            
            <section class="dashboard-section">
                <h3>Other Content</h3>
                <p><a href="../public/routines.php">Manage Workout Routines</a> (View and manage from routines page)</p>
                <p><a href="../public/diet-plans.php">Manage Diet Plans</a> (View and manage from diet plans page)</p>
            </section>
        </main>

        <footer>
            <p>&copy; 2024 Personal Fitness Tracker. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>

