<?php
session_start();
require_once '../config/database.php';

// Must be authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Required GET parameters
if (!isset($_GET['id']) || !isset($_GET['quiz_id'])) {
    die("Missing parameters.");
}

$question_id = (int) $_GET['id'];
$quiz_id     = (int) $_GET['quiz_id'];

// Fetch question
$stmt = $pdo->prepare("
    SELECT id, quiz_id, question, option1, option2, option3, option4, correct_option
    FROM questions
    WHERE id = ?
");
$stmt->execute([$question_id]);
$question = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$question) {
    die("Question not found.");
}

// Handle POST update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $stmtUpdate = $pdo->prepare("
        UPDATE questions
        SET question = ?, option1 = ?, option2 = ?, option3 = ?, option4 = ?, correct_option = ?, updated_at = NOW()
        WHERE id = ?
    ");

    $stmtUpdate->execute([
        $_POST['question'],
        $_POST['option1'],
        $_POST['option2'],
        $_POST['option3'],
        $_POST['option4'],
        $_POST['correct'],
        $question_id
    ]);

    header("Location: edit_quiz.php?id=" . $quiz_id . "&updated_question=1");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Question</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 py-10">

<div class="max-w-3xl mx-auto bg-white shadow-lg rounded-xl p-8">

    <h2 class="text-3xl font-bold text-gray-800 mb-6">Modifier la Question</h2>

    <form action="" method="post" class="space-y-6">

        <div>
            <label class="block font-semibold mb-1">Question *</label>
            <input type="text"
                   name="question"
                   required
                   value="<?= htmlspecialchars($question['question']) ?>"
                   class="w-full border rounded-lg px-4 py-2">
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Option 1 *</label>
                <input type="text"
                       name="option1"
                       required
                       value="<?= htmlspecialchars($question['option1']) ?>"
                       class="w-full border rounded-lg px-4 py-2">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Option 2 *</label>
                <input type="text"
                       name="option2"
                       required
                       value="<?= htmlspecialchars($question['option2']) ?>"
                       class="w-full border rounded-lg px-4 py-2">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Option 3 *</label>
                <input type="text"
                       name="option3"
                       required
                       value="<?= htmlspecialchars($question['option3']) ?>"
                       class="w-full border rounded-lg px-4 py-2">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Option 4 *</label>
                <input type="text"
                       name="option4"
                       required
                       value="<?= htmlspecialchars($question['option4']) ?>"
                       class="w-full border rounded-lg px-4 py-2">
            </div>
        </div>

        <div>
            <label class="block font-semibold mb-1">Bonne réponse *</label>
            <select name="correct" required class="w-full border rounded-lg px-4 py-2">
                <option value="">Sélectionner</option>
                <option value="1" <?= $question['correct_option'] == 1 ? 'selected' : '' ?>>Option 1</option>
                <option value="2" <?= $question['correct_option'] == 2 ? 'selected' : '' ?>>Option 2</option>
                <option value="3" <?= $question['correct_option'] == 3 ? 'selected' : '' ?>>Option 3</option>
                <option value="4" <?= $question['correct_option'] == 4 ? 'selected' : '' ?>>Option 4</option>
            </select>
        </div>

        <div class="flex items-center gap-3">
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2 rounded-lg">
                Enregistrer
            </button>

            <a href="edit_quiz.php?id=<?= $quiz_id ?>"
               class="bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-lg">
                Annuler
            </a>
        </div>
    </form>

</div>

</body>
</html>
