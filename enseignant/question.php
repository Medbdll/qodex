
    <?php if(isset($quiz_id)): ?>
        <input type="hidden" name="quiz_id" value="<?= htmlspecialchars($quiz_id) ?>">
    <?php endif; ?>

    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h4 class="text-xl font-bold text-gray-900">Questions</h4>
            <button type="button" onclick="addQuestion()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                Ajouter une question
            </button>
        </div>

        <div id="questionsContainer">
            <div class="bg-gray-50 rounded-lg p-4 mb-4 question-block">
                <div class="flex justify-between items-center mb-4">
                    <h5 class="font-bold text-gray-900">Question 1</h5>
                    <button type="button" onclick="removeQuestion(this)" class="text-red-600 hover:text-red-700">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Question *</label>
                    <input type="text" name="questions[0][question]" required class="w-full px-4 py-2 border rounded-lg">
                </div>

                <div class="grid md:grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="block text-gray-700 text-sm mb-2">Option 1 *</label>
                        <input type="text" name="questions[0][option1]" required class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm mb-2">Option 2 *</label>
                        <input type="text" name="questions[0][option2]" required class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm mb-2">Option 3 *</label>
                        <input type="text" name="questions[0][option3]" required class="w-full px-4 py-2 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm mb-2">Option 4 *</label>
                        <input type="text" name="questions[0][option4]" required class="w-full px-4 py-2 border rounded-lg">
                    </div>
                </div>

                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Réponse correcte *</label>
                    <select name="questions[0][correct]" required class="w-full px-4 py-2 border rounded-lg">
                        <option value="">Sélectionner</option>
                        <option value="1">Option 1</option>
                        <option value="2">Option 2</option>
                        <option value="3">Option 3</option>
                        <option value="4">Option 4</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
