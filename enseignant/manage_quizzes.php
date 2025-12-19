<?php
require_once 'quiz.php';
$q = $pdo->query("
    SELECT q.id, q.titre, q.created_at, c.nom AS categorie,
      (SELECT COUNT(*) FROM questions WHERE quiz_id = q.id) AS total_questions
    FROM quizzes q
    LEFT JOIN categories c ON q.categorie_id = c.id
    ORDER BY q.created_at DESC
");
$data = $q->fetchAll();
?>
<div id="quiz" class="section-content hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex justify-between items-center mb-8 mt-20">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Mes Quiz</h2>
                <p class="text-gray-600 mt-2">Créez et gérez vos quiz</p>
            </div>
            <button onclick="openModal('createQuizModal')" 
                class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                <i class="fas fa-plus mr-2"></i>Créer un Quiz
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($data as $d): ?>
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">
                                <?= htmlspecialchars($d['categorie']) ?>
                            </span>
                            <div class="flex gap-2">
                                <a href="edit_quiz.php?id=<?= $d['id'] ?>" 
                                   class="text-blue-600 hover:text-blue-700">
                                   <i class="fas fa-edit"></i>
                                </a>
                                <form action="delete_quiz.php" method="POST" 
                                      onsubmit="return confirm('Voulez-vous vraiment supprimer ce quiz ?');">
                                    <input type="hidden" name="id" value="<?= $d['id'] ?>">
                                    <button class="text-red-600 hover:text-red-700" type="submit">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        <h3 class="text-xl font-bold text-gray-900 mb-2">
                            <?= htmlspecialchars($d['titre']) ?>
                        </h3>

                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <span>
                                <i class="fas fa-question-circle mr-1"></i><?= $d['total_questions'] ?>
                            </span>
                            <span>
                                <i class="fas fa-calendar mr-2"></i><?= $d['created_at'] ?>
                            </span>
                        </div>

                        <button class="w-full bg-indigo-600 text-white py-2 rounded-lg font-semibold hover:bg-indigo-700 transition">
                            <i class="fas fa-eye mr-2"></i>Voir les résultats
                        </button>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</div>
