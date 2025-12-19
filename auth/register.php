<?php
session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once '../config/database.php';

$firstNameErr = $lastNameErr = $emailErr = $passwordErr = $confirmPasswordErr = $roleErr = '';
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Erreur CSRF: Requête invalide");
    }

    $firstName = trim($_POST["firstName"]);
    $lastName  = trim($_POST["lastName"]);
    $email     = trim($_POST["email"]);
    $password  = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];
    $role      = $_POST["role"];

    if ($firstName === '') {
        $firstNameErr = "Prénom est requis";
    } elseif (strlen($firstName) < 2) {
        $firstNameErr = "Le prénom doit contenir au moins 2 caractères";
    } elseif (!preg_match("/^[a-zA-ZÀ-ÿ\s'-]+$/u", $firstName)) {
        $firstNameErr = "Le prénom contient des caractères invalides";
    }

    if ($lastName === '') {
        $lastNameErr = "Nom est requis";
    } elseif (strlen($lastName) < 2) {
        $lastNameErr = "Le nom doit contenir au moins 2 caractères";
    } elseif (!preg_match("/^[a-zA-ZÀ-ÿ\s'-]+$/u", $lastName)) {
        $lastNameErr = "Le nom contient des caractères invalides";
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

    if (!$firstNameErr && !$lastNameErr && !$emailErr && !$passwordErr && !$confirmPasswordErr && !$roleErr) {

        try {
            $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $check->execute([$email]);

            if ($check->fetch()) {
                $emailErr = "L'adresse e-mail existe déjà";
            } else {
                $passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

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

                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

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
    <script src="https://cdn.tailwindcss.com"></script>
    <title>QODEX - Inscription</title>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .password-strength {
            height: 4px;
            transition: all 0.3s;
        }
    </style>
</head>

<body class="flex items-center justify-center p-4">
    <div class="w-full max-w-lg">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl mb-4 shadow-lg">
                <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white mb-2">QODEX</h1>
            <p class="text-white text-opacity-90">Créez votre compte pour commencer</p>
        </div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="registerForm"> <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="role" id="role" value="etudiant">

            <div class="bg-white rounded-3xl shadow-2xl p-8">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            Enseignant
                        </button>
                    </div>
                    <?php if ($roleErr): ?>
                        <span class="text-red-600 text-sm mt-1 block"><?php echo $roleErr; ?></span>
                    <?php endif; ?>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div>
                        <label for="firstName" class="block text-gray-700 font-medium mb-2">Prénom *</label>
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
                        <label for="lastName" class="block text-gray-700 font-medium mb-2">Nom *</label>
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
                <div class="mb-5">
                    <label for="email" class="block text-gray-700 font-medium mb-2">Adresse email *</label>
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
                <div class="mb-5">
                    <label for="password" class="block text-gray-700 font-medium mb-2">Mot de passe *</label>
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
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="mt-2">
                        <div class="password-strength bg-gray-200 rounded-full" id="strengthBar"></div>
                        <p class="text-xs text-gray-500 mt-1" id="strengthText">Force du mot de passe</p>
                    </div>
                    <?php if ($passwordErr): ?>
                        <span class="text-red-600 text-sm mt-1 block"><?php echo $passwordErr; ?></span>
                    <?php else: ?>
                        <span class="text-gray-500 text-xs mt-1 block">Minimum 8 caractères, 1 majuscule, 1 minuscule, 1 chiffre</span>
                    <?php endif; ?>
                </div>
                <div class="mb-5">
                    <label for="confirmPassword" class="block text-gray-700 font-medium mb-2">Confirmer le mot de passe *</label>
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
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
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
                <div class="mb-6">
                    <label class="flex items-start cursor-pointer">
                        <input type="checkbox" required class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 mt-1">
                        <span class="ml-2 text-sm text-gray-700">
                            J'accepte les <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium underline">conditions d'utilisation</a> et la <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium underline">politique de confidentialité</a>
                        </span>
                    </label>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl font-medium hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 mb-6">
                    S'inscrire
                </button>
                <div class="flex items-start gap-3 text-sm text-gray-600 bg-gray-50 p-4 rounded-xl">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <p>Vos données sont protégées par chiffrement SSL/TLS et vos mots de passe sont hashés avec bcrypt (cost: 12).</p>
                </div>
            </div>
        </form>
        <div class="text-center mt-6">
            <p class="text-white">
                Vous avez déjà un compte ?
                <a href="login.php" class="text-white font-bold hover:underline">Se connecter</a>
            </p>
        </div>
        <div class="flex items-center justify-center gap-2 mt-4 text-sm text-white text-opacity-90">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
            </svg>
            <span>Connexion sécurisée SSL/TLS</span>
        </div>
    </div>

    <script>
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

        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        });

        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('confirmPassword');

        toggleConfirmPassword.addEventListener('click', () => {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
        });

        const strengthBar = document.getElementById('strengthBar');
        const strengthText = document.getElementById('strengthText');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;

            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^a-zA-Z0-9]/.test(password)) strength++;

            const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500', 'bg-green-600'];
            const texts = ['Très faible', 'Faible', 'Moyen', 'Fort', 'Très fort'];
            const widths = ['20%', '40%', '60%', '80%', '100%'];

            strengthBar.className = 'password-strength rounded-full ' + colors[strength - 1];
            strengthBar.style.width = widths[strength - 1] || '0%';
            strengthText.textContent = texts[strength - 1] || 'Trop court';
            strengthText.className = 'text-xs mt-1 ' + (strength >= 3 ? 'text-green-600' : 'text-gray-500');
        });
    </script>

</body>

</html>