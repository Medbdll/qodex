    <?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$userName = $_SESSION['user_name'];
$userRole = $_SESSION['user_role'];
?>
    <nav class="bg-white shadow-lg fixed w-full z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-graduation-cap text-3xl text-indigo-600"></i>
                        <span class="ml-2 text-2xl font-bold text-gray-900">QuizMaster</span>
                        <span class="ml-3 px-3 py-1 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-full"><?php echo htmlspecialchars($userRole); ?></span>
                    </div>
                    <div class="hidden md:ml-10 md:flex md:space-x-8">
                        <a href="#dashboard" onclick="showSection('dashboard')" class="border-indigo-500 text-gray-900 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-home mr-2"></i>Tableau de bord
                        </a>
                        <a href="#categories" onclick="showSection('categories')" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-folder mr-2"></i>Catégories
                        </a>
                        <a href="#quiz" onclick="showSection('quiz')" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-clipboard-list mr-2"></i>Mes Quiz
                        </a>
                        <a href="#results" onclick="showSection('results')" class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                            <i class="fas fa-chart-bar mr-2"></i>Résultats
                        </a>
                    </div>
                </div>
                <div class="flex items-center">
                    <div class="hidden md:flex md:items-center md:space-x-4">
                        <button class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-bell text-xl"></i>
                        </button>
                        <div class="relative">
                            <button class="flex items-center space-x-3 focus:outline-none" onclick="toggleDropdown()">
                                <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center text-white font-semibold">
                                    AB
                                </div>
                                <div class="hidden md:block text-left">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($userName); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo htmlspecialchars($userRole); ?></div>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500"></i>
                            </button>
                            <div id="userDropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user mr-2"></i>Mon Profil
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog mr-2"></i>Paramètres
                                </a>
                                <hr class="my-1">
                                <a href="#student" onclick="switchToStudent()" class="block px-4 py-2 text-sm text-blue-600 hover:bg-gray-100">
                                    <i class="fas fa-exchange-alt mr-2"></i>Espace Étudiant
                                </a>
                                <a href="../auth/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>