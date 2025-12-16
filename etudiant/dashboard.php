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
    ?>
  <!-- ESPACE ÉTUDIANT -->
 <div id="studentSpace" class="hidden pt-16">
        
        <!-- Student Dashboard -->
        <div id="studentDashboard" class="student-section">
            <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                    <h1 class="text-4xl font-bold mb-4">Espace Étudiant</h1>
                    <p class="text-xl text-green-100 mb-6">Passez des quiz et suivez votre progression</p>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-8">Catégories Disponibles</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div onclick="showStudentSection('categoryQuizzes', 'HTML/CSS')" class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden group cursor-pointer">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 text-white">
                            <i class="fas fa-code text-4xl mb-3"></i>
                            <h3 class="text-xl font-bold">HTML/CSS</h3>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 mb-4">Maîtrisez les bases du web</p>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500"><i class="fas fa-clipboard-list mr-2"></i>12 quiz</span>
                                <span class="text-green-600 font-semibold group-hover:translate-x-2 transition-transform">Explorer →</span>
                            </div>
                        </div>
                    </div>

                    <div onclick="showStudentSection('categoryQuizzes', 'JavaScript')" class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden group cursor-pointer">
                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 p-6 text-white">
                            <i class="fas fa-laptop-code text-4xl mb-3"></i>
                            <h3 class="text-xl font-bold">JavaScript</h3>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 mb-4">Programmation interactive</p>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500"><i class="fas fa-clipboard-list mr-2"></i>8 quiz</span>
                                <span class="text-purple-600 font-semibold group-hover:translate-x-2 transition-transform">Explorer →</span>
                            </div>
                        </div>
                    </div>

                    <div onclick="showStudentSection('categoryQuizzes', 'PHP/MySQL')" class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden group cursor-pointer">
                        <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 text-white">
                            <i class="fas fa-database text-4xl mb-3"></i>
                            <h3 class="text-xl font-bold">PHP/MySQL</h3>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 mb-4">Backend et bases de données</p>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500"><i class="fas fa-clipboard-list mr-2"></i>10 quiz</span>
                                <span class="text-green-600 font-semibold group-hover:translate-x-2 transition-transform">Explorer →</span>
                            </div>
                        </div>
                    </div>

                    <div onclick="showStudentSection('studentResults')" class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 overflow-hidden group cursor-pointer">
                        <div class="bg-gradient-to-br from-orange-500 to-orange-600 p-6 text-white">
                            <i class="fas fa-chart-line text-4xl mb-3"></i>
                            <h3 class="text-xl font-bold">Mes Résultats</h3>
                        </div>
                        <div class="p-6">
                            <p class="text-gray-600 mb-4">Consultez vos performances</p>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500"><i class="fas fa-trophy mr-2"></i>24 tentatives</span>
                                <span class="text-orange-600 font-semibold group-hover:translate-x-2 transition-transform">Voir →</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Quizzes List -->
        <div id="categoryQuizzes" class="student-section hidden">
            <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <button onclick="showStudentSection('studentDashboard')" class="text-white hover:text-green-100 mb-4">
                        <i class="fas fa-arrow-left mr-2"></i>Retour aux catégories
                    </button>
                    <h1 class="text-4xl font-bold mb-2" id="categoryTitle">HTML/CSS</h1>
                    <p class="text-xl text-green-100">Sélectionnez un quiz pour commencer</p>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div id="quizListContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Quiz cards will be loaded dynamically -->
                </div>
            </div>
        </div>

        <!-- Quiz Taking Interface -->
        <div id="takeQuiz" class="student-section hidden">
            <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-3xl font-bold mb-2" id="quizTitle">Les Bases de HTML5</h1>
                            <p class="text-green-100">Question <span id="currentQuestion">1</span> sur <span id="totalQuestions">20</span></p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-green-100 mb-1">Temps restant</div>
                            <div class="text-3xl font-bold" id="timer">30:00</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="bg-white rounded-xl shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6" id="questionText">
                        Quelle balise HTML5 est utilisée pour définir une section de navigation ?
                    </h3>

                    <div class="space-y-4">
                        <div onclick="selectAnswer(this)" class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4 option-radio">
                                    <div class="w-4 h-4 rounded-full bg-green-600 hidden option-selected"></div>
                                </div>
                                <span class="text-lg">&lt;nav&gt;</span>
                            </div>
                        </div>

                        <div onclick="selectAnswer(this)" class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4 option-radio">
                                    <div class="w-4 h-4 rounded-full bg-green-600 hidden option-selected"></div>
                                </div>
                                <span class="text-lg">&lt;navigation&gt;</span>
                            </div>
                        </div>

                        <div onclick="selectAnswer(this)" class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4 option-radio">
                                    <div class="w-4 h-4 rounded-full bg-green-600 hidden option-selected"></div>
                                </div>
                                <span class="text-lg">&lt;menu&gt;</span>
                            </div>
                        </div>

                        <div onclick="selectAnswer(this)" class="answer-option p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 hover:bg-green-50 transition">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full border-2 border-gray-300 flex items-center justify-center mr-4 option-radio">
                                    <div class="w-4 h-4 rounded-full bg-green-600 hidden option-selected"></div>
                                </div>
                                <span class="text-lg">&lt;navbar&gt;</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between mt-8">
                        <button onclick="previousQuestion()" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            <i class="fas fa-arrow-left mr-2"></i>Précédent
                        </button>
                        <button onclick="nextQuestion()" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            Suivant<i class="fas fa-arrow-right ml-2"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Student Results -->
        <div id="studentResults" class="student-section hidden">
            <div class="bg-gradient-to-r from-green-600 to-teal-600 text-white">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <button onclick="showStudentSection('studentDashboard')" class="text-white hover:text-green-100 mb-4">
                        <i class="fas fa-arrow-left mr-2"></i>Retour au tableau de bord
                    </button>
                    <h1 class="text-4xl font-bold mb-2">Mes Résultats</h1>
                    <p class="text-xl text-green-100">Suivez votre progression et vos performances</p>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Quiz Complétés</p>
                                <p class="text-3xl font-bold text-gray-900">24</p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-lg">
                                <i class="fas fa-check-circle text-blue-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Moyenne</p>
                                <p class="text-3xl font-bold text-gray-900">16.5/20</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-lg">
                                <i class="fas fa-star text-green-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Taux Réussite</p>
                                <p class="text-3xl font-bold text-gray-900">85%</p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-lg">
                                <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-md p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 text-sm">Classement</p>
                                <p class="text-3xl font-bold text-gray-900">#12</p>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-lg">
                                <i class="fas fa-trophy text-yellow-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

              
            </div>
        </div>
    </div>
    </body>
    </html>