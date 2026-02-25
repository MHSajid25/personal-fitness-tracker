<?php
require_once '../config/config.php';
requireUserType('admin');

$conn = getDBConnection();

// Get all activity logs
$logs = $conn->query("SELECT al.*, u.username FROM activity_logs al JOIN users u ON al.user_id = u.id ORDER BY al.created_at DESC LIMIT 100")->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs - Admin</title>
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
            <h2>Activity Logs</h2>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Details</th>
                        <th>Notes</th>
                        <th>Logged At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($logs)): ?>
                        <tr><td colspan="6">No activity logs found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($log['username']); ?></td>
                                <td><?php echo ucfirst($log['activity_type']); ?></td>
                                <td><?php echo htmlspecialchars($log['activity_date']); ?></td>
                                <td>
                                    <?php
                                    if ($log['activity_type'] === 'workout') {
                                        $workout = $conn->query("SELECT title FROM workout_routines WHERE id = " . $log['workout_id'])->fetch_assoc();
                                        echo htmlspecialchars($workout['title'] ?? 'Workout ID: ' . $log['workout_id']);
                                    } elseif ($log['activity_type'] === 'meal') {
                                        echo htmlspecialchars($log['meal_name'] . ' (' . $log['calories'] . ' cal)');
                                    } else {
                                        echo htmlspecialchars($log['water_intake_ml'] . ' ml');
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($log['notes'] ?? ''); ?></td>
                                <td><?php echo date('Y-m-d H:i', strtotime($log['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>

        <footer>
            <p>&copy; 2024 Personal Fitness Tracker. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>

