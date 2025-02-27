<?php

session_start();

require_once "controllers/QuizController.php";
require_once "helpers/ViewHelper.php";


$pageTitle = 'Quiz Result - Quiz Platform';

ob_start();
?>

<div class="container">
    <?php

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $quizId = $_POST["quiz_id"];
        $answers = $_POST["answers"]; 
        $timeTaken = isset($_POST["time_taken"]) ? $_POST["time_taken"] : 0; 

        $processedAnswers = [];
        foreach ($answers as $questionId => $selectedOption) {
            $selectedOptionValue = QuizController::getOptionValueByIndex($quizId, $questionId, $selectedOption);

            $processedAnswers[$questionId] = $selectedOptionValue;
        }

        $result = QuizController::gradeQuiz($quizId, $processedAnswers, $timeTaken);
        if ($result !== null) {
            echo '<div class="text-center mt-5 mb-3"><h2>Your Results</h2></div>';
            echo '<div class="text-center">';
            echo '<p class="lead">Your Score: <strong>' . $result['score'] . '/' . $result['total_questions'] . '</strong></p>';
            echo '<p>' . $result['message'] . '</p>';
            echo '<a href="' . BASE_URL . 'index.php/quiz?id=' . $quizId . '" class="btn btn-primary mr-2">Try Again</a>';
            echo '<a href="' . BASE_URL . '" class="btn btn-secondary">Home Page</a>';
            echo '</div>';
        } else {
            echo '<p>Failed to grade the quiz. Please try again.</p>';
        }
    } else {
        echo '<p>Invalid request method: POST expected.</p>';
    }
    ?>
</div>

<?php

$content = ob_get_clean();

require_once 'template.php';
?>
