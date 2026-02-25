<?php
require_once '../config/config.php';
requireUserType('admin');

$conn = getDBConnection();

// Get statistics
$stats = [];
$stats['total_users'] = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'user'")->fetch_assoc()['count'];
$stats['total_trainers'] = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'trainer'")->fetch_assoc()['count'];
$stats['total_routines'] = $conn->query("SELECT COUNT(*) as count FROM workout_routines")->fetch_assoc()['count'];
$stats['total_feedback'] = $conn->query("SELECT COUNT(*) as count FROM feedback")->fetch_assoc()['count'];

// Get recent activity logs
$recent_logs = $conn->query("SELECT al.*, u.username FROM activity_logs al JOIN users u ON al.user_id = u.id ORDER BY al.created_at DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Personal Fitness Tracker</title>
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
            <h2>Admin Dashboard</h2>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] ?? $_SESSION['username']); ?>!</p>
            
            <section class="stats-grid">
                <div class="stat-card">
                    <h3>Total Users</h3>
                    <p class="stat-number"><?php echo $stats['total_users']; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Trainers</h3>
                    <p class="stat-number"><?php echo $stats['total_trainers']; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Workout Routines</h3>
                    <p class="stat-number"><?php echo $stats['total_routines']; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Feedback</h3>
                    <p class="stat-number"><?php echo $stats['total_feedback']; ?></p>
                </div>
            </section>

            <section class="dashboard-section">
                <h3>Recent Activity Logs</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_logs)): ?>
                            <tr><td colspan="4">No activity logs yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($recent_logs as $log): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($log['username']); ?></td>
                                    <td><?php echo ucfirst($log['activity_type']); ?></td>
                                    <td><?php echo htmlspecialchars($log['activity_date']); ?></td>
                                    <td>
                                        <?php
                                        if ($log['activity_type'] === 'workout') {
                                            echo 'Workout ID: ' . $log['workout_id'];
                                        } elseif ($log['activity_type'] === 'meal') {
                                            echo htmlspecialchars($log['meal_name'] . ' (' . $log['calories'] . ' cal)');
                                        } else {
                                            echo htmlspecialchars($log['water_intake_ml'] . ' ml');
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>

        <footer>
            <p>&copy; 2024 Personal Fitness Tracker. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>

