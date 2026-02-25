<?php
require_once '../config/config.php';
requireUserType('user');

$conn = getDBConnection();
$userId = $_SESSION['user_id'];

// Get activity statistics
$workout_stats = $conn->query("SELECT DATE(activity_date) as date, COUNT(*) as count FROM activity_logs WHERE user_id = $userId AND activity_type = 'workout' GROUP BY DATE(activity_date) ORDER BY date DESC LIMIT 30")->fetch_all(MYSQLI_ASSOC);

$calorie_stats = $conn->query("SELECT DATE(activity_date) as date, SUM(calories) as total FROM activity_logs WHERE user_id = $userId AND activity_type = 'meal' GROUP BY DATE(activity_date) ORDER BY date DESC LIMIT 30")->fetch_all(MYSQLI_ASSOC);

$water_stats = $conn->query("SELECT DATE(activity_date) as date, SUM(water_intake_ml) as total FROM activity_logs WHERE user_id = $userId AND activity_type = 'water' GROUP BY DATE(activity_date) ORDER BY date DESC LIMIT 30")->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress - Personal Fitness Tracker</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <a href="../logout.php">Logout</a>
            </nav>
        </header>

        <main>
            <h2>My Progress</h2>
            
            <section class="progress-section">
                <h3>Workout Frequency (Last 30 Days)</h3>
                <canvas id="workoutChart"></canvas>
            </section>
            
            <section class="progress-section">
                <h3>Calorie Intake (Last 30 Days)</h3>
                <canvas id="calorieChart"></canvas>
            </section>
            
            <section class="progress-section">
                <h3>Water Intake (Last 30 Days)</h3>
                <canvas id="waterChart"></canvas>
            </section>
        </main>

        <footer>
            <p>&copy; 2024 Personal Fitness Tracker. All rights reserved.</p>
        </footer>
    </div>
    
    <script>
        // Workout Chart
        const workoutData = <?php echo json_encode($workout_stats); ?>;
        new Chart(document.getElementById('workoutChart'), {
            type: 'line',
            data: {
                labels: workoutData.map(d => d.date).reverse(),
                datasets: [{
                    label: 'Workouts',
                    data: workoutData.map(d => d.count).reverse(),
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            }
        });
        
        // Calorie Chart
        const calorieData = <?php echo json_encode($calorie_stats); ?>;
        new Chart(document.getElementById('calorieChart'), {
            type: 'bar',
            data: {
                labels: calorieData.map(d => d.date).reverse(),
                datasets: [{
                    label: 'Calories',
                    data: calorieData.map(d => d.total || 0).reverse(),
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderColor: 'rgba(255, 99, 132, 1)'
                }]
            }
        });
        
        // Water Chart
        const waterData = <?php echo json_encode($water_stats); ?>;
        new Chart(document.getElementById('waterChart'), {
            type: 'line',
            data: {
                labels: waterData.map(d => d.date).reverse(),
                datasets: [{
                    label: 'Water (ml)',
                    data: waterData.map(d => d.total || 0).reverse(),
                    borderColor: 'rgb(54, 162, 235)',
                    tension: 0.1
                }]
            }
        });
    </script>
</body>
</html>

