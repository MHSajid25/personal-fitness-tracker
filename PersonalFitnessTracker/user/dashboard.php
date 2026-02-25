<?php
require_once '../config/config.php';
requireUserType('user');

$conn = getDBConnection();
$userId = $_SESSION['user_id'];

// Get user stats
$stats = [];
$stats['total_workouts'] = $conn->query("SELECT COUNT(*) as count FROM activity_logs WHERE user_id = $userId AND activity_type = 'workout'")->fetch_assoc()['count'];
$stats['total_meals'] = $conn->query("SELECT COUNT(*) as count FROM activity_logs WHERE user_id = $userId AND activity_type = 'meal'")->fetch_assoc()['count'];
$stats['total_water'] = $conn->query("SELECT SUM(water_intake_ml) as total FROM activity_logs WHERE user_id = $userId AND activity_type = 'water'")->fetch_assoc()['total'] ?? 0;

// Get recent activities
$recent_activities = $conn->query("SELECT * FROM activity_logs WHERE user_id = $userId ORDER BY created_at DESC LIMIT 10")->fetch_all(MYSQLI_ASSOC);

// Get user plans
$user_plans = $conn->query("SELECT up.*, wr.title as routine_title, dp.title as diet_title 
    FROM user_plans up 
    LEFT JOIN workout_routines wr ON up.routine_id = wr.id 
    LEFT JOIN diet_plans dp ON up.diet_plan_id = dp.id 
    WHERE up.user_id = $userId")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Personal Fitness Tracker</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Personal Fitness Tracker</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="log-activity.php">Log Activity</a>
                <a href="progress.php">Progress</a>
                <a href="plans.php">My Plans</a>
                <a href="feedback.php">Feedback</a>
                <a href="../public/routines.php">Routines</a>
                <a href="../public/diet-plans.php">Diet Plans</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </header>

        <main>
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] ?? $_SESSION['username']); ?>!</h2>
            
            <section class="stats-grid">
                <div class="stat-card">
                    <h3>Total Workouts</h3>
                    <p class="stat-number"><?php echo $stats['total_workouts']; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Meals Logged</h3>
                    <p class="stat-number"><?php echo $stats['total_meals']; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Water Intake</h3>
                    <p class="stat-number"><?php echo number_format($stats['total_water'] / 1000, 1); ?>L</p>
                </div>
            </section>

            <section class="dashboard-section">
                <h3>Recent Activities</h3>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Details</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_activities)): ?>
                            <tr><td colspan="4">No activities logged yet. <a href="log-activity.php">Log your first activity!</a></td></tr>
                        <?php else: ?>
                            <?php foreach ($recent_activities as $activity): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($activity['activity_date']); ?></td>
                                    <td><?php echo ucfirst($activity['activity_type']); ?></td>
                                    <td>
                                        <?php
                                        if ($activity['activity_type'] === 'workout' && $activity['workout_id']) {
                                            $workout_result = $conn->query("SELECT title FROM workout_routines WHERE id = " . intval($activity['workout_id']));
                                            $workout = $workout_result ? $workout_result->fetch_assoc() : null;
                                            echo htmlspecialchars($workout['title'] ?? 'Workout #' . $activity['workout_id']);
                                        } elseif ($activity['activity_type'] === 'meal') {
                                            echo htmlspecialchars($activity['meal_name'] . ' (' . $activity['calories'] . ' cal)');
                                        } else {
                                            echo htmlspecialchars($activity['water_intake_ml'] . ' ml');
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($activity['notes'] ?? ''); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>

            <section class="dashboard-section">
                <h3>My Plans</h3>
                <?php if (empty($user_plans)): ?>
                    <p>No plans yet. <a href="plans.php">Create a plan!</a></p>
                <?php else: ?>
                    <div class="plans-grid">
                        <?php foreach ($user_plans as $plan): ?>
                            <div class="plan-card">
                                <h4><?php echo htmlspecialchars($plan['plan_name'] ?? ($plan['routine_title'] ?? $plan['diet_title'])); ?></h4>
                                <p><?php echo $plan['routine_title'] ? 'Workout Routine' : 'Diet Plan'; ?></p>
                                <p>Start: <?php echo htmlspecialchars($plan['start_date']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </main>

        <footer>
            <p>&copy; 2024 Personal Fitness Tracker. All rights reserved.</p>
        </footer>
    </div>
    <?php closeDBConnection($conn); ?>
</body>
</html>

