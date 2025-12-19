<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom         = trim($_POST["nom"] ?? '');
    $description = trim($_POST["description"] ?? '');

    if ($nom === '') {
        header("Location: dashboard.php?error=nom_required#categories");
        exit;
    }

    if (!isset($_SESSION['user_name'])) {
        header("Location: ../auth/login.php?error=session_expired");
        exit;
    }

    $createdBy = $_SESSION['user_name'];

    
        $stmt = $pdo->prepare(
            "INSERT INTO categories (nom, description, created_by, created_at)
             VALUES (:nom, :description, :created_by, NOW())"
        );

        $stmt->execute([
            ':nom'        => $nom,
            ':description'=> $description,
            ':created_by' => $createdBy
        ]);

        header("Location: dashboard.php?success=category_added#categories");
        exit;


} else {
    header("Location: dashboard.php");
    exit;
}
