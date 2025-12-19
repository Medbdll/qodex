<?php 
require_once "../enseignant/category_view.php";
?>
<div id="categories" class="section-content hidden ">
    <div class=" mx-auto px-4 sm:px-6 lg:px-8 py-8 ">

        <!-- Success Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="mt-[100px] mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>
                <?php 
                if ($_GET['success'] == 'category_added') {
                    echo 'Catégorie créée avec succès!';
                } else if ($_GET['success'] == 'deleted') {
                    echo 'Catégorie supprimée avec succès!';
                }
                ?>
            </div>
        <?php endif; ?>

        <!-- Error Messages -->
        <?php if (isset($_GET['error'])): ?>
            <div class="mt-[100px] mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?php
                if ($_GET['error'] == 'nom_required') {
                    echo 'Le nom de la catégorie est requis.';
                } else {
                    echo 'Une erreur est survenue.';
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="flex justify-between items-center mb-8 mt-10">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Gestion des Catégories</h2>
                <p class="text-gray-600 mt-2">Organisez vos quiz par catégories</p>
            </div>
            <button onclick="openModal('createCategoryModal')" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition">
                <i class="fas fa-plus mr-2"></i>Nouvelle Catégorie
            </button>
        </div>

        <!-- Categories List -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ($categories as $index => $category): ?>
                <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-<?php echo $borderColors[$index % count($borderColors)]; ?>">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900"><?php echo htmlspecialchars($category['nom']); ?></h3>
                            <p class="text-gray-600 text-sm mt-1"><?php echo htmlspecialchars($category['description'] ?? ''); ?></p>
                        </div>
                        <div class="flex gap-2">
                           <a href="edit_category.php?id=<?php echo $category['id']; ?>" class="text-blue-600 hover:text-blue-700" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <!-- Delete Form -->
                            <form action="delete_category.php" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer cette catégorie ?');">
                                <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                <button type="submit" class="text-red-600 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">
                            <i class="fas fa-clipboard-list mr-2"></i>
                            <?php echo $category['quiz_count'] ?? 0; ?> quiz
                        </span>
                        <span class="text-gray-500">
                            <i class="fas fa-calendar mr-2"></i>
                            <?php echo date('d/m/Y', strtotime($category['created_at'])); ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Modal: Créer Catégorie -->
    <div id="createCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Nouvelle Catégorie</h3>
                    <button onclick="closeModal('createCategoryModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form action="add_categorie.php" method="post">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Nom de la catégorie *
                        </label>
                        <input type="text" name="nom" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Ex: HTML/CSS">
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Description
                        </label>
                        <textarea name="description" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Décrivez cette catégorie..."></textarea>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" onclick="closeModal('createCategoryModal')" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                            Annuler
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            <i class="fas fa-check mr-2"></i>Créer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>