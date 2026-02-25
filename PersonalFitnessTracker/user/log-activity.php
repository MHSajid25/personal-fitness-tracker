<?php
require_once '../config/config.php';
requireUserType('user');

$conn = getDBConnection();
$userId = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $activity_type = $_POST['activity_type'] ?? '';
    $activity_date = $_POST['activity_date'] ?? date('Y-m-d');
    
    if ($activity_type === 'workout') {
        $workout_id = $_POST['workout_id'] ?? null;
        $notes = $_POST['notes'] ?? '';
        
        $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, activity_type, activity_date, workout_id, notes) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $userId, $activity_type, $activity_date, $workout_id, $notes);
    } elseif ($activity_type === 'meal') {
        $meal_name = $_POST['meal_name'] ?? '';
        $calories = $_POST['calories'] ?? null;
        $notes = $_POST['notes'] ?? '';
        
        $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, activity_type, activity_date, meal_name, calories, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssis", $userId, $activity_type, $activity_date, $meal_name, $calories, $notes);
    } elseif ($activity_type === 'water') {
        $water_intake = $_POST['water_intake_ml'] ?? 0;
        $notes = $_POST['notes'] ?? '';
        
        $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, activity_type, activity_date, water_intake_ml, notes) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $userId, $activity_type, $activity_date, $water_intake, $notes);
    }
    
    if (isset($stmt) && $stmt->execute()) {
        $success = 'Activity logged successfully!';
    } else {
        $error = 'Failed to log activity. Please try again.';
    }
    
    if (isset($stmt)) {
        $stmt->close();
    }
}

// Get workout routines for dropdown
$workout_routines = $conn->query("SELECT id, title FROM workout_routines WHERE is_public = TRUE")->fetch_all(MYSQLI_ASSOC);
closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Activity - Personal Fitness Tracker</title>
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
                <a href="../logout.php">Logout</a>
            </nav>
        </header>

        <main>
            <h2>Log Activity</h2>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="log-activity.php" class="activity-form">
                <div class="form-group">
                    <label for="activity_type">Activity Type</label>
                    <select id="activity_type" name="activity_type" required onchange="toggleActivityFields()">
                        <option value="">Select activity type</option>
                        <option value="workout">Workout</option>
                        <option value="meal">Meal</option>
                        <option value="water">Water Intake</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="activity_date">Date</label>
                    <input type="date" id="activity_date" name="activity_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div id="workout_fields" style="display: none;">
                    <div class="form-group">
                        <label for="workout_id">Workout Routine</label>
                        <select id="workout_id" name="workout_id">
                            <option value="">Select a routine</option>
                            <?php foreach ($workout_routines as $routine): ?>
                                <option value="<?php echo $routine['id']; ?>"><?php echo htmlspecialchars($routine['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div id="meal_fields" style="display: none;">
                    <div class="form-group">
                        <label for="meal_name">Meal Name</label>
                        <input type="text" id="meal_name" name="meal_name">
                    </div>
                    <div class="form-group">
                        <label for="calories">Calories</label>
                        <input type="number" id="calories" name="calories" min="0">
                    </div>
                </div>
                
                <div id="water_fields" style="display: none;">
                    <div class="form-group">
                        <label for="water_intake_ml">Water Intake (ml)</label>
                        <input type="number" id="water_intake_ml" name="water_intake_ml" min="0" value="250">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="notes">Notes (optional)</label>
                    <textarea id="notes" name="notes" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Log Activity</button>
            </form>
        </main>

        <footer>
            <p>&copy; 2024 Personal Fitness Tracker. All rights reserved.</p>
        </footer>
    </div>
    
    <script>
        function toggleActivityFields() {
            const activityType = document.getElementById('activity_type').value;
            document.getElementById('workout_fields').style.display = activityType === 'workout' ? 'block' : 'none';
            document.getElementById('meal_fields').style.display = activityType === 'meal' ? 'block' : 'none';
            document.getElementById('water_fields').style.display = activityType === 'water' ? 'block' : 'none';
        }
    </script>
</body>
</html>

