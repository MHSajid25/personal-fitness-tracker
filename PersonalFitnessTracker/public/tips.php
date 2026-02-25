<?php
require_once '../config/config.php';

$conn = getDBConnection();

// Get all fitness tips
$tips = $conn->query("SELECT ft.*, u.username FROM fitness_tips ft LEFT JOIN users u ON ft.created_by = u.id ORDER BY ft.created_at DESC")->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Tips - Personal Fitness Tracker</title>
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
            <h2>Fitness Tips</h2>
            <p>Expert advice and tips to help you on your fitness journey.</p>
            
            <div class="tips-grid">
                <?php if (empty($tips)): ?>
                    <p>No fitness tips available.</p>
                <?php else: ?>
                    <?php foreach ($tips as $tip): ?>
                        <div class="tip-card">
                            <h3><?php echo htmlspecialchars($tip['title']); ?></h3>
                            <?php if ($tip['category']): ?>
                                <p class="tip-category"><?php echo htmlspecialchars($tip['category']); ?></p>
                            <?php endif; ?>
                            <p><?php echo nl2br(htmlspecialchars($tip['content'])); ?></p>
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

