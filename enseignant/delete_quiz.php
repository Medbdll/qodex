<?php
session_start();

require_once "init.php";


$id = $_POST['id'];


$sql = "DELETE FROM quizzes WHERE id = :id AND enseignant_id = :userid";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id' => $id,
    ':userid' => $_SESSION['user_id']
]);

header("Location: dashboard.php?success=deleted#categories");
exit;
?>