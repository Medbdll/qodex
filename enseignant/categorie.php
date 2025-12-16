
 <!-- Categories Section -->
 <div id="categories" class="section-content hidden ">
     <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
         <div class="flex justify-between items-center mb-8 mt-20">
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
             <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                 <div class="flex justify-between items-start mb-4">
                     <div>
                         <h3 class="text-xl font-bold text-gray-900">HTML/CSS</h3>
                         <p class="text-gray-600 text-sm mt-1">Bases du développement web</p>
                     </div>
                     <div class="flex gap-2">
                         <button class="text-blue-600 hover:text-blue-700">
                             <i class="fas fa-edit"></i>
                         </button>
                         <button class="text-red-600 hover:text-red-700">
                             <i class="fas fa-trash"></i>
                         </button>
                     </div>
                 </div>
                 <div class="flex items-center justify-between text-sm">
                     <span class="text-gray-500"><i class="fas fa-clipboard-list mr-2"></i>12 quiz</span>
                     <span class="text-gray-500"><i class="fas fa-user-friends mr-2"></i>45 étudiants</span>
                 </div>
             </div>

             <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                 <div class="flex justify-between items-start mb-4">
                     <div>
                         <h3 class="text-xl font-bold text-gray-900">JavaScript</h3>
                         <p class="text-gray-600 text-sm mt-1">Programmation côté client</p>
                     </div>
                     <div class="flex gap-2">
                         <button class="text-blue-600 hover:text-blue-700">
                             <i class="fas fa-edit"></i>
                         </button>
                         <button class="text-red-600 hover:text-red-700">
                             <i class="fas fa-trash"></i>
                         </button>
                     </div>
                 </div>
                 <div class="flex items-center justify-between text-sm">
                     <span class="text-gray-500"><i class="fas fa-clipboard-list mr-2"></i>8 quiz</span>
                     <span class="text-gray-500"><i class="fas fa-user-friends mr-2"></i>38 étudiants</span>
                 </div>
             </div>

             <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                 <div class="flex justify-between items-start mb-4">
                     <div>
                         <h3 class="text-xl font-bold text-gray-900">PHP/MySQL</h3>
                         <p class="text-gray-600 text-sm mt-1">Backend et bases de données</p>
                     </div>
                     <div class="flex gap-2">
                         <button class="text-blue-600 hover:text-blue-700">
                             <i class="fas fa-edit"></i>
                         </button>
                         <button class="text-red-600 hover:text-red-700">
                             <i class="fas fa-trash"></i>
                         </button>
                     </div>
                 </div>
                 <div class="flex items-center justify-between text-sm">
                     <span class="text-gray-500"><i class="fas fa-clipboard-list mr-2"></i>10 quiz</span>
                     <span class="text-gray-500"><i class="fas fa-user-friends mr-2"></i>42 étudiants</span>
                 </div>
             </div>
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
             <form>
                 <input type="hidden" name="csrf_token" value="">
<!-- <php echo generate_csrf_token(); ?> -->
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

 