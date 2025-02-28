<?php
require_once '../config.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle search
$search = isset($_GET['search']) ? $_GET['search'] : '';
$searchCondition = $search ? "AND (u.name LIKE :search OR u.email LIKE :search OR q.title LIKE :search)" : "";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Results - Admin</title>
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
        .search-box {
            max-width: 400px;
            margin-bottom: 2rem;
        }
        .results-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .score-cell {
            font-weight: 500;
        }
        .percentage-badge {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }
        .high-score {
            background-color: #4caf50;
            color: white;
        }
        .medium-score {
            background-color: #ff9800;
            color: white;
        }
        .low-score {
            background-color: #f44336;
            color: white;
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
                            <a class="nav-link" href="users.php">Users</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="results.php">Results</a>
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
                    <h1 class="h2">Quiz Results</h1>
                </div>

                <!-- Search Box -->
                <div class="search-box">
                    <form method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" 
                               placeholder="Search by name, email or quiz..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </form>
                </div>

                <!-- Results Table -->
                <div class="results-table table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Student ID</th>
                                <th>Quiz</th>
                                <th>Score</th>
                                <th>Percentage</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "
                                SELECT 
                                    u.name as user_name,
                                    u.email,
                                    q.title as quiz_title,
                                    r.score,
                                    r.total_questions,
                                    r.submission_time,
                                    (r.score * 100 / r.total_questions) as percentage
                                FROM quiz_results r
                                JOIN users u ON r.user_id = u.id
                                JOIN quizzes q ON r.quiz_id = q.id
                                WHERE 1=1 $searchCondition
                                ORDER BY r.submission_time DESC
                            ";
                            
                            $stmt = $pdo->prepare($query);
                            if($search) {
                                $searchParam = "%$search%";
                                $stmt->bindParam(':search', $searchParam);
                            }
                            $stmt->execute();

                            while ($result = $stmt->fetch()) {
                                $percentage = $result['percentage'];
                                $percentageClass = $percentage >= 80 ? 'high-score' : 
                                                 ($percentage >= 60 ? 'medium-score' : 'low-score');
                                
                                echo "<tr>";
                                echo "<td>{$result['user_name']}</td>";
                                echo "<td>{$result['email']}</td>";
                                echo "<td>{$result['quiz_title']}</td>";
                                echo "<td class='score-cell'>{$result['score']}/{$result['total_questions']}</td>";
                                echo "<td><span class='badge percentage-badge {$percentageClass}'>" . 
                                     number_format($percentage, 1) . "%</span></td>";
                                echo "<td>" . date('M d, Y H:i', strtotime($result['submission_time'])) . "</td>";
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