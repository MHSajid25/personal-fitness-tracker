<?php
require_once 'config/config.php';

// If already logged in, redirect to appropriate dashboard
if (isLoggedIn()) {
    $userType = getUserType();
    if ($userType === 'admin') {
        redirect('admin/dashboard.php');
    } elseif ($userType === 'trainer') {
        redirect('trainer/dashboard.php');
    } else {
        redirect('user/dashboard.php');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Personal Fitness Tracker - Home</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Personal Fitness Tracker</h1>
            <nav>
                <a href="index.php">Home</a>
                <a href="public/routines.php">Workout Routines</a>
                <a href="public/tips.php">Fitness Tips</a>
                <a href="public/diet-plans.php">Diet Plans</a>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            </nav>
        </header>

        <main>
            <section class="hero">
                <h2>Welcome to Your Personal Fitness Journey</h2>
                <p>Track your workouts, monitor your nutrition, and achieve your fitness goals</p>
            </section>

            <section class="features">
                <div class="feature-card">
                    <h3>Track Workouts</h3>
                    <p>Log your daily exercises and monitor your progress over time</p>
                </div>
                <div class="feature-card">
                    <h3>Monitor Nutrition</h3>
                    <p>Record your meals and water intake to maintain a balanced diet</p>
                </div>
                <div class="feature-card">
                    <h3>View Progress</h3>
                    <p>See your fitness journey through detailed statistics and charts</p>
                </div>
                <div class="feature-card">
                    <h3>Get Expert Advice</h3>
                    <p>Receive personalized suggestions from certified trainers</p>
                </div>
            </section>

            <section class="cta">
                <h2>Ready to Start Your Fitness Journey?</h2>
                <a href="register.php" class="btn btn-primary">Register Now</a>
                <a href="login.php" class="btn btn-secondary">Login</a>
            </section>
        </main>

        <footer>
            <p>&copy; 2024 Personal Fitness Tracker. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>

