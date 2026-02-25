<?php
require_once '../config/config.php';

$conn = getDBConnection();

// Get all public workout routines
$routines = $conn->query("SELECT wr.*, u.username FROM workout_routines wr LEFT JOIN users u ON wr.created_by = u.id WHERE wr.is_public = TRUE ORDER BY wr.created_at DESC")->fetch_all(MYSQLI_ASSOC);

// Get exercises for each routine
foreach ($routines as &$routine) {
    $routine['exercises'] = $conn->query("SELECT * FROM routine_exercises WHERE routine_id = " . $routine['id'])->fetch_all(MYSQLI_ASSOC);
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workout Routines - Personal Fitness Tracker</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Personal Fitness Tracker</h1>
            <nav>
                <a href="../index.php">Home</a>
                <a href="routines.php">Workout Routines</a>
                <a href="tips.php">Fitness Tips</a>
                <a href="diet-plans.php">Diet Plans</a>
                <?php if (isLoggedIn()): ?>
                    <a href="../logout.php">Logout</a>
                <?php else: ?>
                    <a href="../login.php">Login</a>
                    <a href="../register.php">Register</a>
                <?php endif; ?>
            </nav>
        </header>

        <main>
            <h2>Workout Routines</h2>
            <p>Browse our collection of workout routines. <?php if (!isLoggedIn()): ?><a href="../register.php">Register</a> to create personalized plans and track your progress.<?php endif; ?></p>
            
            <div class="routines-grid">
                <?php if (empty($routines)): ?>
                    <p>No workout routines available.</p>
                <?php else: ?>
                    <?php foreach ($routines as $routine): ?>
                        <div class="routine-card">
                            <h3><?php echo htmlspecialchars($routine['title']); ?></h3>
                            <p class="routine-meta">
                                <span class="badge badge-<?php echo $routine['difficulty_level']; ?>"><?php echo ucfirst($routine['difficulty_level']); ?></span>
                                <span><?php echo $routine['duration_minutes']; ?> minutes</span>
                            </p>
                            <p><?php echo htmlspecialchars($routine['description'] ?? ''); ?></p>
                            
                            <?php if (!empty($routine['exercises'])): ?>
                                <div class="exercises-list">
                                    <h4>Exercises:</h4>
                                    <ul>
                                        <?php foreach ($routine['exercises'] as $exercise): ?>
                                            <li>
                                                <?php echo htmlspecialchars($exercise['exercise_name']); ?>
                                                <?php if ($exercise['sets']): ?>
                                                    - <?php echo $exercise['sets']; ?> sets
                                                <?php endif; ?>
                                                <?php if ($exercise['reps']): ?>
                                                    x <?php echo $exercise['reps']; ?> reps
                                                <?php endif; ?>
                                                <?php if ($exercise['duration_seconds']): ?>
                                                    (<?php echo $exercise['duration_seconds']; ?>s)
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isLoggedIn() && isUser()): ?>
                                <a href="../user/feedback.php?routine_id=<?php echo $routine['id']; ?>" class="btn btn-small">Share Feedback</a>
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

