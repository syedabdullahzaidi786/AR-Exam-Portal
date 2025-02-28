<?php 
require_once 'config.php';
if (!isset($_SESSION['user_id']) || !isset($_GET['quiz_id'])) {
    header("Location: login.php");
    exit();
}

$quiz_id = $_GET['quiz_id'];
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch();

if (!$quiz) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Rules - <?php echo $quiz['title']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .rules-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-top: 2rem;
        }
        
        .rules-header {
            background-color: #1976d2;
            color: white;
            padding: 1.5rem;
            border-radius: 15px 15px 0 0;
        }
        
        .rules-content {
            padding: 2rem;
        }
        
        .rules-list {
            background-color: #e3f2fd;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .rules-list ul {
            margin-bottom: 0;
        }
        
        .rules-list li {
            margin-bottom: 0.5rem;
            color: #1565c0;
        }
        
        .important-instructions {
            background-color: #fff3e0;
            border-radius: 10px;
            padding: 1.5rem;
        }
        
        .important-instructions h5 {
            color: #e65100;
            margin-bottom: 1rem;
        }
        
        .important-instructions li {
            color: #f57c00;
            margin-bottom: 0.5rem;
        }
        
        .exam-key-section {
            margin-top: 2rem;
            padding: 1.5rem;
            border-top: 1px solid #e0e0e0;
        }
        
        .btn-start {
            background-color: #2196f3;
            border: none;
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        
        .btn-start:hover {
            background-color: #1976d2;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="rules-card">
                    <div class="rules-header">
                        <h2 class="text-center mb-0">Rules & Regulations</h2>
                    </div>
                    <div class="rules-content">
                        <h4 class="mb-4"><?php echo $quiz['title']; ?></h4>
                        
                        <div class="rules-list">
                            <ul>
                                <li>Duration: <?php echo $quiz['duration']; ?> minutes</li>
                                <li>Total Questions: <?php echo $quiz['total_questions']; ?></li>
                                <li>Each question carries equal marks</li>
                                <li>No negative marking</li>
                                <li>Once submitted, you cannot retake the quiz</li>
                            </ul>
                        </div>

                        <div class="important-instructions">
                            <h5>Important Instructions:</h5>
                            <ul>
                                <li>Ensure stable internet connection</li>
                                <li>Do not refresh the page during quiz</li>
                                <li>Timer will start once you enter the quiz</li>
                                <li>Submit before time expires</li>
                            </ul>
                        </div>

                        <div class="exam-key-section">
                            <form method="POST" action="start-quiz.php">
                                <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">
                                <div class="mb-3">
                                    <label class="form-label">Enter Exam Key</label>
                                    <input type="text" name="exam_key" class="form-control" required>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" required id="agree">
                                    <label class="form-check-label" for="agree">
                                        I have read and agree to the rules
                                    </label>
                                </div>
                                <button type="submit" class="btn btn-primary btn-start w-100">Start Quiz</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>