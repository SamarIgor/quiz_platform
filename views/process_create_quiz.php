<?php

require_once "controllers/QuizController.php";
require_once "helpers/ViewHelper.php";

session_start();

if (!isset($_SESSION['user_id'])) {
    ViewHelper::redirect(BASE_URL . "index.php/login");
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = $_POST["title"];
    $description = $_POST["description"];
    $questions = $_POST["questions"]; 

    $userId = $_SESSION['user_id'];

    $result = QuizController::createQuiz($userId, $title, $description, $questions);

    if ($result) {
        echo "Quiz created successfully!";
        header("Location: " . BASE_URL);
        exit();
    } else {
        include "views/create_quiz.php";
        echo "<p>Failed to create quiz. Please try again.</p>";
    }
}
?>
