<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $quiz_id = $_POST['quiz_id'];
    $exam_key = $_POST['exam_key'];

    // Verify exam key
    $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE id = ? AND exam_key = ?");
    $stmt->execute([$quiz_id, $exam_key]);
    $quiz = $stmt->fetch();

    if ($quiz) {
        $_SESSION['quiz_id'] = $quiz_id;
        $_SESSION['start_time'] = time(); // Store start time for timer
        header("Location: quiz.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid exam key!";
        header("Location: rules.php?quiz_id=" . $quiz_id);
        exit();
    }
}
?>