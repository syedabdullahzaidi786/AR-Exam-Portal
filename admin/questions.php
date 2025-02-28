<?php
require_once '../config.php';

if(!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle question deletion
if(isset($_POST['delete_question'])) {
    $question_id = $_POST['question_id'];
    $stmt = $pdo->prepare("DELETE FROM quiz_questions WHERE id = ?");
    $stmt->execute([$question_id]);
    header("Location: questions.php");
    exit();
}

// Handle question addition/update
if(isset($_POST['save_question'])) {
    $quiz_id = $_POST['quiz_id'];
    $question_text = $_POST['question_text'];
    $option_a = $_POST['option_a'];
    $option_b = $_POST['option_b'];
    $option_c = $_POST['option_c'];
    $option_d = $_POST['option_d'];
    $correct_answer = $_POST['correct_answer'];
    
    if(isset($_POST['question_id'])) {
        // Update existing question
        $stmt = $pdo->prepare("UPDATE quiz_questions SET quiz_id = ?, question_text = ?, 
                              option_a = ?, option_b = ?, option_c = ?, option_d = ?, 
                              correct_answer = ? WHERE id = ?");
        $stmt->execute([$quiz_id, $question_text, $option_a, $option_b, $option_c, 
                       $option_d, $correct_answer, $_POST['question_id']]);
    } else {
        // Add new question
        $stmt = $pdo->prepare("INSERT INTO quiz_questions (quiz_id, question_text, option_a, 
                              option_b, option_c, option_d, correct_answer) 
                              VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$quiz_id, $question_text, $option_a, $option_b, $option_c, 
                       $option_d, $correct_answer]);
    }
    header("Location: questions.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Questions - Admin</title>
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
        .card {
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        .question-card {
            margin-bottom: 1rem;
            border-left: 4px solid #1a237e;
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
                            <a class="nav-link active" href="questions.php">Manage Questions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">Users</a>
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
                    <h1 class="h2">Manage Questions</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#questionModal">
                        Add New Question
                    </button>
                </div>

                <!-- Quiz Filter -->
                <div class="mb-4">
                    <select class="form-select" id="quizFilter" onchange="filterQuestions(this.value)">
                        <option value="">All Quizzes</option>
                        <?php
                        $quizzes = $pdo->query("SELECT * FROM quizzes")->fetchAll();
                        foreach($quizzes as $quiz) {
                            echo "<option value='{$quiz['id']}'>{$quiz['title']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Questions List -->
                <div class="questions-container">
                    <?php
                    $stmt = $pdo->query("
                        SELECT q.*, qz.title as quiz_title 
                        FROM quiz_questions q 
                        JOIN quizzes qz ON q.quiz_id = qz.id 
                        ORDER BY q.quiz_id, q.id
                    ");
                    while ($question = $stmt->fetch()) {
                    ?>
                    <div class="card question-card" data-quiz-id="<?php echo $question['quiz_id']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($question['question_text']); ?></h5>
                            <p class="text-muted">Quiz: <?php echo $question['quiz_title']; ?></p>
                            <div class="options-grid mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2">
                                            A) <?php echo htmlspecialchars($question['option_a']); ?>
                                            <?php if($question['correct_answer'] == 'a') echo ' ✓'; ?>
                                        </p>
                                        <p class="mb-2">
                                            B) <?php echo htmlspecialchars($question['option_b']); ?>
                                            <?php if($question['correct_answer'] == 'b') echo ' ✓'; ?>
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2">
                                            C) <?php echo htmlspecialchars($question['option_c']); ?>
                                            <?php if($question['correct_answer'] == 'c') echo ' ✓'; ?>
                                        </p>
                                        <p class="mb-2">
                                            D) <?php echo htmlspecialchars($question['option_d']); ?>
                                            <?php if($question['correct_answer'] == 'd') echo ' ✓'; ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="btn-group">
                                <button class="btn btn-sm btn-primary edit-question" 
                                        data-question='<?php echo json_encode($question); ?>'>
                                    Edit
                                </button>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Are you sure?');">
                                    <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                                    <button type="submit" name="delete_question" class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Question Modal -->
    <div class="modal fade" id="questionModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Question</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="questionForm" method="POST">
                        <input type="hidden" name="question_id" id="question_id">
                        <div class="mb-3">
                            <label class="form-label">Select Quiz</label>
                            <select name="quiz_id" class="form-select" required>
                                <?php
                                foreach($quizzes as $quiz) {
                                    echo "<option value='{$quiz['id']}'>{$quiz['title']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Question Text</label>
                            <textarea name="question_text" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Option A</label>
                                    <input type="text" name="option_a" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Option B</label>
                                    <input type="text" name="option_b" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Option C</label>
                                    <input type="text" name="option_c" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Option D</label>
                                    <input type="text" name="option_d" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correct Answer</label>
                            <select name="correct_answer" class="form-select" required>
                                <option value="a">Option A</option>
                                <option value="b">Option B</option>
                                <option value="c">Option C</option>
                                <option value="d">Option D</option>
                            </select>
                        </div>
                        <button type="submit" name="save_question" class="btn btn-primary">Save Question</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle edit question button clicks
        document.querySelectorAll('.edit-question').forEach(button => {
            button.addEventListener('click', () => {
                const question = JSON.parse(button.dataset.question);
                document.getElementById('question_id').value = question.id;
                document.querySelector('[name="quiz_id"]').value = question.quiz_id;
                document.querySelector('[name="question_text"]').value = question.question_text;
                document.querySelector('[name="option_a"]').value = question.option_a;
                document.querySelector('[name="option_b"]').value = question.option_b;
                document.querySelector('[name="option_c"]').value = question.option_c;
                document.querySelector('[name="option_d"]').value = question.option_d;
                document.querySelector('[name="correct_answer"]').value = question.correct_answer;
                new bootstrap.Modal(document.getElementById('questionModal')).show();
            });
        });

        // Filter questions by quiz
        function filterQuestions(quizId) {
            document.querySelectorAll('.question-card').forEach(card => {
                if (!quizId || card.dataset.quizId === quizId) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>