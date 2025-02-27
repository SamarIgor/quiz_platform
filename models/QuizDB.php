<?php

require_once 'DBInit.php'; 

class QuizDB {

    /**
     * Retrieves all quizzes from the database.
     *
     * @return array List of quizzes as associative arrays
     */
    

public static function getAllQuizzes() {
    try {
        $pdo = DBInit::getInstance(); 
        
        $offset = 0;
        $limit = 100; 
        
        $quizzes = array();
        do {
            $stmt = $pdo->prepare("SELECT * FROM quizzes LIMIT :limit OFFSET :offset");
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();

            $batchQuizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $quizzes = array_merge($quizzes, $batchQuizzes);

            $offset += $limit;
        } while (!empty($batchQuizzes)); 
        
        return $quizzes;
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}


    /**
     * Retrieves a user from the database based on username and password.
     * Returns null if no user is found.
     *
     * @param string $username
     * @param string $password
     * @return array|null User data as associative array or null if user not found
     */
    public static function getUserByUsername($username) {
        try {
            $pdo = DBInit::getInstance(); 
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            return $user; 
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
            return null; 
        }
    }
    /**
     * Retrieves quizzes created by a specific user.
     *
     * @param int $userId
     * @return array List of quizzes as associative arrays
     */
    public static function getQuizzesByUserId($userId) {
        try {
            $pdo = DBInit::getInstance(); 
            $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE user_id = :user_id");
            $stmt->execute(['user_id' => $userId]);
            $quizzes = $stmt->fetchAll();

            return $quizzes;
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    }

    /**
     * Creates a new quiz in the database along with its questions.
     *
     * @param int $userId
     * @param string $title
     * @param string $description
     * @param array $questions Array of questions (associative arrays) for the quiz
     *                         Example: [ ['question_text' => 'What is 2 + 2?', 'options' => ['4', '5', '6', '8'], 'correct_option' => 0], ... ]
     * @return bool True if quiz creation is successful, false otherwise
     */
    public static function createQuizWithQuestions($userId, $title, $description, $questions) {
        try {
            $pdo = DBInit::getInstance(); 

            $pdo->beginTransaction();

            $stmtQuiz = $pdo->prepare("INSERT INTO quizzes (user_id, title, description) VALUES (:user_id, :title, :description)");
            $stmtQuiz->execute([
                'user_id' => $userId,
                'title' => $title,
                'description' => $description
            ]);

            $quizId = $pdo->lastInsertId();

            $stmtQuestion = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, option_1, option_2, option_3, option_4, correct_option) 
                                           VALUES (:quiz_id, :question_text, :option_1, :option_2, :option_3, :option_4, :correct_option)");
            
            foreach ($questions as $question) {
                $stmtQuestion->execute([
                    'quiz_id' => $quizId,
                    'question_text' => $question['question_text'],
                    'option_1' => $question['options'][0],
                    'option_2' => $question['options'][1],
                    'option_3' => $question['options'][2],
                    'option_4' => $question['options'][3],
                    'correct_option' => $question['correct_option']
                ]);
            }

            $pdo->commit();

            return true; 
        } catch (PDOException $e) {
            $pdo->rollBack();
            die("Error: " . $e->getMessage());
            return false; 
        }
    }

    // Add additional methods for updating/deleting quizzes and other operations as needed...
    /**
     * Retrieves a quiz from the database based on quiz ID.
     *
     * @param int $quizId The ID of the quiz to retrieve
     * @return array|null Quiz data as associative array or null if quiz not found
     */
    public static function getQuizById($quizId) {
        try {
            $pdo = DBInit::getInstance(); 
            $stmt = $pdo->prepare("SELECT * FROM quizzes WHERE quiz_id = :quiz_id");
            $stmt->execute(['quiz_id' => $quizId]);
            $quiz = $stmt->fetch();

            return $quiz; 
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
            return null; 
        }
    }

    /**
     * Retrieves questions of a quiz from the database based on quiz ID.
     *
     * @param int $quizId The ID of the quiz to retrieve questions for
     * @return array List of questions for the quiz as associative arrays
     */
    public static function getQuizQuestions($quizId) {
        try {
            $pdo = DBInit::getInstance(); 
            $stmt = $pdo->prepare("SELECT * FROM questions WHERE quiz_id = :quiz_id");
            $stmt->execute(['quiz_id' => $quizId]);
            $questions = $stmt->fetchAll();
            return $questions; 
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
            return []; 
        }
    }
    
    public static function gradeQuiz($quizId, $answers, $timeTaken) {
        try {
            $pdo = DBInit::getInstance();
    
            $stmt = $pdo->prepare("SELECT question_id, correct_option FROM questions WHERE quiz_id = :quiz_id");
            $stmt->execute(['quiz_id' => $quizId]);
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $score = 0;
            foreach ($questions as $question) {
                $questionId = $question['question_id'];
                $correctOption = $question['correct_option'];
                
                if (isset($answers[$questionId]) && $answers[$questionId] == $correctOption) {
                    $score++;
                }
            }
    
            $totalQuestions = count($questions);
    

            $percentageCorrect = ($totalQuestions > 0) ? ($score / $totalQuestions) * 100 : 0;
    
            if ($percentageCorrect >= 50) {
                $message = "You have answered " . round($percentageCorrect) . "% of the quiz correctly. Congratulations!";
            } else {
                $message = "You have answered " . round($percentageCorrect) . "% of the quiz correctly. Better luck next time.";
            }

            $userId = $_SESSION['user_id'] ?? null;
    
            $stmt = $pdo->prepare("INSERT INTO graded_quizzes (user_id, quiz_id, score, time_taken) 
                                   VALUES (:user_id, :quiz_id, :score, :time_taken)");
            $stmt->execute([
                'user_id' => $userId,
                'quiz_id' => $quizId,
                'score' => $score . "/" . $totalQuestions,
                'time_taken' => $timeTaken
            ]);

            return [
                'score' => $score,
                'total_questions' => $totalQuestions,
                'message' => $message
            ];
        } catch (PDOException $e) {
            error_log("Error grading quiz: " . $e->getMessage());
            return null; 
        }
    }
    
    
    public static function updateQuizWithQuestions($quizId, $title, $description, $questions) {
        try {
            $pdo = DBInit::getInstance();
            
            // Begin a transaction to ensure atomicity
            $pdo->beginTransaction();
    
            // Update quiz details in quizzes table
            $stmtQuiz = $pdo->prepare("UPDATE quizzes SET title = :title, description = :description WHERE quiz_id = :quiz_id");
            $stmtQuiz->execute([
                'title' => $title,
                'description' => $description,
                'quiz_id' => $quizId
            ]);
    
            // Delete existing questions for the quiz
            $stmtDeleteQuestions = $pdo->prepare("DELETE FROM questions WHERE quiz_id = :quiz_id");
            $stmtDeleteQuestions->execute(['quiz_id' => $quizId]);
    
            // Insert updated questions into questions table
            $stmtQuestion = $pdo->prepare("INSERT INTO questions (quiz_id, question_text, option_1, option_2, option_3, option_4, correct_option) 
                                           VALUES (:quiz_id, :question_text, :option_1, :option_2, :option_3, :option_4, :correct_option)");
            
            foreach ($questions as $question) {
                $stmtQuestion->execute([
                    'quiz_id' => $quizId,
                    'question_text' => $question['question_text'],
                    'option_1' => $question['options'][0],
                    'option_2' => $question['options'][1],
                    'option_3' => $question['options'][2],
                    'option_4' => $question['options'][3],
                    'correct_option' => $question['correct_option']
                ]);
            }
    
            // Commit the transaction
            $pdo->commit();
    
            return true; // Return true if quiz update was successful
        } catch (PDOException $e) {
            // Roll back the transaction on error
            $pdo->rollBack();
            // Handle database connection errors
            die("Error: " . $e->getMessage());
            return false; // Return false on error
        }
    }
    
    public static function deleteQuiz($quizId) {
        try {
            $pdo = DBInit::getInstance();
            $pdo->beginTransaction();

            $stmtQuiz = $pdo->prepare("DELETE FROM quizzes WHERE quiz_id = :quiz_id");
            $stmtQuiz->execute(['quiz_id' => $quizId]);

            $stmtQuestions = $pdo->prepare("DELETE FROM questions WHERE quiz_id = :quiz_id");
            $stmtQuestions->execute(['quiz_id' => $quizId]);

            $pdo->commit();

            return true; 
        } catch (PDOException $e) {
            $pdo->rollBack();
            var_dump($e);
            error_log("Error deleting quiz: " . $e->getMessage());
            return false; 
        }
    }

}



?>
