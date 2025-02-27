<?php

session_start();

require_once "helpers/ViewHelper.php";
require_once "controllers/QuizController.php";

$userLoggedIn = isset($_SESSION['username']);

if (!$userLoggedIn) {
    ViewHelper::redirect(BASE_URL . "index.php/login");
}

$pageTitle = 'Create Quiz - Quiz Platform';

ob_start();
?>

<div class="container">
    <h2 style="text-align: center;">Create a New Quiz</h2>

    <form action="<?= BASE_URL ?>index.php/process_create_quiz" method="POST" class="quiz-form">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" required></textarea><br>

        <div id="questions-container">
            <div class="question-item">
                <label for="question1">Question Text:</label>
                <input type="text" id="question1" name="questions[0][question_text]" required><br>
                <label for="option1">Option 1:</label>
                <input type="text" id="option1" name="questions[0][options][0]" required><br>
                <label for="option2">Option 2:</label>
                <input type="text" id="option2" name="questions[0][options][1]" required><br>
                <label for="option3">Option 3:</label>
                <input type="text" id="option3" name="questions[0][options][2]" required><br>
                <label for="option4">Option 4:</label>
                <input type="text" id="option4" name="questions[0][options][3]" required><br>
                <label for="correct_option">Correct Option (1-4):</label>
                <input type="number" id="correct_option" name="questions[0][correct_option]" min="1" max="4" required><br>
            </div>
        </div>

        <button type="button" onclick="addQuestion()" style="border-radius: 8px;">Add Another Question</button><br>

        <input type="submit" value="Create Quiz" class="btn btn-primary" style="margin: 0 45%;">
    </form>
</div>
<script src="../assets/js/script.js"></script>
<?php

$content = ob_get_clean();

require_once 'template.php';
?>
