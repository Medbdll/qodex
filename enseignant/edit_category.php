<?php
session_start();

require_once "../config/database.php";

if (!isset($_SESSION['user_name'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    
    if (empty($nom)) {
        header("Location: dashboard.php?error=nom_required#categories");
        exit;
    }
    
    $sql = "UPDATE categories SET nom = :nom, description = :description WHERE id = :id AND created_by = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom' => $nom,
        ':description' => $description,
        ':id' => $id,
        ':username' => $_SESSION['user_name']
    ]);
    
    header("Location: dashboard.php?success=updated#categories");
    exit;
}

else {
    
    $id = $_GET['id'];
    
    $sql = "SELECT * FROM categories WHERE id = :id AND created_by = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $id,
        ':username' => $_SESSION['user_name']
    ]);
    $category = $stmt->fetch();
    
    if (!$category) {
        header("Location: dashboard.php?error=not_found#categories");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Catégorie</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">

    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-md w-full">
            
            <!-- Back Button -->
            <a href="dashboard.php#categories" class="inline-flex items-center text-indigo-600 hover:text-indigo-700 mb-6">
                <i class="fas fa-arrow-left mr-2"></i>Retour au tableau de bord
            </a>
            
            <!-- Edit Form -->
            <div class="bg-white rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">
                    <i class="fas fa-edit text-indigo-600 mr-2"></i>Modifier la Catégorie
                </h2>
                
                <form action="edit_category.php" method="POST">
                    
                    <!-- Hidden ID -->
                    <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                    
                    <!-- Name Field -->
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Nom de la catégorie *
                        </label>
                        <input 
                            type="text" 
                            name="nom" 
                            value="<?php echo htmlspecialchars($category['nom']); ?>"
                            required 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Ex: HTML/CSS">
                    </div>

                    <!-- Description Field -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2">
                            Description
                        </label>
                        <textarea 
                            name="description" 
                            rows="4" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                            placeholder="Décrivez cette catégorie..."><?php echo htmlspecialchars($category['description']); ?></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex gap-3">
                        <a href="dashboard.php#categories" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition text-center">
                            Annuler
                        </a>
                        <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                            <i class="fas fa-save mr-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Info Box -->
            <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Créée le:</strong> <?php echo date('d/m/Y', strtotime($category['created_at'])); ?>
                </p>
            </div>
        </div>
    </div>

</body>
</html>