<?php

session_start();

require_once "helpers/ViewHelper.php";
require_once "controllers/QuizController.php";

$userLoggedIn = isset($_SESSION['username']);

if (!$userLoggedIn || $_SESSION['user_id'] !== $quiz['user_id']) {
    ViewHelper::redirect(BASE_URL . "index.php/login");
}

$pageTitle = 'Edit Quiz - Quiz Platform';

ob_start();
?>

<div class="container">
    <h2 style="text-align: center;">Edit Quiz</h2>
    <form action="<?= BASE_URL ?>index.php/process_edit_quiz" method="POST" class="quiz-form">
        <input type="hidden" name="quiz_id" value="<?= htmlspecialchars($quiz['quiz_id'] ?? '') ?>">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?= htmlspecialchars($quiz['title'] ?? '') ?>" required><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" rows="4" required><?= htmlspecialchars($quiz['description'] ?? '') ?></textarea><br>

        <div id="questions-container">
            <?php if (!empty($questions) && is_array($questions)): ?>
                <?php foreach ($questions as $index => $question): ?>
                    <div class="question-item">
                        <label for="question<?= $index + 1 ?>">Question Text:</label>
                        <input type="text" id="question<?= $index + 1 ?>" name="questions[<?= $index ?>][question_text]" value="<?= htmlspecialchars($question['question_text'] ?? '') ?>" required><br>

                        
                        <label for="option1">Option 1:</label>
                        <input type="text" id="option1" name="questions[<?= $index ?>][options][0]" value="<?= htmlspecialchars($question['option_1'] ?? '') ?>" required><br>
                        
                        <label for="option2">Option 2:</label>
                        <input type="text" id="option2" name="questions[<?= $index ?>][options][1]" value="<?= htmlspecialchars($question['option_2'] ?? '') ?>" required><br>
                        
                        <label for="option3">Option 3:</label>
                        <input type="text" id="option3" name="questions[<?= $index ?>][options][2]" value="<?= htmlspecialchars($question['option_3'] ?? '') ?>" required><br>
                        
                        <label for="option4">Option 4:</label>
                        <input type="text" id="option4" name="questions[<?= $index ?>][options][3]" value="<?= htmlspecialchars($question['option_4'] ?? '') ?>" required><br>

                        <label for="correct_option">Correct Option (1-4):</label>
                        <input type="number" id="correct_option" name="questions[<?= $index ?>][correct_option]" min="1" max="4" value="<?= htmlspecialchars($question['correct_option'] ?? '') ?>" required><br>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <button type="button" onclick="addQuestion()" style="border-radius: 8px;">Add Another Question</button><br>          
        <input type="submit" value="Update Quiz" class="btn" style="margin: 0 45%;">
        
    </form>
    <form id="deleteForm" action="<?= BASE_URL ?>index.php/delete_quiz" method="POST">
        <input type="hidden" name="quiz_id" value="<?= htmlspecialchars($quiz['quiz_id'] ?? '') ?>">
        <input type="radio" id="confirmDeleteRadio" name="confirmDelete" value="yes" required>
        <label for="confirmDeleteRadio">Confirm Deletion</label>
        <button type="submit" id="deleteButton" class="dangerbtn btn-danger mt-1" style="border-radius: 8px;">Delete Quiz</button>
    </form>
</div>

<script src="../assets/js/script.js"></script>
<script>
    document.getElementById('deleteForm').addEventListener('submit', function(event) {
        if (!document.getElementById('confirmDeleteRadio').checked) {
            alert('Please confirm deletion by selecting the radio button.');
            event.preventDefault();
        }
    });
</script>

<?php

$content = ob_get_clean();

require_once 'template.php';
?>
