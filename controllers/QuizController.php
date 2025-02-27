<?php

require_once 'models/QuizDB.php'; 
require_once 'helpers/ViewHelper.php';

class QuizController {

    /**
     * Handles user registration.
     *
     * @param string $username
     * @param string $password
     * @param string $email
     * @return bool True if registration is successful, false otherwise
     */
    public static function registerUser($username, $password, $email) {
        if (empty($username) || empty($password) || empty($email)) {
            return false;
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $pdo = DBInit::getInstance(); 

            $stmt = $pdo->prepare("SELECT COUNT(*) AS count FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && $row['count'] > 0) {
                return false;
            }

            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, email) VALUES (:username, :password_hash, :email)");
            $result = $stmt->execute([
                'username' => $username,
                'password_hash' => $passwordHash,
                'email' => $email
            ]);

            return $result; 
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
            return false; 
        }
    }

    /**
     * Handles user login and starts a session upon successful login.
     *
     * @param string $username
     * @param string $password
     * @return bool True if login is successful, false otherwise
     */
    public static function loginUser($username, $password) {
        try {
            $user = QuizDB::getUserByUsername($username);
    
            if (!$user) {
                ViewHelper::error400("User not found for username: $username");
                return false;
            }
            $storedPasswordHash = $user['password_hash'];
            if (password_verify($password, $storedPasswordHash)) {
                // Start a new session and store user data
                session_start();
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                return true; 
            } else {
                return false; 
            }
        } catch (PDOException $e) {
            return false; 
        }
    }
    

    /**
     * Checks if a user is logged in by verifying session data.
     *
     * @return bool True if user is logged in, false otherwise
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Logs out the currently logged-in user by destroying the session.
     */
    public static function logoutUser() {
        session_start(); 
        session_destroy(); 
    }

    /**
     * Handles quiz creation.
     *
     * @param int $userId
     * @param string $title
     * @param string $description
     * @param array $questions Array of questions for the quiz
     * @return bool True if quiz creation is successful, false otherwise
     */
    public static function createQuiz($userId, $title, $description, $questions) {
        return QuizDB::createQuizWithQuestions($userId, $title, $description, $questions);
    }

    /**
     * Handles quiz deletion.
     *
     * @param int $quizId
     * @return bool True if quiz deletion is successful, false otherwise
     */
    public static function deleteQuiz($quizId) {
        return QuizDB::deleteQuiz($quizId);
    }

    /**
     * Retrieves all quizzes from the database.
     *
     * @return array List of quizzes as associative arrays
     */
    public static function getAllQuizzes() {
        // Retrieve all quizzes using QuizDB class
        return QuizDB::getAllQuizzes();
    }

    public static function getQuizById($quizId) {
        return QuizDB::getQuizById($quizId);
    }
    
    public static function getQuizQuestions($quizId) {
        return QuizDB::getQuizQuestions($quizId);
    }
    
    public static function gradeQuiz($quizId, $answers, $timeTaken) {
        return QuizDB::gradeQuiz($quizId, $answers, $timeTaken);
    }

    public static function getOptionValueByIndex($quizId, $questionId, $selectedOptionIndex) {
        try {
            $pdo = DBInit::getInstance();
    
            $stmt = $pdo->prepare("SELECT option_1, option_2, option_3, option_4 FROM questions WHERE quiz_id = :quiz_id AND question_id = :question_id");
            $stmt->execute([
                'quiz_id' => $quizId,
                'question_id' => $questionId
            ]);
            $options = $stmt->fetch(PDO::FETCH_ASSOC);

            switch ($selectedOptionIndex) {
                case 0:
                    return 1;
                case 1:
                    return 2;
                case 2:
                    return 3;
                case 3:
                    return 4;
                default:
                    return null; 
            }
        } catch (PDOException $e) {
            error_log("Error retrieving option value: " . $e->getMessage());
            return null; 
        }
    }

    public static function updateQuiz($quizId, $title, $description, $questions) {
        return QuizDB::updateQuizWithQuestions($quizId, $title, $description, $questions);
    }
    
    public static function getQuizzesByUser($userId) {
        try {
            $pdo = DBInit::getInstance(); 

            $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $userId]);
            $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $quizzes;
        } catch (PDOException $e) {
            error_log("Error fetching quizzes by user: " . $e->getMessage());
            return []; 
        }
    }

    public static function getQuizzesByTitle($searchQuery) {
        try {
            $pdo = DBInit::getInstance();

            $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE title LIKE :search_query");
            $stmt->execute(['search_query' => '%' . $searchQuery . '%']);
            $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $quizzes;
        } catch (PDOException $e) {
            
            error_log("Error fetching quizzes by title: " . $e->getMessage());
            return []; 
        }
    }
    
}

?>
