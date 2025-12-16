<?php
session_start();
require_once '../config/database.php';

$emailErr = $passwordErr = $loginErr = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $rememberMe = isset($_POST["remember"]);
    if ($email === '') {
        $emailErr = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Format d'e-mail invalide";
    }

    if ($password === '') {
        $passwordErr = "Mot de passe est requis";
    }

    if (!$emailErr && !$passwordErr) {
        try {
            $stmt = $pdo->prepare("SELECT id, nom, email, password_hash, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nom'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['logged_in'] = true;

                if ($rememberMe) {
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', true, true);
                }

                if ($user['role'] === 'enseignant') {
                    header("Location: ../enseignant/dashboard.php");
                } else {
                    header("Location: ../etudiant/dashboard.php");
                }
                exit;
            } else {
                $loginErr = "Email ou mot de passe incorrect";
            }
        } catch (PDOException $e) {
            $loginErr = "Erreur de connexion. Veuillez réessayer.";
            error_log($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plateforme de Quiz - Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            background: linear-gradient(135deg, #e0e7ff 0%, #ddd6fe 100%);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-indigo-600 rounded-2xl mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-semibold text-gray-800 mb-2">QODEX</h1>
            <p class="text-gray-600">Connectez-vous pour accéder à votre espace</p>
        </div>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="bg-white rounded-3xl shadow-xl p-8">

                <?php if ($loginErr): ?>
                    <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl">
                        <div class="flex items-center gap-2 text-red-800">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium"><?php echo $loginErr; ?></span>
                        </div>
                    </div>
                <?php endif; ?>

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
                    <?php endif; ?>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">Se souvenir de moi</span>
                    </label>
                    <a href="forgot-password.php" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium">Mot de passe oublié ?</a>
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded-xl font-medium hover:bg-indigo-700 transition-colors mb-6">
                    Se connecter
                </button>

                <div class="flex items-start gap-3 text-sm text-gray-600 bg-gray-50 p-4 rounded-xl">
                    <svg class="w-5 h-5 text-gray-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <p>Vos données sont protégées par chiffrement SSL/TLS et vos mots de passe sont hashés avec bcrypt.</p>
                </div>
            </div>
        </form>

        <div class="text-center mt-6">
            <p class="text-gray-700">
                Pas encore de compte ?
                <a href="register.php" class="text-indigo-600 hover:text-indigo-700 font-medium">S'inscrire</a>
            </p>
        </div>

        <div class="flex items-center justify-center gap-2 mt-4 text-sm text-gray-600">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
            </svg>
            <span>Connexion sécurisée SSL/TLS</span>
        </div>
    </div>

    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
        });
    </script>
</body>

</html>