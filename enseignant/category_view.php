<?php
require_once "init.php";

$username =  $_SESSION['user_name']  ?? null;

$stmt = $pdo->prepare("
    SELECT c.id, c.nom, c.description, c.created_by, c.created_at,
           COUNT(DISTINCT q.id) as quiz_count
    FROM categories c
    LEFT JOIN quizzes q ON c.id = q.categorie_id
    WHERE c.created_by = :username
    GROUP BY c.id, c.nom, c.description, c.created_by, c.created_at
    ORDER BY c.created_at DESC
");
$stmt->execute([':username' => $username]);
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

$borderColors = ['blue-500', 'purple-500', 'green-500', 'red-500', 'yellow-500', 'pink-500'];
?>