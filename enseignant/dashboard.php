<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizMaster - Espace Enseignant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-50">


    <!-- ESPACE ENSEIGNANT -->
    <?php
    // session_start();
    require_once '../config/database.php';
    require_once '../includes/header.php'; 
    require_once 'categorie.php'; 
    require_once 'manage_quizzes.php'; 
    require_once 'view_results.php'; 
    ?>
    <div id="teacherSpace" class="pt-16">

        <!-- Dashboard Section -->
        <div id="dashboard" class="section-content">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    <h1 class="text-4xl font-bold mb-4">Tableau de bord Enseignant</h1>
                    <p class="text-xl text-indigo-100 mb-6">Gérez vos quiz et suivez les performances de vos étudiants</p>
                    <div class="flex gap-4">
                        <button onclick="showSection('categories'); openModal('createCategoryModal')" class="bg-white text-indigo-600 px-6 py-3 rounded-lg font-semibold hover:bg-indigo-50 transition">
                            <i class="fas fa-folder-plus mr-2"></i>Nouvelle Catégorie
                        </button>
                        <button onclick="showSection('quiz'); openModal('createQuizModal')" class="bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-800 transition">
                            <i class="fas fa-plus-circle mr-2"></i>Créer un Quiz
                        </button>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Total Quiz</p>
                                <p class="text-3xl font-bold text-gray-900">24</p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-lg">
                                <i class="fas fa-clipboard-list text-blue-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Catégories</p>
                                <p class="text-3xl font-bold text-gray-900">8</p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-lg">
                                <i class="fas fa-folder text-purple-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Étudiants Actifs</p>
                                <p class="text-3xl font-bold text-gray-900">156</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-lg">
                                <i class="fas fa-user-graduate text-green-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Taux Réussite</p>
                                <p class="text-3xl font-bold text-gray-900">87%</p>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-lg">
                                <i class="fas fa-chart-line text-yellow-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>

    <script>
        // ==================== VARIABLES GLOBALES ====================
        let questionCount = 1;
        let currentQuestionIndex = 0;
        let selectedAnswers = {};
        let timerInterval;
        let currentQuiz = null;
        let studentAnswers = [];
        let quizTimer = null;
        let timeLeft = 0;

        // ==================== DONNÉES DES QUIZ ====================
        const quizQuestions = {
            'Les Bases de HTML5': [{
                    question: 'Quelle balise HTML5 est utilisée pour définir une section de navigation ?',
                    options: ['<nav>', '<navigation>', '<menu>', '<navbar>'],
                    correct: 0
                },
                {
                    question: 'Quelle balise est utilisée pour créer un lien hypertexte ?',
                    options: ['<link>', '<a>', '<href>', '<url>'],
                    correct: 1
                },
                {
                    question: 'Quelle propriété CSS permet de changer la couleur du texte ?',
                    options: ['text-color', 'font-color', 'color', 'text-style'],
                    correct: 2
                }
            ],
            'CSS Avancé': [{
                question: 'Quelle propriété CSS permet de créer un design responsive avec Flexbox ?',
                options: ['display: flex;', 'display: grid;', 'display: block;', 'display: inline;'],
                correct: 0
            }],
            'JavaScript Fondamentaux': [{
                question: 'Comment déclarer une variable en JavaScript ES6 ?',
                options: ['var', 'let', 'const', 'toutes ces réponses'],
                correct: 3
            }]
        };

        // ==================== NAVIGATION ====================

        // Navigation pour l'espace enseignant
        function showSection(sectionId) {
            document.querySelectorAll('.section-content').forEach(section => {
                section.classList.add('hidden');
            });
            document.getElementById(sectionId).classList.remove('hidden');

            // Update active nav link
            document.querySelectorAll('nav a').forEach(link => {
                link.classList.remove('border-indigo-500', 'text-gray-900');
                link.classList.add('border-transparent', 'text-gray-500');
            });
            if (event && event.target) {
                event.target.classList.remove('border-transparent', 'text-gray-500');
                event.target.classList.add('border-indigo-500', 'text-gray-900');
            }
        }

        // Navigation pour l'espace étudiant
        function showStudentSection(sectionId, categoryName = '') {
            document.querySelectorAll('.student-section').forEach(section => {
                section.classList.add('hidden');
            });
            document.getElementById(sectionId).classList.remove('hidden');

            if (categoryName && sectionId === 'categoryQuizzes') {
                document.getElementById('categoryTitle').textContent = categoryName;
                loadQuizzesForCategory(categoryName);
            }

            // Charger les résultats si nécessaire
            if (sectionId === 'studentResults') {
                updateResultsTable();
            }
        }

        // ==================== DROPDOWN ====================

        // Toggle user dropdown
        function toggleDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const button = event.target.closest('button');

            if (!button || !button.onclick || button.onclick.toString().indexOf('toggleDropdown') === -1) {
                if (!dropdown.contains(event.target)) {
                    dropdown.classList.add('hidden');
                }
            }
        });

        // ==================== SWITCH SPACES ====================

        // Switch to Student Space
        function switchToStudent() {
            document.getElementById('teacherSpace').classList.add('hidden');
            document.getElementById('studentSpace').classList.remove('hidden');

            // Update navigation
            const nav = document.querySelector('nav');
            nav.innerHTML = `
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-graduation-cap text-3xl text-green-600"></i>
                        <span class="ml-2 text-2xl font-bold text-gray-900">QuizMaster</span>
                        <span class="ml-3 px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Étudiant</span>
                    </div>
                </div>
                <div class="flex items-center">
                    <button onclick="switchToTeacher()" class="mr-4 text-indigo-600 hover:text-indigo-700 font-semibold">
                        <i class="fas fa-exchange-alt mr-2"></i>Espace Enseignant
                    </button>
                    <div class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center text-white font-semibold">
                        AB
                    </div>
                </div>
            </div>
        </div>
    `;
        }

        // Switch to Teacher Space
        function switchToTeacher() {
            document.getElementById('studentSpace').classList.add('hidden');
            document.getElementById('teacherSpace').classList.remove('hidden');
            location.reload(); // Reload to restore teacher navigation
        }

        // ==================== MODALS ====================

        // Open modal
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        // Close modal
        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('bg-opacity-50')) {
                event.target.classList.add('hidden');
                event.target.classList.remove('flex');
            }
        }

        // ==================== QUIZ MANAGEMENT (TEACHER) ====================

        // Add Question to Quiz Creation Form
        function addQuestion() {
            questionCount++;
            const container = document.getElementById('questionsContainer');
            const questionHTML = `
        <div class="bg-gray-50 rounded-lg p-4 mb-4 question-block">
            <div class="flex justify-between items-center mb-4">
                <h5 class="font-bold text-gray-900">Question ${questionCount}</h5>
                <button type="button" onclick="removeQuestion(this)" class="text-red-600 hover:text-red-700">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Question *</label>
                <input type="text" name="questions[${questionCount-1}][question]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Posez votre question...">
            </div>

            <div class="grid md:grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-gray-700 text-sm mb-2">Option 1 *</label>
                    <input type="text" name="questions[${questionCount-1}][option1]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm mb-2">Option 2 *</label>
                    <input type="text" name="questions[${questionCount-1}][option2]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm mb-2">Option 3 *</label>
                    <input type="text" name="questions[${questionCount-1}][option3]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm mb-2">Option 4 *</label>
                    <input type="text" name="questions[${questionCount-1}][option4]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Réponse correcte *</label>
                <select name="questions[${questionCount-1}][correct]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">Sélectionner la bonne réponse</option>
                    <option value="1">Option 1</option>
                    <option value="2">Option 2</option>
                    <option value="3">Option 3</option>
                    <option value="4">Option 4</option>
                </select>
            </div>
        </div>
    `;
            container.insertAdjacentHTML('beforeend', questionHTML);
        }

        // Remove Question from Quiz Creation Form
        function removeQuestion(button) {
            const questionBlock = button.closest('.question-block');
            questionBlock.remove();

            // Renumber questions
            const questions = document.querySelectorAll('.question-block');
            questions.forEach((q, index) => {
                const title = q.querySelector('h5');
                title.textContent = `Question ${index + 1}`;
            });
            questionCount = questions.length;
        }

        // ==================== STUDENT QUIZ FUNCTIONS ====================

        // Load quizzes based on category
        function loadQuizzesForCategory(categoryName) {
            const quizContainer = document.getElementById('quizListContainer');

            // Quiz data by category
            const quizData = {
                'HTML/CSS': [{
                        title: 'Les Bases de HTML5',
                        description: 'Testez vos connaissances sur les éléments HTML5 et leur utilisation',
                        questions: quizQuestions['Les Bases de HTML5']?.length || 3,
                        duration: 30,
                        rating: 4.8,
                        badge: 'bg-blue-100 text-blue-700'
                    },
                    {
                        title: 'CSS Avancé',
                        description: 'Flexbox, Grid, animations et responsive design',
                        questions: quizQuestions['CSS Avancé']?.length || 1,
                        duration: 25,
                        rating: 4.6,
                        badge: 'bg-blue-100 text-blue-700'
                    }
                ],
                'JavaScript': [{
                    title: 'JavaScript Fondamentaux',
                    description: 'Variables, types de données, opérateurs et structures de contrôle',
                    questions: quizQuestions['JavaScript Fondamentaux']?.length || 1,
                    duration: 35,
                    rating: 4.7,
                    badge: 'bg-purple-100 text-purple-700'
                }],
                'PHP/MySQL': [{
                    title: 'PHP Basics',
                    description: 'Syntaxe de base, variables et opérations en PHP',
                    questions: 20,
                    duration: 30,
                    rating: 4.6,
                    badge: 'bg-green-100 text-green-700'
                }]
            };

            const quizzes = quizData[categoryName] || [];

            quizContainer.innerHTML = quizzes.map(quiz => `
        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="px-3 py-1 ${quiz.badge} text-xs font-semibold rounded-full">${categoryName}</span>
                    <span class="text-yellow-500"><i class="fas fa-star"></i> ${quiz.rating}</span>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">${quiz.title}</h3>
                <p class="text-gray-600 mb-4 text-sm">${quiz.description}</p>
                <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                    <span><i class="fas fa-question-circle mr-1"></i>${quiz.questions} questions</span>
                    <span><i class="fas fa-clock mr-1"></i>${quiz.duration} min</span>
                </div>
                <button onclick="startQuiz('${quiz.title}')" class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                    <i class="fas fa-play mr-2"></i>Commencer le Quiz
                </button>
            </div>
        </div>
    `).join('');
        }

        // Start Quiz
        function startQuiz(quizTitle) {
            currentQuiz = quizTitle;
            currentQuestionIndex = 0;
            studentAnswers = [];
            timeLeft = 30 * 60; // 30 minutes in seconds

            document.getElementById('quizTitle').textContent = quizTitle;
            document.getElementById('currentQuestion').textContent = currentQuestionIndex + 1;

            const totalQuestions = quizQuestions[quizTitle]?.length || 0;
            document.getElementById('totalQuestions').textContent = totalQuestions;

            showStudentSection('takeQuiz');
            loadQuestion();
            startTimer();
        }

        // ==================== LOAD QUESTION ====================
        function loadQuestion() {
            if (!currentQuiz || !quizQuestions[currentQuiz]) return;

            const questions = quizQuestions[currentQuiz];
            if (currentQuestionIndex >= questions.length) return;

            const question = questions[currentQuestionIndex];

            document.getElementById('questionText').textContent = question.question;
            document.getElementById('currentQuestion').textContent = currentQuestionIndex + 1;

            const optionsContainer = document.querySelector('#takeQuiz .space-y-4');
            optionsContainer.innerHTML = '';

            question.options.forEach((option, index) => {
                const isSelected = studentAnswers[currentQuestionIndex] === index;
                const optionHTML = `
            <div onclick="selectAnswer(${index})" class="answer-option p-4 border-2 ${isSelected ? 'border-green-500 bg-green-50' : 'border-gray-200'} rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4">
                        <div class="w-4 h-4 rounded-full bg-green-600 ${isSelected ? '' : 'hidden'}"></div>
                    </div>
                    <span class="text-lg">${option}</span>
                </div>
            </div>
        `;
                optionsContainer.innerHTML += optionHTML;
            });

            // Update navigation buttons
            const prevBtn = document.querySelector('#takeQuiz button:first-child');
            const nextBtn = document.querySelector('#takeQuiz button:last-child');

            prevBtn.disabled = currentQuestionIndex === 0;
            nextBtn.innerHTML = currentQuestionIndex === questions.length - 1 ?
                '<i class="fas fa-paper-plane mr-2"></i>Soumettre' :
                'Suivant<i class="fas fa-arrow-right ml-2"></i>';
        }

        // ==================== SELECT ANSWER ====================
        function selectAnswer(answerIndex) {
            studentAnswers[currentQuestionIndex] = answerIndex;
            loadQuestion(); // Reload to show selection
        }

        // ==================== NAVIGATE QUESTIONS ====================
        function nextQuestion() {
            const questions = quizQuestions[currentQuiz];
            if (currentQuestionIndex < questions.length - 1) {
                currentQuestionIndex++;
                loadQuestion();
            } else {
                submitQuiz();
            }
        }

        function previousQuestion() {
            if (currentQuestionIndex > 0) {
                currentQuestionIndex--;
                loadQuestion();
            }
        }

        // ==================== TIMER ====================
        function startTimer() {
            clearInterval(timerInterval);

            timerInterval = setInterval(() => {
                timeLeft--;

                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                document.getElementById('timer').textContent =
                    `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;

                // Color coding for timer
                const timerElement = document.getElementById('timer');
                timerElement.classList.remove('text-red-500', 'text-yellow-500');
                if (timeLeft < 300) { // Less than 5 minutes
                    timerElement.classList.add('text-red-500');
                } else if (timeLeft < 600) { // Less than 10 minutes
                    timerElement.classList.add('text-yellow-500');
                }

                if (timeLeft <= 0) {
                    clearInterval(timerInterval);
                    submitQuiz();
                    alert('Temps écoulé! Votre quiz a été soumis automatiquement.');
                }
            }, 1000);
        }

        // ==================== SUBMIT QUIZ ====================
        function submitQuiz() {
            clearInterval(timerInterval);

            if (!currentQuiz) return;

            // Calculate score
            const questions = quizQuestions[currentQuiz];
            let score = 0;
            let totalQuestions = questions.length;

            studentAnswers.forEach((answer, index) => {
                if (answer === questions[index].correct) {
                    score++;
                }
            });

            const percentage = Math.round((score / totalQuestions) * 100);

            // Show results
            const resultHTML = `
        <div class="bg-white rounded-xl shadow-lg p-8 text-center">
            <div class="w-24 h-24 mx-auto rounded-full bg-green-100 flex items-center justify-center mb-6">
                <i class="fas fa-trophy text-4xl text-green-600"></i>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Quiz Terminé!</h2>
            <p class="text-gray-600 mb-8">Vous avez complété <span class="font-bold">${currentQuiz}</span></p>
            
            <div class="bg-gray-50 rounded-lg p-6 mb-8">
                <div class="flex justify-between items-center mb-4">
                    <div class="text-center">
                        <p class="text-gray-500 text-sm">Score</p>
                        <p class="text-4xl font-bold text-gray-900">${score}/${totalQuestions}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-500 text-sm">Pourcentage</p>
                        <p class="text-4xl font-bold ${percentage >= 70 ? 'text-green-600' : 'text-red-600'}">
                            ${percentage}%
                        </p>
                    </div>
                    <div class="text-center">
                        <p class="text-gray-500 text-sm">Statut</p>
                        <p class="text-xl font-bold ${percentage >= 70 ? 'text-green-600' : 'text-red-600'}">
                            ${percentage >= 70 ? 'Réussi' : 'Échoué'}
                        </p>
                    </div>
                </div>
                
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-green-600 h-4 rounded-full" style="width: ${percentage}%"></div>
                </div>
            </div>
            
            <div class="mb-8 text-left">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Détail des réponses:</h3>
                ${questions.map((q, index) => {
                    const userAnswer = studentAnswers[index];
                    const isCorrect = userAnswer === q.correct;
                    const hasAnswered = userAnswer !== undefined;
                    
                    return `
                        <div class="mb-4 p-4 ${isCorrect ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'} border rounded-lg">
                            <p class="font-bold mb-2">${index + 1}. ${q.question}</p>
                            <p class="mb-1">Votre réponse: <span class="${isCorrect ? 'text-green-600' : 'text-red-600'} font-bold">
                                ${hasAnswered ? q.options[userAnswer] : 'Non répondue'}
                            </span></p>
                            ${!isCorrect ? `<p class="text-green-600 font-bold">Bonne réponse: ${q.options[q.correct]}</p>` : ''}
                        </div>
                    `;
                }).join('')}
            </div>
            
            <div class="flex gap-3 justify-center">
                <button onclick="showStudentSection('studentDashboard')" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                    <i class="fas fa-home mr-2"></i>Tableau de bord
                </button>
                <button onclick="showStudentSection('studentResults')" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <i class="fas fa-chart-bar mr-2"></i>Voir tous mes résultats
                </button>
            </div>
        </div>
    `;

            // Replace quiz interface with results
            const quizContainer = document.querySelector('#takeQuiz .max-w-4xl');
            quizContainer.innerHTML = resultHTML;

            // Store result in localStorage (simulating database)
            saveQuizResult(currentQuiz, score, totalQuestions, percentage);
        }

        // ==================== SAVE RESULT ====================
        function saveQuizResult(quizName, score, total, percentage) {
            let results = JSON.parse(localStorage.getItem('studentQuizResults') || '[]');

            const result = {
                quiz: quizName,
                score: score,
                total: total,
                percentage: percentage,
                date: new Date().toLocaleDateString('fr-FR'),
                time: new Date().toLocaleTimeString('fr-FR', {
                    hour: '2-digit',
                    minute: '2-digit'
                }),
                status: percentage >= 70 ? 'Réussi' : 'Échoué'
            };

            results.push(result);
            localStorage.setItem('studentQuizResults', JSON.stringify(results));

            // Update results table
            updateResultsTable();
        }

        // ==================== UPDATE RESULTS TABLE ====================
        function updateResultsTable() {
            const results = JSON.parse(localStorage.getItem('studentQuizResults') || '[]');
            const resultsTable = document.querySelector('#studentResults tbody');

            if (!resultsTable) return;

            resultsTable.innerHTML = results.map(result => `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4 text-sm font-medium text-gray-900">${result.quiz}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full ${getCategoryBadge(result.quiz)}">
                    ${getCategoryForQuiz(result.quiz)}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-lg font-bold ${result.percentage >= 70 ? 'text-green-600' : 'text-red-600'}">
                    ${result.score}/${result.total}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${result.date}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${result.time}</td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${result.status === 'Réussi' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    <i class="fas fa-${result.status === 'Réussi' ? 'check' : 'times'} mr-1"></i>${result.status}
                </span>
            </td>
        </tr>
    `).join('');

            // Update stats
            updateStudentStats();
        }

        // ==================== HELPER FUNCTIONS ====================
        function getCategoryForQuiz(quizName) {
            const quizCategories = {
                'Les Bases de HTML5': 'HTML/CSS',
                'CSS Avancé': 'HTML/CSS',
                'JavaScript Fondamentaux': 'JavaScript',
                'PHP Basics': 'PHP/MySQL'
            };
            return quizCategories[quizName] || 'Général';
        }

        function getCategoryBadge(quizName) {
            const category = getCategoryForQuiz(quizName);
            switch (category) {
                case 'HTML/CSS':
                    return 'bg-blue-100 text-blue-800';
                case 'JavaScript':
                    return 'bg-purple-100 text-purple-800';
                case 'PHP/MySQL':
                    return 'bg-green-100 text-green-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        }

        function updateStudentStats() {
            const results = JSON.parse(localStorage.getItem('studentQuizResults') || '[]');

            if (results.length === 0) return;

            // Calculate stats
            const totalQuizzes = results.length;
            const passedQuizzes = results.filter(r => r.status === 'Réussi').length;
            const averageScore = results.reduce((sum, r) => sum + r.percentage, 0) / totalQuizzes;
            const successRate = Math.round((passedQuizzes / totalQuizzes) * 100);

            // Update stats cards if they exist
            const statsCards = document.querySelectorAll('#studentResults .bg-white.rounded-xl.shadow-md');
            if (statsCards.length >= 4) {
                statsCards[0].querySelector('p.text-3xl').textContent = totalQuizzes;
                statsCards[1].querySelector('p.text-3xl').textContent = `${(averageScore / 100 * 20).toFixed(1)}/20`;
                statsCards[2].querySelector('p.text-3xl').textContent = `${successRate}%`;
                statsCards[3].querySelector('p.text-3xl').textContent = `#${Math.floor(Math.random() * 50) + 1}`;
            }
        }

        // ==================== INITIALIZATION ====================

        // Initialize the application when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('QuizMaster initialized successfully!');

            // Load any existing results if in student space
            if (document.getElementById('studentSpace') &&
                !document.getElementById('studentSpace').classList.contains('hidden')) {
                updateResultsTable();
            }
        });
    </script>
</body>

</html>