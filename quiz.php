<?php 
require_once 'config.php';

// Check if user is logged in and quiz is started
if (!isset($_SESSION['user_id']) || !isset($_SESSION['quiz_id'])) {
    header("Location: login.php");
    exit();
}

$quiz_id = $_SESSION['quiz_id'];

// Fetch quiz details
$stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch();

// Fetch questions from database
$stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE quiz_id = ? ORDER BY RAND()");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll();

// Calculate remaining time
$elapsed_time = time() - $_SESSION['start_time'];
$remaining_time = ($quiz['duration'] * 60) - $elapsed_time;

if ($remaining_time <= 0) {
    // Time's up - auto submit
    header("Location: submit-quiz.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $quiz['title']; ?> - Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><?php echo $quiz['title']; ?></h5>
                        <div id="timer" class="badge bg-light text-dark"></div>
                    </div>
                    <div class="card-body">
                        <form id="quizForm" method="POST" action="submit-quiz.php">
                            <?php foreach($questions as $index => $question) { ?>
                            <div class="question mb-4">
                                <h5><?php echo ($index + 1) . ". " . $question['question_text']; ?></h5>
                                <div class="options">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="answers[<?php echo $question['id']; ?>]" value="a" required>
                                        <label class="form-check-label">
                                            <?php echo $question['option_a']; ?>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="answers[<?php echo $question['id']; ?>]" value="b">
                                        <label class="form-check-label">
                                            <?php echo $question['option_b']; ?>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="answers[<?php echo $question['id']; ?>]" value="c">
                                        <label class="form-check-label">
                                            <?php echo $question['option_c']; ?>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" 
                                               name="answers[<?php echo $question['id']; ?>]" value="d">
                                        <label class="form-check-label">
                                            <?php echo $question['option_d']; ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                            <button type="submit" class="btn btn-primary w-100">Submit Quiz</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Timer functionality
        let timeLeft = <?php echo $remaining_time; ?>;
        const timerElement = document.getElementById('timer');
        
        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft === 0) {
                document.getElementById('quizForm').submit();
            } else {
                timeLeft--;
                setTimeout(updateTimer, 1000);
            }
        }
        
        updateTimer();
    </script>
</body>
</html>