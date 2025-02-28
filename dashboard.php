<?php 
require_once 'config.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AR Quiz Portal - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1976d2;
            --secondary-color: #90caf9;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }
        
        .main-heading {
            color: #1a237e;
            font-weight: 600;
        }
        
        .sub-heading {
            color: #666;
            max-width: 600px;
            margin: 0 auto 3rem;
        }
        
        .quiz-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .quiz-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .quiz-header {
            background-color: var(--primary-color);
            color: white;
            padding: 1.5rem;
        }
        
        .quiz-header h5 {
            margin: 0;
            font-size: 1.25rem;
        }
        
        .quiz-header small {
            color: rgba(255,255,255,0.8);
        }
        
        .quiz-body {
            padding: 1.5rem;
        }
        
        .quiz-detail {
            display: flex;
            align-items: center;
            margin-bottom: 0.5rem;
            color: #666;
        }
        
        .quiz-detail i {
            margin-right: 0.5rem;
        }
        
        .btn-start-quiz {
            background-color: var(--primary-color);
            border: none;
            width: 100%;
            padding: 0.75rem;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }
        
        .btn-start-quiz:hover {
            background-color: #1565c0;
        }
        
        .btn-closed {
            background-color: #9e9e9e;
            cursor: not-allowed;
        }
        
        .student-id {
            color: #666;
            font-weight: 500;
        }
        
        .btn-logout {
            background-color: #f44336;
            border: none;
        }
        
        .btn-logout:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="logo.jpg" alt="GIAIC" onerror="this.src='https://via.placeholder.com/40'">
                AR Quiz Portal
            </a>
            <div class="d-flex align-items-center">
                <span class="student-id me-3">Student ID: <?php echo $_SESSION['user_id']; ?></span>
                <a href="logout.php" class="btn btn-danger btn-logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <img src="logo.webp" alt="GIAIC"class="d-block mx-auto mb-4" height="100">
        <h1 class="text-center main-heading mb-3">Available Quizzes</h1>
        <p class="text-center sub-heading">
            Test your knowledge with our adaptive AI-powered quizzes. Select a quiz below to get started.
        </p>

        <h1>Q2 Result</h1>

        <!-- Quiz Results Section -->
        <div class="results-section mb-5">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Quiz Title</th>
                            <th>Score</th>
                            <th>Total Questions</th>
                            <th>Percentage</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $stmt = $pdo->prepare("
                            SELECT 
                                q.title as quiz_title,
                                r.score,
                                r.total_questions,
                                r.submission_time,
                                (r.score * 100 / r.total_questions) as percentage
                            FROM quiz_results r
                            JOIN quizzes q ON r.quiz_id = q.id
                            WHERE r.user_id = ?
                            ORDER BY r.submission_time DESC
                        ");
                        $stmt->execute([$_SESSION['user_id']]);
                        
                        while ($result = $stmt->fetch()) {
                            $percentage = number_format($result['percentage'], 1);
                            $percentageClass = $result['percentage'] >= 80 ? 'text-success' : 
                                            ($result['percentage'] >= 60 ? 'text-warning' : 'text-danger');
                            
                            echo "<tr>";
                            echo "<td>{$result['quiz_title']}</td>";
                            echo "<td>{$result['score']}</td>";
                            echo "<td>{$result['total_questions']}</td>";
                            echo "<td class='{$percentageClass}'>{$percentage}%</td>";
                            echo "<td>" . date('M d, Y H:i', strtotime($result['submission_time'])) . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="row g-4">
            <?php
            $stmt = $pdo->query("SELECT * FROM quizzes ORDER BY id DESC");
            while ($quiz = $stmt->fetch()) {
            ?>
            <div class="col-md-6">
                <div class="quiz-card">
                    <div class="quiz-header">
                        <h5 class="card-title"><?php echo $quiz['title']; ?></h5>
                        <small>Course Code: <?php echo $quiz['course_code']; ?></small>
                    </div>
                    <div class="quiz-body">
                        <h6 class="mb-3">Quiz Details</h6>
                        <div class="quiz-detail">
                            <i class="bi bi-clock"></i>
                            <span>Duration: <?php echo $quiz['duration']; ?> minutes</span>
                        </div>
                        <div class="quiz-detail mb-4">
                            <i class="bi bi-question-circle"></i>
                            <span>Questions: <?php echo $quiz['total_questions']; ?></span>
                        </div>
                        <?php if ($quiz['status'] == 'open') { ?>
                            <a href="rules.php?quiz_id=<?php echo $quiz['id']; ?>" 
                               class="btn btn-primary btn-start-quiz">Start Quiz</a>
                        <?php } else { ?>
                            <button class="btn btn-secondary btn-start-quiz btn-closed" disabled>Closed</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>