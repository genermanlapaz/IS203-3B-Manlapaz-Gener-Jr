<?php
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Sample data to simulate database records
$users = [
    ['id' => 1, 'username' => 'admin', 'password' => 'admin123'],
    ['id' => 2, 'username' => 'user', 'password' => 'user123'],
];

// Handle delete request
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $idToDelete = intval($_GET['id']);
    // Filter users to remove the user with the specified ID
    $users = array_filter($users, function ($user) use ($idToDelete) {
        return $user['id'] !== $idToDelete;
    });
}

// Handle edit request
if (isset($_POST['edit'])) {
    $idToEdit = intval($_POST['id']);
    $newUsername = $_POST['username'];
    $newPassword = $_POST['password'];

    foreach ($users as &$user) {
        if ($user['id'] === $idToEdit) {
            $user['username'] = $newUsername;
            $user['password'] = $newPassword;
            break;
        }
    }
}

// Handle create request
if (isset($_POST['create'])) {
    $newId = count($users) + 1; // Assign a new ID
    $newUsername = $_POST['new_username'];
    $newPassword = $_POST['new_password'];
    
    // Add new user to the users array
    $users[] = ['id' => $newId, 'username' => $newUsername, 'password' => $newPassword];
}

// Handle print request
if (isset($_POST['print'])) {
    // Print functionality could be a simple alert or redirect to a print-friendly page
    echo '<script>
            window.print();
          </script>';
}

// Store the updated users array in the session (optional)
$_SESSION['users'] = $users;

// Determine which users to display based on the role
$filteredUsers = $role === 'admin' ? $users : array_filter($users, function ($user) use ($username) {
    return $user['username'] === $username; // Show only the logged-in user
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        a {
            color: #007BFF;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        .form-container {
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <p>Your role: <?php echo htmlspecialchars($role); ?></p>
        <p><a href="logout.php">Logout</a></p>

        <?php if ($role === 'admin'): ?>
            <h2>Admin Dashboard</h2>
            <p>This is the admin area.</p>
            <p><a href="admin.php">Go to Admin Panel</a></p>
        <?php else: ?>
            <h2>User Dashboard</h2>
            <p>This is the user area.</p>
        <?php endif; ?>

        <h2>User Information</h2>
        
        <div class="form-container">
            <h3>Add New User</h3>
            <form method="POST">
                <input type="text" name="new_username" placeholder="Username" required>
                <input type="password" name="new_password" placeholder="Password" required>
                <button type="submit" name="create">Create User</button>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filteredUsers as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['password']); ?></td>
                        <td class="action-buttons">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                                <input type="text" name="username" placeholder="New Username" required>
                                <input type="password" name="password" placeholder="New Password" required>
                                <button type="submit" name="edit">Edit</button>
                            </form>
                            <?php if ($role === 'admin'): // Allow only admin to delete users ?>
                                <a href="?action=delete&id=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="form-container">
            <form method="POST">
                <button type="submit" name="print">Print User List</button>
            </form>
        </div>
    </div>
</body>
</html>