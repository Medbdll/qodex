<?php
require_once '../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
        die("Invalid request.");
    }

    unset($_SESSION['csrf_token']);

    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare("
            INSERT INTO quizzes (titre, description, categorie_id, enseignant_id, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $_POST['titre'],
            $_POST['description'],
            $_POST['categorie_id'],
            $_SESSION['user_id']
        ]);

        $quiz_id = $pdo->lastInsertId();

        foreach ($_POST['questions'] as $q) {
            $stmt2 = $pdo->prepare("
                INSERT INTO questions (quiz_id, question, option1, option2, option3, option4, correct_option)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt2->execute([
                $quiz_id,
                $q['question'],
                $q['option1'],
                $q['option2'],
                $q['option3'],
                $q['option4'],
                $q['correct']
            ]);
        }

        $pdo->commit();
        header("Location: dashboard.php#quiz?created=1");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
}
