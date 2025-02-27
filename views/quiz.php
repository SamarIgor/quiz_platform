<?php

require_once "controllers/QuizController.php";

$quizId = isset($_GET['id']) ? $_GET['id'] : null;

if ($quizId) {
    $quiz = QuizController::getQuizById($quizId);

    ob_start();

    if ($quiz) {
        $questions = QuizController::getQuizQuestions($quizId);

        echo '<h2 style="text-align: center;">Quiz Questions</h2>';


        if (!empty($questions)) {
            echo '<form action="' . BASE_URL . 'index.php/grade_quiz" method="POST" onsubmit="return validateQuiz()" style="max-width: 500px; margin: 0 auto; padding: 20px; background-color: #f8f9fa; border: 1px solid #ced4da; border-radius: 5px;">'; 

            foreach ($questions as $index => $question) {
                echo '<div class="question" style="margin-bottom: 20px; border-bottom: 1px solid #ccc; padding-bottom: 20px;">';
                echo '<p style="font-weight: bold;">' . ($index + 1) . '. ' . htmlspecialchars($question['question_text']) . '</p>';

                $options = array(
                    $question['option_1'],
                    $question['option_2'],
                    $question['option_3'],
                    $question['option_4']
                );

                foreach ($options as $key => $option) {
                    echo '<label style="display: block; margin-bottom: 10px;">';
                    echo '<input type="radio" name="answers[' . $question['question_id'] . ']" value="' . $key . '" style="margin-right: 10px;">';
                    echo htmlspecialchars($option);
                    echo '</label>';
                }
            
                echo '</div>';
            }
            
            echo '<input type="submit" value="Submit Quiz" class="btn btn-primary" style="margin: 0 auto; display: block;">'; // Applied btn and btn-primary classes
            echo '<input type="hidden" name="quiz_id" value="' . $quizId . '">';
            echo '</form>';
        } else {
            echo '<p>No questions found for this quiz.</p>';
        }
    } else {
        echo '<p>Quiz not found.</p>';
    }

    $content = ob_get_clean();

    $pageTitle = 'Quiz - Quiz Platform';

    require_once 'template.php';
} else {
    echo '<p>Invalid request: Quiz ID not provided.</p>';
}
?>
<script>
    let startTime;

    window.onload = function() {
        startTime = Date.now(); 
    };

    function getElapsedTime() {
        const currentTime = Date.now();
        const elapsedTime = Math.floor((currentTime - startTime) / 1000); 
        return elapsedTime;
    }

    function validateQuiz() {
        const questions = document.getElementsByClassName('question');

        for (let i = 0; i < questions.length; i++) {
            const questionInputs = questions[i].querySelectorAll('input[type="radio"]');
            let answered = false;

            for (let j = 0; j < questionInputs.length; j++) {
                if (questionInputs[j].checked) {
                    answered = true;
                    break;
                }
            }

            if (!answered) {
                alert('Please answer all questions.');
                return false; 
            }
        }

        const timeTaken = getElapsedTime();

        const form = document.querySelector('form');
        const timeInput = document.createElement('input');
        timeInput.type = 'hidden';
        timeInput.name = 'time_taken';
        timeInput.value = timeTaken;
        form.appendChild(timeInput);

        return true;
    }
</script>
