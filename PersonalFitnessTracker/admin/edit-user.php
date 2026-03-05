<?php
require_once '../config/config.php';
requireUserType('admin');

$conn = getDBConnection();
$success = '';
$error = '';

$userId = intval($_GET['id'] ?? 0);
if ($userId <= 0) {
    redirect(BASE_URL . 'admin/manage-users.php');
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';

    if (empty($username) || empty($email)) {
        $error = 'Username and email are required';
    } else {
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ?, first_name = ?, last_name = ? WHERE id = ? AND user_type = 'user'");
            $stmt->bind_param("sssssi", $username, $email, $hashed_password, $first_name, $last_name, $userId);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, first_name = ?, last_name = ? WHERE id = ? AND user_type = 'user'");
            $stmt->bind_param("ssssi", $username, $email, $first_name, $last_name, $userId);
        }

        if ($stmt->execute()) {
            $success = 'User updated successfully';
        } else {
            $error = 'Failed to update user. Please try again.';
        }
        $stmt->close();
    }
}

// Get user data
$result = $conn->query("SELECT * FROM users WHERE id = $userId AND user_type = 'user'");
$user = $result->fetch_assoc();

if (!$user) {
    closeDBConnection($conn);
    redirect(BASE_URL . 'admin/manage-users.php');
}

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>Personal Fitness Tracker - Admin</h1>
            <nav>
                <a href="dashboard.php">Dashboard</a>
                <a href="manage-users.php">Manage Users</a>
                <a href="manage-trainers.php">Manage Trainers</a>
                <a href="manage-content.php">Manage Content</a>
                <a href="activity-logs.php">Activity Logs</a>
                <a href="view-feedback.php">View Feedback</a>
                <a href="../logout.php">Logout</a>
            </nav>
        </header>

        <main>
            <h2>Edit User</h2>

            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <section class="dashboard-section">
                <form method="POST" action="edit-user.php?id=<?php echo $userId; ?>">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password (leave blank to keep current)</label>
                        <input type="password" id="password" name="password">
                    </div>

                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
                    </div>

                    <button type="submit" class="btn btn-primary">Update User</button>
                    <a href="manage-users.php" class="btn btn-secondary">Cancel</a>
                </form>
            </section>
        </main>

        <footer>
            <p>&copy; 2024 Personal Fitness Tracker. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
