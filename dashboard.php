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

        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .result-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }

        .result-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        .result-header {
            padding: 1.5rem;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
        }

        .result-header h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .result-body {
            padding: 1.5rem;
        }

        .quiz-title {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .score-display {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .score-number {
            font-size: 2rem;
            font-weight: 700;
        }

        .score-total {
            color: #666;
            font-size: 0.9rem;
        }

        .percentage-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .percentage-badge.high {
            background-color: #e8f5e9;
            color: #2e7d32;
        }

        .percentage-badge.medium {
            background-color: #fff3e0;
            color: #f57c00;
        }

        .percentage-badge.low {
            background-color: #ffebee;
            color: #c62828;
        }

        .date-info {
            color: #666;
            font-size: 0.9rem;
            margin-top: 1rem;
            text-align: right;
        }

        .no-results {
            background: white;
            border-radius: 15px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .no-results-icon {
            width: 80px;
            height: 80px;
            margin-bottom: 1.5rem;
            color: #666;
        }

        .no-results h3 {
            color: #333;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .no-results p {
            color: #666;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }

        .btn-view-quizzes {
            background: var(--primary-color);
            color: white;
            padding: 0.8rem 2rem;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-view-quizzes:hover {
            background: #1565c0;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .quiz-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
        <img src="logo.webp" alt="GIAIC" class="d-block mx-auto mb-4" height="100">
        
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
                WHERE r.user_id = ? AND r.published = TRUE
                ORDER BY r.submission_time DESC
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $results = $stmt->fetchAll();
            
            if (count($results) > 0) {
                echo '<div class="results-grid">';
                foreach ($results as $result) {
                    $percentage = number_format($result['percentage'], 1);
                    $badgeClass = $result['percentage'] >= 80 ? 'high' : 
                                ($result['percentage'] >= 60 ? 'medium' : 'low');
                    ?>
                    <div class="result-card">
                        <div class="result-header">
                            <h3>Q<?php echo $result['quiz_id']; ?> Result</h3>
                        </div>
                        <div class="result-body">
                            <div class="quiz-title">
                                <?php echo $result['quiz_title']; ?>
                            </div>
                            <div class="score-display">
                                <div>
                                    <div class="score-number"><?php echo $result['score']; ?></div>
                                    <div class="score-total">out of <?php echo $result['total_questions']; ?> questions</div>
                                </div>
                                <div class="percentage-badge <?php echo $badgeClass; ?>">
                                    <?php echo $percentage; ?>%
                                </div>
                            </div>
                            <div class="date-info">
                                Completed on <?php echo date('M d, Y \a\t H:i', strtotime($result['submission_time'])); ?>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                echo '</div>';
            } else {
                ?>
                <div class="no-results">
                    <svg class="no-results-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <h3>No Published Results Yet</h3>
                    <p>Your quiz results will appear here once they are published by the administrator.</p>
                    <a href="#available-quizzes" class="btn btn-view-quizzes">
                        Browse Available Quizzes
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