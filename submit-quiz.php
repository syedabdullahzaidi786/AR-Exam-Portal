<?php
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['quiz_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$quiz_id = $_SESSION['quiz_id'];

// Get submitted answers
$submitted_answers = $_POST['answers'] ?? [];

// Fetch correct answers from database
$stmt = $pdo->prepare("SELECT id, correct_answer FROM quiz_questions WHERE quiz_id = ?");
$stmt->execute([$quiz_id]);
$correct_answers = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Calculate score
$score = 0;
$total_questions = count($correct_answers);

foreach ($submitted_answers as $question_id => $answer) {
    if (isset($correct_answers[$question_id]) && $correct_answers[$question_id] === $answer) {
        $score++;
    }
}

// Store result in database
try {
    $stmt = $pdo->prepare("INSERT INTO quiz_results (user_id, quiz_id, score, total_questions) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $quiz_id, $score, $total_questions]);

    // Store score in session for display
    $_SESSION['last_score'] = $score;
    $_SESSION['total_questions'] = $total_questions;

    // Clear quiz session data
    unset($_SESSION['quiz_id']);
    unset($_SESSION['start_time']);

    header("Location: completion.php");
    exit();
} catch (PDOException $e) {
    die("Error storing quiz results: " . $e->getMessage());
}
?>