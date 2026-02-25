<?php
require_once '../config/config.php';
requireUserType('admin');

$conn = getDBConnection();
$success = '';
$error = '';

// Handle delete
if (isset($_GET['delete']) && $_GET['delete'] > 0) {
    $userId = intval($_GET['delete']);
    if ($conn->query("DELETE FROM users WHERE id = $userId AND user_type = 'user'")) {
        $success = 'User deleted successfully';
    } else {
        $error = 'Failed to delete user';
    }
}

// Get all users
$users = $conn->query("SELECT * FROM users WHERE user_type = 'user' ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
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
            <h2>Manage Users</h2>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Name</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr><td colspan="6">No users found.</td></tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="btn btn-small">Edit</a>
                                    <a href="?delete=<?php echo $user['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </main>

        <footer>
            <p>&copy; 2024 Personal Fitness Tracker. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>

