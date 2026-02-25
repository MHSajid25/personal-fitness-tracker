<?php
require_once '../config/config.php';
requireUserType('trainer');

$conn = getDBConnection();
$trainerId = $_SESSION['user_id'];

// Get feedback for routines created by this trainer
$feedback = $conn->query("SELECT f.*, u.username, wr.title as routine_title, dp.title as diet_title,
    (SELECT response_text FROM trainer_responses WHERE feedback_id = f.id AND trainer_id = $trainerId LIMIT 1) as response_text
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
    <title>View Feedback - Trainer</title>
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
            <h2>View Feedback</h2>
            
            <div class="feedback-list">
                <?php if (empty($feedback)): ?>
                    <p>No feedback found.</p>
                <?php else: ?>
                    <?php foreach ($feedback as $fb): ?>
                        <div class="feedback-item">
                            <h4><?php echo htmlspecialchars($fb['routine_title'] ?? $fb['diet_title'] ?? 'N/A'); ?></h4>
                            <p><strong>User:</strong> <?php echo htmlspecialchars($fb['username']); ?></p>
                            <p><strong>Rating:</strong> <?php echo $fb['rating']; ?>/5</p>
                            <p><strong>Comment:</strong> <?php echo htmlspecialchars($fb['comment']); ?></p>
                            <p class="text-muted"><?php echo date('Y-m-d H:i', strtotime($fb['created_at'])); ?></p>
                            <?php if ($fb['response_text']): ?>
                                <div class="response-box">
                                    <p><strong>Your Response:</strong></p>
                                    <p><?php echo htmlspecialchars($fb['response_text']); ?></p>
                                </div>
                            <?php else: ?>
                                <a href="respond-feedback.php?feedback_id=<?php echo $fb['id']; ?>" class="btn btn-small">Respond</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>

        <footer>
            <p>&copy; 2024 Personal Fitness Tracker. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>

