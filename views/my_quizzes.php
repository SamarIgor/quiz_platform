<?php

session_start();

require_once "helpers/ViewHelper.php";
require_once "controllers/QuizController.php";

$quizzes = QuizController::getQuizzesByUser($_SESSION['user_id']);

$pageTitle = 'My Quizzes';

ob_start();
?>
<h2 style="text-align: center;">My Quizzes</h2>

<?php
$loggedInUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
if (!empty($quizzes)) {
    foreach ($quizzes as $quiz) {
        echo '<div class="card mb-3">';
        echo '<div class="card-body">';
        echo '<h3 class="card-title">Title: ' . htmlspecialchars($quiz['title']) . '</h3>';
        echo '<p class="card-text"> Description: ' . htmlspecialchars($quiz['description']) . '</p>';
        echo '<a href="' . BASE_URL . 'index.php/quiz?id=' . $quiz['quiz_id'] . '" class="btn btn-info">Start Quiz</a>';
        if ($loggedInUserId && $quiz['user_id'] == $loggedInUserId) {
            echo '<a href="' . BASE_URL . 'index.php/edit_quiz?id=' . $quiz['quiz_id'] . '" class="btn btn-warning ml-2">Edit</a>';
        }
        echo '</div>';
        echo '</div>';
    }
} else {
    echo '<p>No quizzes found.</p>';
}
?>

<?php

$content = ob_get_clean();

require_once "template.php";
?>
