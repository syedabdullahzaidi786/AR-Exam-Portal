<?php
require_once '../config.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle user deletion
if(isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND is_admin = FALSE");
    $stmt->execute([$user_id]);
    header("Location: users.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            background-color: #1a237e;
            min-height: 100vh;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.8);
            padding: 1rem 1.5rem;
            font-size: 1rem;
        }
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255,255,255,.1);
        }
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,.2);
        }
        .main-content {
            background-color: #f5f5f5;
            min-height: 100vh;
        }
        .users-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .password-cell {
            font-family: monospace;
            background-color: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="quizzes.php">Manage Quizzes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="questions.php">Manage Questions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="users.php">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="results.php">Results</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Users</h1>
                </div>

                <!-- Users Table -->
                <div class="users-table table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Student ID</th>
                                <th>Password</th>
                                <th>Joined Date</th>
                                <th>Quiz Attempts</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("
                                SELECT u.*, COUNT(r.id) as attempts 
                                FROM users u 
                                LEFT JOIN quiz_results r ON u.id = r.user_id 
                                WHERE u.is_admin = FALSE 
                                GROUP BY u.id 
                                ORDER BY u.id DESC
                            ");
                            while ($user = $stmt->fetch()) {
                                echo "<tr>";
                                echo "<td>{$user['id']}</td>";
                                echo "<td>{$user['name']}</td>";
                                echo "<td>{$user['email']}</td>";
                                echo "<td><span class='password-cell'>{$user['password']}</span></td>";
                                echo "<td>" . date('M d, Y H:i', strtotime($user['created_at'])) . "</td>";
                                echo "<td>{$user['attempts']}</td>";
                                echo "<td>
                                        <form method='POST' class='d-inline' 
                                              onsubmit='return confirm(\"Are you sure?\");'>
                                            <input type='hidden' name='user_id' value='{$user['id']}'>
                                            <button type='submit' name='delete_user' 
                                                    class='btn btn-sm btn-danger'>Delete</button>
                                        </form>
                                      </td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>