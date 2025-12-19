<?php
session_start();

require_once "init.php";


$id = $_POST['id'];


$sql = "DELETE FROM categories WHERE id = :id AND created_by = :username";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id' => $id,
    ':username' => $_SESSION['user_name']
]);

header("Location: dashboard.php?success=deleted#categories");
exit;
?>