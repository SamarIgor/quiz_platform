<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
</head>
<body>

<header class="header d-flex justify-content-between align-items-center py-3" style="background-color: #343a40;">
    <a href="/quiz" class="header-icon ml-3" style="text-decoration: none; color: #ffffff; font-weight: bold; border-bottom: 2px solid transparent; transition: border-bottom-color 0.3s;" onmouseover="this.style.borderBottomColor = '#ffffff';" onmouseout="this.style.borderBottomColor = 'transparent';">Quiz Platform</a>

    <h1 class="text-center mx-auto">Quiz Platform</h1>
    <div class="header-right mr-3">
        <?php
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
    <?php echo $content; ?>
</div>

</body>
</html>
