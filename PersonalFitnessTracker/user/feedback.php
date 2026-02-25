<?php
require_once '../config/config.php';
requireUserType('user');

$conn = getDBConnection();
$userId = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $routine_id = !empty($_POST['routine_id']) ? $_POST['routine_id'] : null;
    $diet_plan_id = !empty($_POST['diet_plan_id']) ? $_POST['diet_plan_id'] : null;
    $rating = $_POST['rating'] ?? null;
    $comment = $_POST['comment'] ?? '';
    
    if (($routine_id || $diet_plan_id) && $rating) {
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, routine_id, diet_plan_id, rating, comment) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiis", $userId, $routine_id, $diet_plan_id, $rating, $comment);
        
        if ($stmt->execute()) {
            $success = 'Feedback submitted successfully!';
        } else {
            $error = 'Failed to submit feedback. Please try again.';
        }
        $stmt->close();
    } else {
        $error = 'Please fill in all required fields.';
    }
}

// Get user's previous feedback
$user_feedback = $conn->query("SELECT f.*, wr.title as routine_title, dp.title as diet_title, 
    (SELECT COUNT(*) FROM trainer_responses WHERE feedback_id = f.id) as response_count
    FROM feedback f 
    LEFT JOIN workout_routines wr ON f.routine_id = wr.id 
    LEFT JOIN diet_plans dp ON f.diet_plan_id = dp.id 
    WHERE f.user_id = $userId ORDER BY f.created_at DESC")->fetch_all(MYSQLI_ASSOC);

// Get routines and diet plans
$routines = $conn->query("SELECT id, title FROM workout_routines WHERE is_public = TRUE")->fetch_all(MYSQLI_ASSOC);
$diet_plans = $conn->query("SELECT id, title FROM diet_plans WHERE is_public = TRUE")->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Personal Fitness Tracker</title>
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
            <h2>Share Feedback</h2>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="feedback.php" class="feedback-form">
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
                    <label for="rating">Rating (1-5)</label>
                    <input type="number" id="rating" name="rating" min="1" max="5" required>
                </div>
                
                <div class="form-group">
                    <label for="comment">Comment</label>
                    <textarea id="comment" name="comment" rows="5" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Submit Feedback</button>
            </form>
            
            <section class="dashboard-section">
                <h3>My Previous Feedback</h3>
                <?php if (empty($user_feedback)): ?>
                    <p>No feedback submitted yet.</p>
                <?php else: ?>
                    <div class="feedback-list">
                        <?php foreach ($user_feedback as $fb): ?>
                            <div class="feedback-item">
                                <h4><?php echo htmlspecialchars($fb['routine_title'] ?? $fb['diet_title'] ?? 'N/A'); ?></h4>
                                <p>Rating: <?php echo $fb['rating']; ?>/5</p>
                                <p><?php echo htmlspecialchars($fb['comment']); ?></p>
                                <p class="text-muted"><?php echo date('Y-m-d H:i', strtotime($fb['created_at'])); ?></p>
                                <?php if ($fb['response_count'] > 0): ?>
                                    <p class="text-success">✓ Trainer has responded</p>
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

