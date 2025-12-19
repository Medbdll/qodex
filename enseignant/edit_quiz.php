<?php
session_start();
require_once '../config/database.php';

// Security check
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ensure quiz_id exists
if (!isset($_GET['id'])) {
    die("Quiz ID is missing.");
}

$quiz_id = (int) $_GET['id'];

// Fetch quiz
$stmt = $pdo->prepare("
    SELECT id, titre, description, categorie_id
    FROM quizzes
    WHERE id = ? AND enseignant_id = ?
");
$stmt->execute([$quiz_id, $_SESSION['user_id']]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {
    die("Quiz not found or unauthorized.");
}

// Fetch categories
$catStmt = $pdo->query("SELECT id, nom FROM categories ORDER BY nom ASC");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch questions
$qStmt = $pdo->prepare("
    SELECT id, question, correct_option
    FROM questions
    WHERE quiz_id = ?
");
$qStmt->execute([$quiz_id]);
$questions = $qStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle update form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmtUpdate = $pdo->prepare("
        UPDATE quizzes
        SET titre = ?, description = ?, categorie_id = ?, updated_at = NOW()
        WHERE id = ? AND enseignant_id = ?
    ");
    $stmtUpdate->execute([
        $_POST['titre'],
        $_POST['description'],
        $_POST['categorie_id'],
        $quiz_id,
        $_SESSION['user_id']
    ]);

    header("Location: dashboard.php#quiz?updated=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 py-10">

<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-xl p-8">

    <h2 class="text-3xl font-bold text-gray-800 mb-6">Modifier Quiz</h2>

    <form action="" method="post" class="space-y-6">

        <div>
            <label class="block font-semibold mb-1">Titre *</label>
            <input type="text"
                   name="titre"
                   required
                   value="<?= htmlspecialchars($quiz['titre']) ?>"
                   class="w-full border rounded-lg px-4 py-2">
        </div>

        <div>
            <label class="block font-semibold mb-1">Description</label>
            <textarea name="description" rows="3"
                      class="w-full border rounded-lg px-4 py-2"><?= htmlspecialchars($quiz['description']) ?></textarea>
        </div>

        <div>
            <label class="block font-semibold mb-1">Catégorie *</label>
            <select name="categorie_id" required class="w-full border rounded-lg px-4 py-2">
                <option value="">-- Sélectionnez une catégorie --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"
                        <?= ($cat['id'] == $quiz['categorie_id'] ? 'selected' : '') ?>>
                        <?= htmlspecialchars($cat['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg">
            Enregistrer les modifications
        </button>

        <a href="dashboard.php#quiz"
           class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-lg ml-2">
            Annuler
        </a>
    </form>

    <hr class="my-8">

    <h3 class="text-2xl font-semibold text-gray-700 mb-4">Questions du quiz</h3>

    <?php if (empty($questions)): ?>
        <p class="text-gray-500">Aucune question pour le moment.</p>
        <a href="add_question.php?quiz_id=<?= $quiz_id ?>"
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-block mt-3">
            Ajouter des questions
        </a>
    <?php else: ?>
        <table class="min-w-full divide-y divide-gray-200 bg-white shadow rounded-lg">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Question</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bonne Réponse</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php foreach ($questions as $q): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-900"><?= htmlspecialchars($q['question']) ?></td>
                    <td class="px-6 py-4 text-sm">
                        Option <?= htmlspecialchars($q['correct_option']) ?>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <a href="edit_question.php?id=<?= $q['id'] ?>" class="text-indigo-600 hover:text-indigo-700">
                            Modifier
                        </a>
                        <a href="delete_question.php?id=<?= $q['id'] ?>&quiz_id=<?= $quiz_id ?>"
                           onclick="return confirm('Supprimer cette question ?');"
                           class="text-red-600 hover:text-red-700">
                            Supprimer
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="add_question.php?quiz_id=<?= $quiz_id ?>"
           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg inline-block mt-4">
            Ajouter une nouvelle question
        </a>
    <?php endif; ?>

</div>

</body>
</html>
