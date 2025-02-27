<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Quizzes - Quiz Platform</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    
</head>
<body>

<header class="header d-flex justify-content-between align-items-center py-3" style="background-color: #343a40;">
    <a href="/quiz" class="header-icon ml-3" style="text-decoration: none; color: #ffffff; font-weight: bold; border-bottom: 2px solid transparent; transition: border-bottom-color 0.3s;" onmouseover="this.style.borderBottomColor = '#ffffff';" onmouseout="this.style.borderBottomColor = 'transparent';">Quiz Platform</a>
    <h1 class="text-center mx-auto">Quiz Platform</h1>
    <div class="header-right mr-3">
        <?php
        session_start();

        $userLoggedIn = isset($_SESSION['username']);

        if (!$userLoggedIn) {
            echo '<a href="' . BASE_URL . 'index.php/login" class="btn btn-primary">Login</a>';
            echo '<a href="' . BASE_URL . 'index.php/register" class="btn btn-secondary">Register</a>';
        } else {
            echo '<div class="user-info">';
            echo 'Welcome, <a href="' . BASE_URL . 'index.php/my_quizzes" style="color: #ffffff;padding: 5px;">' . htmlspecialchars($_SESSION['username']) . '</a>';
            echo '<a href="' . BASE_URL . 'index.php/logout" class="btn btn-danger ml-2">Logout</a>';
            echo '</div>';
        }
        ?>
    </div>
</header>

<div class="container mt-4">
    
            <h2 style="text-align: center;">Available Quizzes</h2>

            <form action="<?php echo BASE_URL . 'index.php/search_quizzes'; ?>" method="GET" class="search-form">
                <div class="input-group">
                    <input type="text" name="search_query" class="form-control" placeholder="Search by title...">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">Search</button>
                    </div>
                </div>
            </form>
            <?php

            if ($userLoggedIn) {
                echo '<div class="text-left mb-1">';
                echo '<a href="' . BASE_URL . 'index.php/create_quiz" class="btn btn-success">Create Quiz</a>';
                echo '</div>';
            }
            ?>
            <div class="quiz-list mt-4">
                <?php
                require_once "controllers/QuizController.php";

                $quizzes = QuizController::getAllQuizzes();

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
                ?>
            </div>
</div>

</body>
</html>
