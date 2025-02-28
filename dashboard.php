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

        <div class="results-container mb-5">
            <h2 class="text-center main-heading mb-4">Your Quiz Results</h2>
            
            <?php
            $stmt = $pdo->prepare("
                SELECT 
                    q.id as quiz_id,
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
            $results = $stmt->fetchAll();
            
            if (count($results) > 0) {
                foreach ($results as $result) {
                    $percentage = number_format($result['percentage'], 1);
                    $cardClass = $result['percentage'] >= 80 ? 'border-success' : 
                               ($result['percentage'] >= 60 ? 'border-warning' : 'border-danger');
                    $textClass = $result['percentage'] >= 80 ? 'text-success' : 
                               ($result['percentage'] >= 60 ? 'text-warning' : 'text-danger');
                    ?>
                    <div class="card mb-3 <?php echo $cardClass; ?>" style="border-width: 2px;">
                        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                            <h3 class="h5 mb-0">Q<?php echo $result['quiz_id']; ?> Result</h3>
                            <small class="text-muted"><?php echo date('M d, Y H:i', strtotime($result['submission_time'])); ?></small>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h4 class="h6 text-muted mb-3">Quiz: <?php echo $result['quiz_title']; ?></h4>
                                    <p class="mb-0">
                                        <strong>Score:</strong> <?php echo $result['score']; ?> / <?php echo $result['total_questions']; ?>
                                    </p>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div class="h4 <?php echo $textClass; ?> mb-0">
                                        <?php echo $percentage; ?>%
                                    </div>
                                    <small class="text-muted">Overall Performance</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div class="text-center py-5 bg-light rounded">
                    <div class="mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-clipboard-x text-muted" viewBox="0 0 16 16">
                            <path d="M6.5 0A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3Zm3 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3Z"/>
                            <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1A2.5 2.5 0 0 1 9.5 5h-3A2.5 2.5 0 0 1 4 2.5v-1ZM6.854 7.146a.5.5 0 1 0-.708.708L7.293 9l-1.147 1.146a.5.5 0 0 0 .708.708L8 9.707l1.146 1.147a.5.5 0 0 0 .708-.708L8.707 9l1.147-1.146a.5.5 0 0 0-.708-.708L8 8.293 6.854 7.146Z"/>
                        </svg>
                    </div>
                    <h3 class="h4 mb-3">No Results Found</h3>
                    <p class="text-muted mb-4">You haven't taken any quizzes yet. Start a quiz to see your results here!</p>
                    <a href="#available-quizzes" class="btn btn-primary">
                        View Available Quizzes
                    </a>
                </div>
                <?php
            }
            ?>
        </div>

        <div id="available-quizzes">
            <h2 class="text-center main-heading mb-4">Available Quizzes</h2>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>