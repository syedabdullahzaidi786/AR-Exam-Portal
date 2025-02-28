<?php

include '../config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Handle quiz deletion
if (isset($_POST['delete_quiz'])) {
    $quiz_id = intval($_POST['quiz_id']); // Ensure it's an integer
    $stmt = $pdo->prepare("DELETE FROM quizzes WHERE id = ?");
    $stmt->execute([$quiz_id]);
    header("Location: quizzes.php");
    exit();
}

// Handle quiz addition/update
if (isset($_POST['save_quiz'])) {
    $title = trim(htmlspecialchars($_POST['title']));
    $course_code = trim(htmlspecialchars($_POST['course_code']));
    $duration = intval($_POST['duration']);
    $total_questions = intval($_POST['total_questions']);
    $exam_key = trim(htmlspecialchars($_POST['exam_key']));
    $status = trim(htmlspecialchars($_POST['status']));

    if (!empty($_POST['quiz_id'])) {
        // Update existing quiz
        $quiz_id = intval($_POST['quiz_id']);
        $stmt = $pdo->prepare("UPDATE quizzes SET title = ?, course_code = ?, duration = ?, 
                              total_questions = ?, exam_key = ?, status = ? WHERE id = ?");
        $stmt->execute([$title, $course_code, $duration, $total_questions, $exam_key, $status, $quiz_id]);
    } else {
        // Add new quiz
        $stmt = $pdo->prepare("INSERT INTO quizzes (title, course_code, duration, 
                              total_questions, exam_key, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $course_code, $duration, $total_questions, $exam_key, $status]);
    }
    header("Location: quizzes.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Quizzes - Admin</title>
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
        .table-container {
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
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
                        <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link active" href="quizzes.php">Manage Quizzes</a></li>
                        <li class="nav-item"><a class="nav-link" href="questions.php">Manage Questions</a></li>
                        <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
                        <li class="nav-item"><a class="nav-link" href="results.php">Results</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>

            <!-- Main content -->
            <div class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Manage Quizzes</h1>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quizModal">Add New Quiz</button>
                </div>

                <!-- Quizzes Table -->
                <div class="table-container">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Course Code</th>
                                <th>Duration</th>
                                <th>Questions</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM quizzes ORDER BY id DESC");
                            while ($quiz = $stmt->fetch()) {
                                echo "<tr>";
                                echo "<td>{$quiz['title']}</td>";
                                echo "<td>{$quiz['course_code']}</td>";
                                echo "<td>{$quiz['duration']} mins</td>";
                                echo "<td>{$quiz['total_questions']}</td>";
                                echo "<td>{$quiz['status']}</td>";
                                echo "<td>
                                        <button class='btn btn-sm btn-primary edit-quiz' data-quiz='" . htmlspecialchars(json_encode($quiz), ENT_QUOTES, 'UTF-8') . "'>
                                            Edit
                                        </button>
                                        <form method='POST' class='d-inline' onsubmit='return confirm(\"Are you sure?\");'>
                                            <input type='hidden' name='quiz_id' value='{$quiz['id']}'>
                                            <button type='submit' name='delete_quiz' class='btn btn-sm btn-danger'>Delete</button>
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

    <!-- Quiz Modal -->
    <div class="modal fade" id="quizModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add/Edit Quiz</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="quizForm" method="POST">
                        <input type="hidden" name="quiz_id" id="quiz_id">
                        <div class="mb-3"><label class="form-label">Title</label><input type="text" id="title" name="title" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Course Code</label><input type="text" id="course_code" name="course_code" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Duration (minutes)</label><input type="number" id="duration" name="duration" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Total Questions</label><input type="number" id="total_questions" name="total_questions" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Exam Key</label><input type="text" id="exam_key" name="exam_key" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Status</label><select id="status" name="status" class="form-control" required><option value="open">Open</option><option value="closed">Closed</option></select></div>
                        <button type="submit" name="save_quiz" class="btn btn-primary">Save Quiz</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".edit-quiz").forEach(button => {
        button.addEventListener("click", function () {
            let quiz = JSON.parse(this.getAttribute("data-quiz"));

            document.getElementById("quiz_id").value = quiz.id;
            document.querySelector("input[name='title']").value = quiz.title;
            document.querySelector("input[name='course_code']").value = quiz.course_code;
            document.querySelector("input[name='duration']").value = quiz.duration;
            document.querySelector("input[name='total_questions']").value = quiz.total_questions;
            document.querySelector("input[name='exam_key']").value = quiz.exam_key;
            document.querySelector("select[name='status']").value = quiz.status;

            new bootstrap.Modal(document.getElementById("quizModal")).show();
        });
    });
});
</script>

</body>
</html>
