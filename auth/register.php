<?php
session_start();

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once '../config/database.php';

$firstNameErr = $lastNameErr = $emailErr = $passwordErr = $confirmPasswordErr = $roleErr = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // CSRF Token Validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Erreur CSRF: Requête invalide");
    }

    $firstName = trim($_POST["firstName"]);
    $lastName  = trim($_POST["lastName"]);
    $email     = trim($_POST["email"]);
    $password  = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];
    $role      = $_POST["role"];

    // Validation des champs
    if ($firstName === '') {
        $firstNameErr = "Prénom est requis";
    } elseif (strlen($firstName) < 2) {
        $firstNameErr = "Le prénom doit contenir au moins 2 caractères";
    }

    if ($lastName === '') {
        $lastNameErr = "Nom est requis";
    } elseif (strlen($lastName) < 2) {
        $lastNameErr = "Le nom doit contenir au moins 2 caractères";
    }

    if ($email === '') {
        $emailErr = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Format d'e-mail invalide";
    }

    if ($password === '') {
        $passwordErr = "Mot de passe est requis";
    } elseif (strlen($password) < 8) {
        $passwordErr = "Le mot de passe doit contenir au moins 8 caractères";
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $passwordErr = "Le mot de passe doit contenir au moins une majuscule";
    } elseif (!preg_match('/[a-z]/', $password)) {
        $passwordErr = "Le mot de passe doit contenir au moins une minuscule";
    } elseif (!preg_match('/[0-9]/', $password)) {
        $passwordErr = "Le mot de passe doit contenir au moins un chiffre";
    }

    if ($confirmPassword === '') {
        $confirmPasswordErr = "La confirmation du mot de passe est requise";
    } elseif ($password !== $confirmPassword) {
        $confirmPasswordErr = "Les mots de passe ne correspondent pas";
    }

    if ($role === '' || !in_array($role, ['etudiant', 'enseignant'])) {
        $roleErr = "Le rôle est requis";
    }

    // Si pas d'erreurs, procéder à l'insertion
    if (!$firstNameErr && !$lastNameErr && !$emailErr && !$passwordErr && !$confirmPasswordErr && !$roleErr) {

        try {
            // Vérifier si l'email existe déjà (unicité)
            $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $check->execute([$email]);

            if ($check->fetch()) {
                $emailErr = "L'adresse e-mail existe déjà";
            } else {
                // Hashage sécurisé du mot de passe
                $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

                // Insertion dans la base de données
                $stmt = $pdo->prepare(
                    "INSERT INTO users (nom, email, password_hash, role, created_at) 
                     VALUES (?, ?, ?, ?, NOW())"
                );

                $stmt->execute([
                    $firstName . ' ' . $lastName,
                    $email,
                    $passwordHash,
                    $role
                ]);

                // Régénérer le token CSRF après inscription
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                // Redirection vers la page de connexion
                header("Location: login.php?registered=1");
                exit;
            }
        } catch (PDOException $e) {
            error_log("Erreur d'inscription: " . $e->getMessage());
            $emailErr = "Une erreur est survenue. Veuillez réessayer.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plateforme de Quiz - Inscription</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 100%);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-indigo-600 rounded-2xl mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-semibold text-gray-800 mb-2">QODEX</h1>
            <p class="text-gray-600">Créez votre compte pour commencer</p>
        </div>

        <!-- Signup Form -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <!-- CSRF Token -->
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="role" id="role" value="etudiant">

            <div class="bg-white rounded-3xl shadow-xl p-8">
                <!-- User Type Selection -->
                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-3">Je suis</label>
                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" id="studentBtn" class="flex items-center justify-center gap-2 px-4 py-3 border-2 border-indigo-600 bg-indigo-50 text-indigo-600 rounded-xl font-medium hover:bg-indigo-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Étudiant
                        </button>
                        <button type="button" id="teacherBtn" class="flex items-center justify-center gap-2 px-4 py-3 border-2 border-gray-300 bg-white text-gray-600 rounded-xl font-medium hover:bg-gray-50 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Enseignant
                        </button>
                    </div>
                    <?php if ($roleErr): ?>
                    <span class="text-red-600 text-sm mt-1 block"><?php echo $roleErr; ?></span>
                    <?php endif; ?>
                </div>

                <!-- Name Fields -->
                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div>
                        <label for="firstName" class="block text-gray-700 font-medium mb-2">Prénom</label>
                        <input
                            type="text"
                            id="firstName"
                            name="firstName"
                            value="<?php echo isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : ''; ?>"
                            placeholder="Mohamed"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all <?php echo $firstNameErr ? 'border-red-500' : ''; ?>">
                        <?php if ($firstNameErr): ?>
                        <span class="text-red-600 text-sm mt-1 block"><?php echo $firstNameErr; ?></span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label for="lastName" class="block text-gray-700 font-medium mb-2">Nom</label>
                        <input
                            type="text"
                            id="lastName"
                            name="lastName"
                            value="<?php echo isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName']) : ''; ?>"
                            placeholder="Boudlal"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all <?php echo $lastNameErr ? 'border-red-500' : ''; ?>">
                        <?php if ($lastNameErr): ?>
                        <span class="text-red-600 text-sm mt-1 block"><?php echo $lastNameErr; ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Email Field -->
                <div class="mb-5">
                    <label for="email" class="block text-gray-700 font-medium mb-2">Adresse email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                        placeholder="votre@email.com"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all <?php echo $emailErr ? 'border-red-500' : ''; ?>">
                    <?php if ($emailErr): ?>
                    <span class="text-red-600 text-sm mt-1 block"><?php echo $emailErr; ?></span>
                    <?php endif; ?>
                </div>

                <!-- Password Field -->
                <div class="mb-5">
                    <label for="password" class="block text-gray-700 font-medium mb-2">Mot de passe</label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all pr-12 <?php echo $passwordErr ? 'border-red-500' : ''; ?>">
                        <button
                            type="button"
                            id="togglePassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    <?php if ($passwordErr): ?>
                    <span class="text-red-600 text-sm mt-1 block"><?php echo $passwordErr; ?></span>
                    <?php else: ?>
                    <span class="text-gray-500 text-xs mt-1 block">Minimum 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre</span>
                    <?php endif; ?>
                </div>

                <!-- Confirm Password Field -->
                <div class="mb-5">
                    <label for="confirmPassword" class="block text-gray-700 font-medium mb-2">Confirmer le mot de passe</label>
                    <div class="relative">
                        <input
                            type="password"
                            id="confirmPassword"
                            name="confirmPassword"
                            placeholder="••••••••"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all pr-12 <?php echo $confirmPasswordErr ? 'border-red-500' : ''; ?>">
                        <button
                            type="button"
                            id="toggleConfirmPassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    <?php if ($confirmPasswordErr): ?>
                    <span class="text-red-600 text-sm mt-1 block"><?php echo $confirmPasswordErr; ?></span>
                    <?php endif; ?>
                </div>

                <!-- Terms and Conditions -->
                <div class="mb-6">
                    <label class="flex items-start cursor-pointer">
                        <input checked type="checkbox" required class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-1">
                        <span class="ml-2 text-sm text-gray-700">
                            J'accepte les <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium">conditions d'utilisation</a> et la <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium">politique de confidentialité</a>
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-medium hover:bg-indigo-700 transition-colors mb-6">
                    S'inscrire
                </button>

                <!-- Security Notice -->
                <div class="flex items-start gap-3 text-sm text-gray-600 bg-gray-50 p-4 rounded-xl">
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <p>Vos données sont protégées par chiffrement SSL/TLS et vos mots de passe sont hashés avec bcrypt.</p>
                </div>
            </div>
        </form>

        <!-- Login Link -->
        <div class="text-center mt-6">
            <p class="text-gray-700">
                Vous avez déjà un compte ?
                <a href="login.php" class="text-indigo-600 hover:text-indigo-700 font-medium">Se connecter</a>
            </p>
        </div>

        <!-- Secure Connection -->
        <div class="flex items-center justify-center gap-2 mt-4 text-sm text-gray-600">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
            </svg>
            <span>Connexion sécurisée SSL/TLS</span>
        </div>
    </div>

    <script>
        // Toggle user type selection
        const studentBtn = document.getElementById('studentBtn');
        const teacherBtn = document.getElementById('teacherBtn');
        const roleInput = document.getElementById('role');

        studentBtn.addEventListener('click', () => {
            studentBtn.classList.add('border-indigo-600', 'bg-indigo-50', 'text-indigo-600');
            studentBtn.classList.remove('border-gray-300', 'bg-white', 'text-gray-600');
            teacherBtn.classList.remove('border-indigo-600', 'bg-indigo-50', 'text-indigo-600');
            teacherBtn.classList.add('border-gray-300', 'bg-white', 'text-gray-600');
            roleInput.value = 'etudiant';
        });

        teacherBtn.addEventListener('click', () => {
            teacherBtn.classList.add('border-indigo-600', 'bg-indigo-50', 'text-indigo-600');
            teacherBtn.classList.remove('border-gray-300', 'bg-white', 'text-gray-600');
            studentBtn.classList.remove('border-indigo-600', 'bg-indigo-50', 'text-indigo-600');
            studentBtn.classList.add('border-gray-300', 'bg-white', 'text-gray-600');
            roleInput.value = 'enseignant';
        });

        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        });

        // Toggle confirm password visibility
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('confirmPassword');

        toggleConfirmPassword.addEventListener('click', () => {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
        });
    </script>

</body>

</html>