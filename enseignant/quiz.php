<?php 
require_once '../config/database.php';

$stmt = $pdo->query("SELECT id, nom FROM categories");
$categories = $stmt->fetchAll();
?>
<div id="createQuizModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Créer un Quiz</h3>
                <button onclick="closeModal('createQuizModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form action="add_quiz.php" method="post">
                <input type="hidden" name="csrf_token" value="">

                <div class="grid md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Titre du quiz *
                        </label>
                        <input type="text" name="titre" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Ex: Les Bases de HTML5">
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Catégorie *
                        </label>
                        <select name="categorie_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nom']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                ()
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Description
                    </label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Décrivez votre quiz..."></textarea>
                </div>

                <hr class="my-6">

                <?php
                require_once 'question.php';
                ?>

                <div class="flex gap-3">
                    <button type="button" onclick="closeModal('createQuizModal')" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                        <i class="fas fa-check mr-2"></i>Créer le Quiz
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>