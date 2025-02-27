<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "helpers/ViewHelper.php";
if(isset($_SESSION['username'])){
    ViewHelper::redirect(BASE_URL);
}    

$pageTitle = "Login - Quiz Platform";

ob_start();
?>
    


<div class="container mt-4" style="max-width: 500px;margin: 0 auto;padding: 20px;background-color: #f8f9fa;border: 1px solid #ced4da;border-radius: 5px;">
    <h2 style="text-align: center;">Login</h2>

    <form action="<?= BASE_URL . "index.php/login" ?>" method="POST" style="margin-bottom: 20px;">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required style="width: 100%;padding: 10px;margin-bottom: 10px;border: 1px solid #ced4da;border-radius: 5px;"><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required style="width: 100%;padding: 10px;margin-bottom: 10px;border: 1px solid #ced4da;border-radius: 5px;"><br><br>

        <input type="submit" value="Login" class="btn btn-primary" style="display: block; margin: 0 auto;">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        if (isset($_POST["username"]) && isset($_POST["password"])) {
            $username = $_POST["username"];
            $password = $_POST["password"]; 

            require_once "controllers/QuizController.php";

            $loggedIn = QuizController::loginUser($username, $password);

            if ($loggedIn) {
                
                header("Location: " . BASE_URL);
                exit();
            } else {
                echo '<p class="text-danger">Login failed. Please check your username and password.</p>';
            }
        }
    }
    ?>

    <p>Don't have an account? <a href="<?= BASE_URL . "index.php/register" ?>">Register here</a>.</p>
</div>

<?php

$content = ob_get_clean();

require_once 'template.php';
?>
