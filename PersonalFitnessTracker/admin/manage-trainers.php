<?php
require_once '../config/config.php';
requireUserType('admin');

$conn = getDBConnection();
$success = '';
$error = '';

// Handle add/edit trainer
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $trainer_id = $_POST['trainer_id'] ?? null;
    
    if (empty($username) || empty($email)) {
        $error = 'Username and email are required';
    } else {
        if ($trainer_id) {
            // Update
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ?, first_name = ?, last_name = ? WHERE id = ? AND user_type = 'trainer'");
                $stmt->bind_param("sssssi", $username, $email, $hashed_password, $first_name, $last_name, $trainer_id);
            } else {
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, first_name = ?, last_name = ? WHERE id = ? AND user_type = 'trainer'");
                $stmt->bind_param("ssssi", $username, $email, $first_name, $last_name, $trainer_id);
            }
        } else {
            // Insert
            if (empty($password)) {
                $error = 'Password is required for new trainers';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, user_type, first_name, last_name) VALUES (?, ?, ?, 'trainer', ?, ?)");
                $stmt->bind_param("sssss", $username, $email, $hashed_password, $first_name, $last_name);
            }
        }
        
        if (isset($stmt) && $stmt->execute()) {
            $success = $trainer_id ? 'Trainer updated successfully' : 'Trainer added successfully';
        } else {
            $error = 'Operation failed. Please try again.';
        }
        if (isset($stmt)) $stmt->close();
    }
}

// Handle delete
if (isset($_GET['delete']) && $_GET['delete'] > 0) {
    $trainerId = intval($_GET['delete']);
    if ($conn->query("DELETE FROM users WHERE id = $trainerId AND user_type = 'trainer'")) {
        $success = 'Trainer deleted successfully';
    } else {
        $error = 'Failed to delete trainer';
    }
}

// Get trainer to edit
$edit_trainer = null;
if (isset($_GET['edit']) && $_GET['edit'] > 0) {
    $editId = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM users WHERE id = $editId AND user_type = 'trainer'");
    $edit_trainer = $result->fetch_assoc();
}

// Get all trainers
$trainers = $conn->query("SELECT * FROM users WHERE user_type = 'trainer' ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Trainers - Admin</title>
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
            <h2>Manage Trainers</h2>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <section class="dashboard-section">
                <h3><?php echo $edit_trainer ? 'Edit Trainer' : 'Add New Trainer'; ?></h3>
                <form method="POST" action="manage-trainers.php">
                    <?php if ($edit_trainer): ?>
                        <input type="hidden" name="trainer_id" value="<?php echo $edit_trainer['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($edit_trainer['username'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($edit_trainer['email'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password <?php echo $edit_trainer ? '(leave blank to keep current)' : ''; ?></label>
                        <input type="password" id="password" name="password" <?php echo $edit_trainer ? '' : 'required'; ?>>
                    </div>
                    
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($edit_trainer['first_name'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($edit_trainer['last_name'] ?? ''); ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-primary"><?php echo $edit_trainer ? 'Update Trainer' : 'Add Trainer'; ?></button>
                    <?php if ($edit_trainer): ?>
                        <a href="manage-trainers.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </form>
            </section>
            
            <section class="dashboard-section">
                <h3>All Trainers</h3>
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
                        <?php if (empty($trainers)): ?>
                            <tr><td colspan="6">No trainers found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($trainers as $trainer): ?>
                                <tr>
                                    <td><?php echo $trainer['id']; ?></td>
                                    <td><?php echo htmlspecialchars($trainer['username']); ?></td>
                                    <td><?php echo htmlspecialchars($trainer['email']); ?></td>
                                    <td><?php echo htmlspecialchars(($trainer['first_name'] ?? '') . ' ' . ($trainer['last_name'] ?? '')); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($trainer['created_at'])); ?></td>
                                    <td>
                                        <a href="?edit=<?php echo $trainer['id']; ?>" class="btn btn-small">Edit</a>
                                        <a href="?delete=<?php echo $trainer['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>

        <footer>
            <p>&copy; 2024 Personal Fitness Tracker. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>

