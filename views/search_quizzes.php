<?php

session_start();

require_once "helpers/ViewHelper.php";
require_once "controllers/QuizController.php";

$pageTitle = 'Search Quizzes - Quiz Platform';

ob_start();
?>

<div class="container mt-4">
    <h2 style="text-align: center;">Search for Quizzes</h2>
            
        <form action="<?php echo BASE_URL . 'index.php/search_quizzes'; ?>" method="GET" class="search-form">
            <div class="input-group">
                <input type="text" name="search_query" class="form-control" placeholder="Search by title...">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>
        <?php
            $userLoggedIn = isset($_SESSION['username']);
            if ($userLoggedIn) {
                echo '<div class="text-left mb-1">';
                echo '<a href="' . BASE_URL . 'index.php/create_quiz" class="btn btn-success">Create Quiz</a>';
                echo '</div>';
            }
        ?>
    <div class="quiz-list mt-4">
        <?php

        $searchQuery = isset($_GET['search_query']) ? $_GET['search_query'] : '';

        if (!empty($searchQuery)) {
            $quizzes = QuizController::getQuizzesByTitle($searchQuery);
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
                echo '<p>No quizzes available.</p>';
            }
        } else {
            $quizzes = QuizController::getAllQuizzes();
            $loggedInUserId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;    
            if(empty($quizzes)){
                echo '<p>No quizzes available.</p>';    
            } else {
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
            }
        }
        ?>
    </div>
</div>

<?php

$content = ob_get_clean();

require_once 'template.php';
?>
