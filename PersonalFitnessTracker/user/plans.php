<?php
require_once '../config/config.php';
requireUserType('user');

$conn = getDBConnection();
$userId = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plan_name = $_POST['plan_name'] ?? '';
    $routine_id = !empty($_POST['routine_id']) ? $_POST['routine_id'] : null;
    $diet_plan_id = !empty($_POST['diet_plan_id']) ? $_POST['diet_plan_id'] : null;
    $start_date = $_POST['start_date'] ?? date('Y-m-d');
    $end_date = $_POST['end_date'] ?? null;
    
    if (($routine_id || $diet_plan_id) && $plan_name) {
        $stmt = $conn->prepare("INSERT INTO user_plans (user_id, routine_id, diet_plan_id, plan_name, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiisss", $userId, $routine_id, $diet_plan_id, $plan_name, $start_date, $end_date);
        
        if ($stmt->execute()) {
            $success = 'Plan added successfully!';
        } else {
            $error = 'Failed to add plan. Please try again.';
        }
        $stmt->close();
    } else {
        $error = 'Please fill in all required fields.';
    }
}

// Get user plans
$user_plans = $conn->query("SELECT up.*, wr.title as routine_title, dp.title as diet_title 
    FROM user_plans up 
    LEFT JOIN workout_routines wr ON up.routine_id = wr.id 
    LEFT JOIN diet_plans dp ON up.diet_plan_id = dp.id 
    WHERE up.user_id = $userId ORDER BY up.created_at DESC")->fetch_all(MYSQLI_ASSOC);

// Get available routines and diet plans
$routines = $conn->query("SELECT id, title FROM workout_routines WHERE is_public = TRUE")->fetch_all(MYSQLI_ASSOC);
$diet_plans = $conn->query("SELECT id, title FROM diet_plans WHERE is_public = TRUE")->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Plans - Personal Fitness Tracker</title>
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
            <h2>My Plans</h2>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <section class="dashboard-section">
                <h3>Add New Plan</h3>
                <form method="POST" action="plans.php" class="plan-form">
                    <div class="form-group">
                        <label for="plan_name">Plan Name</label>
                        <input type="text" id="plan_name" name="plan_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="routine_id">Workout Routine (optional)</label>
                        <select id="routine_id" name="routine_id" onchange="document.getElementById('diet_plan_id').value = ''">
                            <option value="">Select a routine</option>
                            <?php foreach ($routines as $routine): ?>
                                <option value="<?php echo $routine['id']; ?>"><?php echo htmlspecialchars($routine['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="diet_plan_id">Diet Plan (optional)</label>
                        <select id="diet_plan_id" name="diet_plan_id" onchange="document.getElementById('routine_id').value = ''">
                            <option value="">Select a diet plan</option>
                            <?php foreach ($diet_plans as $plan): ?>
                                <option value="<?php echo $plan['id']; ?>"><?php echo htmlspecialchars($plan['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="start_date">Start Date</label>
                        <input type="date" id="start_date" name="start_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="end_date">End Date (optional)</label>
                        <input type="date" id="end_date" name="end_date">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Add Plan</button>
                </form>
            </section>
            
            <section class="dashboard-section">
                <h3>My Saved Plans</h3>
                <?php if (empty($user_plans)): ?>
                    <p>No plans saved yet.</p>
                <?php else: ?>
                    <div class="plans-grid">
                        <?php foreach ($user_plans as $plan): ?>
                            <div class="plan-card">
                                <h4><?php echo htmlspecialchars($plan['plan_name']); ?></h4>
                                <p><strong>Type:</strong> <?php echo $plan['routine_title'] ? 'Workout Routine' : 'Diet Plan'; ?></p>
                                <p><strong>Plan:</strong> <?php echo htmlspecialchars($plan['routine_title'] ?? $plan['diet_title'] ?? 'N/A'); ?></p>
                                <p><strong>Start:</strong> <?php echo htmlspecialchars($plan['start_date']); ?></p>
                                <?php if ($plan['end_date']): ?>
                                    <p><strong>End:</strong> <?php echo htmlspecialchars($plan['end_date']); ?></p>
                                <?php endif; ?>
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
</body>
</html>

