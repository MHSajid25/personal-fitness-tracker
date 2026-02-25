<?php
require_once '../config/config.php';

$conn = getDBConnection();

// Get all public diet plans
$diet_plans = $conn->query("SELECT dp.*, u.username FROM diet_plans dp LEFT JOIN users u ON dp.created_by = u.id WHERE dp.is_public = TRUE ORDER BY dp.created_at DESC")->fetch_all(MYSQLI_ASSOC);

// Get meals for each diet plan
foreach ($diet_plans as &$plan) {
    $plan['meals'] = $conn->query("SELECT * FROM diet_meals WHERE diet_plan_id = " . $plan['id'])->fetch_all(MYSQLI_ASSOC);
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diet Plans - Personal Fitness Tracker</title>
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
            <h2>Diet Plans</h2>
            <p>Explore our nutrition plans. <?php if (!isLoggedIn()): ?><a href="../register.php">Register</a> to create personalized meal plans and track your nutrition.<?php endif; ?></p>
            
            <div class="diet-plans-grid">
                <?php if (empty($diet_plans)): ?>
                    <p>No diet plans available.</p>
                <?php else: ?>
                    <?php foreach ($diet_plans as $plan): ?>
                        <div class="diet-plan-card">
                            <h3><?php echo htmlspecialchars($plan['title']); ?></h3>
                            <p><?php echo htmlspecialchars($plan['description'] ?? ''); ?></p>
                            
                            <?php if (!empty($plan['meals'])): ?>
                                <div class="meals-list">
                                    <h4>Meals:</h4>
                                    <ul>
                                        <?php foreach ($plan['meals'] as $meal): ?>
                                            <li>
                                                <strong><?php echo ucfirst($meal['meal_type']); ?>:</strong> 
                                                <?php echo htmlspecialchars($meal['meal_name']); ?>
                                                <?php if ($meal['calories']): ?>
                                                    (<?php echo $meal['calories']; ?> cal)
                                                <?php endif; ?>
                                                <?php if ($meal['protein_g']): ?>
                                                    - P: <?php echo $meal['protein_g']; ?>g
                                                <?php endif; ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isLoggedIn() && isUser()): ?>
                                <a href="../user/feedback.php?diet_plan_id=<?php echo $plan['id']; ?>" class="btn btn-small">Share Feedback</a>
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

