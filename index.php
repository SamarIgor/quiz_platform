<?php

require_once "controllers/QuizController.php";
require_once "helpers/ViewHelper.php"; 

define("BASE_URL", rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . '/');

$path = isset($_SERVER["PATH_INFO"]) ? trim($_SERVER["PATH_INFO"], "/") : "";

$urls = [
    "" => function () {
        include "views/view_quizzes.php";
    },
    "login" => function () {
        include "views/login.php";
    },
    "register" => function () {
        include "views/register.php";
    },
    "create_quiz" => function () {
        include "views/create_quiz.php";
    },
    "view-quizzes" => function () {
        ViewHelper::redirect(BASE_URL);
    },
    "quiz" => function () {
        $quizId = isset($_GET['id']) ? $_GET['id'] : null;
        if ($quizId) {
            $quiz = QuizController::getQuizById($quizId);
            if ($quiz) {
                include "views/quiz.php";
            } else {
                ViewHelper::error404("Quiz not found");
            }
        } else {
            ViewHelper::error400("Invalid request: Quiz ID not provided");
        }
    },
    "edit_quiz" => function () {
        $quizId = isset($_GET['id']) ? $_GET['id'] : null;
        if ($quizId) {
            $quiz = QuizController::getQuizById($quizId);
            if ($quiz) {
                $questions = QuizController::getQuizQuestions($quizId);
                include "views/edit_quiz.php";
            } else {
                ViewHelper::error404("Quiz not found");
            }
        } else {
            ViewHelper::error400("Invalid request: Quiz ID not provided");
        }
    },  
    "process_edit_quiz" => function () {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $quizId = isset($_POST['quiz_id']) ? $_POST['quiz_id'] : null;
            $title = isset($_POST['title']) ? $_POST['title'] : '';
            $description = isset($_POST['description']) ? $_POST['description'] : '';
            $questions = isset($_POST['questions']) ? $_POST['questions'] : [];
            if (!$quizId || empty($title) || empty($description) || empty($questions)) {
                ViewHelper::error400("Invalid request: Quiz details missing");
                return;
            }

            $success = QuizController::updateQuiz($quizId, $title, $description, $questions);

            if ($success) {
                ViewHelper::redirect(BASE_URL);
            } else {
                ViewHelper::error500("Failed to update quiz");
            }
        } else {
            ViewHelper::error400("Invalid request method");
        }
    },  
    "delete_quiz" => function () {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $quizId = isset($_POST['quiz_id']) ? $_POST['quiz_id'] : null;
            if ($quizId) {
                $success = QuizController::deleteQuiz($quizId);
                var_dump($success);
                if ($success) {
                    ViewHelper::redirect(BASE_URL);
                    exit;
                } else {
                    ViewHelper::error500("Failed to delete quiz.");
                    exit;
                }
            } else {
                ViewHelper::error400("Invalid quiz_id");
                exit;
            }
        } else {
            http_response_code(400);
            exit;
        }
    },
    "my_quizzes" => function() {
       include "views/my_quizzes.php";
    },
    "search_quizzes" => function () {
        include "views/search_quizzes.php";
    },
    "logout" => function () {
        QuizController::logoutUser();
        ViewHelper::redirect(BASE_URL);
    },
    "process_create_quiz" => function () {
        include "views/process_create_quiz.php";
    },
    "grade_quiz" => function(){
        include "views/grade_quiz.php";
    }
    
];

try {
    if (isset($urls[$path])) {
        $urls[$path](); 
    } else {
        ViewHelper::error404("Page not found");
    }
} catch (Exception $e) {
    ViewHelper::error500("Internal Server Error", $e->getMessage());
}
?>
