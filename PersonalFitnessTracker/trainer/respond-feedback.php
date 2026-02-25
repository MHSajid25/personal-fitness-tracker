<?php
require_once '../config/config.php';
requireUserType('trainer');

$conn = getDBConnection();
$trainerId = $_SESSION['user_id'];
$success = '';
$error = '';

// Get feedback to respond to
$feedback_id = isset($_GET['feedback_id']) ? intval($_GET['feedback_id']) : 0;
$feedback = null;

if ($feedback_id > 0) {
    $result = $conn->query("SELECT f.*, u.username, wr.title as routine_title, dp.title as diet_title, wr.created_by as routine_creator, dp.created_by as plan_creator
        FROM feedback f 
        JOIN users u ON f.user_id = u.id 
        LEFT JOIN workout_routines wr ON f.routine_id = wr.id 
        LEFT JOIN diet_plans dp ON f.diet_plan_id = dp.id 
        WHERE f.id = $feedback_id");
    $feedback = $result->fetch_assoc();
    
    // Check if trainer created this routine/plan
    if ($feedback && ($feedback['routine_creator'] != $trainerId && $feedback['plan_creator'] != $trainerId)) {
        $feedback = null;
        $error = 'You can only respond to feedback on your own routines/plans';
    }
}

// Handle response submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback_id'])) {
    $feedback_id = intval($_POST['feedback_id']);
    $response_text = $_POST['response_text'] ?? '';
    
    if (empty($response_text)) {
        $error = 'Response text is required';
    } else {
        $stmt = $conn->prepare("INSERT INTO trainer_responses (feedback_id, trainer_id, response_text) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $feedback_id, $trainerId, $response_text);
        
        if ($stmt->execute()) {
            $success = 'Response submitted successfully!';
            $feedback = null; // Clear feedback to show success message
        } else {
            $error = 'Failed to submit response. Please try again.';
        }
        $stmt->close();
    }
}

// Get all feedback for this trainer
$all_feedback = $conn->query("SELECT f.*, u.username, wr.title as routine_title, dp.title as diet_title,
    (SELECT COUNT(*) FROM trainer_responses WHERE feedback_id = f.id) as response_count
    FROM feedback f 
    JOIN users u ON f.user_id = u.id 
    LEFT JOIN workout_routines wr ON f.routine_id = wr.id 
    LEFT JOIN diet_plans dp ON f.diet_plan_id = dp.id 
    WHERE (wr.created_by = $trainerId OR dp.created_by = $trainerId)
    ORDER BY f.created_at DESC")->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respond to Feedback - Trainer</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Personal Fitness Tracker - Trainer</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="view-feedback.php">View Feedback</a>
                <a href="respond-feedback.php">Respond to Feedback</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </header>

        <main>
            <h2>Respond to Feedback</h2>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($feedback): ?>
                <section class="dashboard-section">
                    <h3>Feedback Details</h3>
                    <div class="feedback-item">
                        <h4><?php echo htmlspecialchars($feedback['routine_title'] ?? $feedback['diet_title'] ?? 'N/A'); ?></h4>
                        <p><strong>User:</strong> <?php echo htmlspecialchars($feedback['username']); ?></p>
                        <p><strong>Rating:</strong> <?php echo $feedback['rating']; ?>/5</p>
                        <p><strong>Comment:</strong> <?php echo htmlspecialchars($feedback['comment']); ?></p>
                        <p class="text-muted"><?php echo date('Y-m-d H:i', strtotime($feedback['created_at'])); ?></p>
                    </div>
                    
                    <h3>Your Response</h3>
                    <form method="POST" action="respond-feedback.php">
                        <input type="hidden" name="feedback_id" value="<?php echo $feedback['id']; ?>">
                        <div class="form-group">
                            <label for="response_text">Response</label>
                            <textarea id="response_text" name="response_text" rows="5" required placeholder="Provide suggestions, encouragement, or modifications to the fitness plan..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Response</button>
                    </form>
                </section>
            <?php endif; ?>
            
            <section class="dashboard-section">
                <h3>All Feedback</h3>
                <div class="feedback-list">
                    <?php if (empty($all_feedback)): ?>
                        <p>No feedback found.</p>
                    <?php else: ?>
                        <?php foreach ($all_feedback as $fb): ?>
                            <div class="feedback-item">
                                <h4><?php echo htmlspecialchars($fb['routine_title'] ?? $fb['diet_title'] ?? 'N/A'); ?></h4>
                                <p><strong>User:</strong> <?php echo htmlspecialchars($fb['username']); ?></p>
                                <p><strong>Rating:</strong> <?php echo $fb['rating']; ?>/5</p>
                                <p><strong>Comment:</strong> <?php echo htmlspecialchars($fb['comment']); ?></p>
                                <p class="text-muted"><?php echo date('Y-m-d H:i', strtotime($fb['created_at'])); ?></p>
                                <?php if ($fb['response_count'] > 0): ?>
                                    <p class="text-success">✓ You have responded</p>
                                <?php else: ?>
                                    <a href="?feedback_id=<?php echo $fb['id']; ?>" class="btn btn-small">Respond</a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>

        <footer>
            <p>&copy; 2024 Personal Fitness Tracker. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>

