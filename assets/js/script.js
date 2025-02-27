let questionCount = 1; 

// Function to add a new question input field
function addQuestion() {
    
    questionCount++;

    const questionsContainer = document.getElementById('questions-container');

    const newQuestionItem = document.createElement('div');
    newQuestionItem.classList.add('question-item');

    // Set the HTML content for the new question input fields
    newQuestionItem.innerHTML = `
        <label for="question${questionCount}">Question Text:</label>
        <input type="text" id="question${questionCount}" name="questions[${questionCount - 1}][question_text]" required><br>
        <label for="option1${questionCount}">Option 1:</label>
        <input type="text" id="option1${questionCount}" name="questions[${questionCount - 1}][options][0]" required><br>
        <label for="option2${questionCount}">Option 2:</label>
        <input type="text" id="option2${questionCount}" name="questions[${questionCount - 1}][options][1]" required><br>
        <label for="option3${questionCount}">Option 3:</label>
        <input type="text" id="option3${questionCount}" name="questions[${questionCount - 1}][options][2]" required><br>
        <label for="option4${questionCount}">Option 4:</label>
        <input type="text" id="option4${questionCount}" name="questions[${questionCount - 1}][options][3]" required><br>
        <label for="correct_option${questionCount}">Correct Option (1-4):</label>
        <input type="number" id="correct_option${questionCount}" name="questions[${questionCount - 1}][correct_option]" min="1" max="4" required><br>
    `;

    questionsContainer.appendChild(newQuestionItem);
}

