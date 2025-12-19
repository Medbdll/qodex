<?php
session_start();
require_once '../config/database.php';

if (isset($_SESSION['user_id']) && $_SESSION['logged_in'] === true) {

    if ($_SESSION['user_role'] === 'enseignant') {
        header("Location: ../enseignant/dashboard.php");
    } else {
        header("Location: ../etudiant/dashboard.php");
    }
    exit;
}

$emailErr = $passwordErr = $loginErr = '';
$registered = isset($_GET['registered']) && $_GET['registered'] == 1;
$logout = isset($_GET['logout']) && $_GET['logout'] == 'success';

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

        $stmt = $pdo->prepare("
            SELECT id, nom, email, password_hash, role 
            FROM users 
            WHERE email = ? AND deleted_at IS NULL
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password_hash'])) {

            session_regenerate_id(true);

            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['nom'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role']  = $user['role'];
            $_SESSION['logged_in']  = true;
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

            if ($rememberMe) {
                $token = bin2hex(random_bytes(32));
                setcookie(
                    'remember_token',
                    $token,
                    time() + (30 * 24 * 60 * 60),
                    '/',
                    '',
                    isset($_SERVER['HTTPS']),
                    true
                );
            }

            if ($user['role'] === 'enseignant') {
                header("Location: ../enseignant/dashboard.php");
            } else {
                header("Location: ../etudiant/dashboard.php");
            }
            exit;

        } else {
            $loginErr = "Email ou mot de passe incorrect";
            error_log("Failed login attempt for email: " . $email);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QODEX - Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-animate {
            animation: slideIn 0.3s ease-out;
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
            <p class="text-white text-opacity-90">Connectez-vous pour accéder à votre espace</p>
        </div>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="bg-white rounded-3xl shadow-2xl p-8">

                <?php if ($registered): ?>
                    <div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-xl alert-animate">
                        <div class="flex items-center gap-2 text-green-800">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium">Inscription réussie ! Vous pouvez maintenant vous connecter.</span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($logout): ?>
                    <div class="mb-5 p-4 bg-blue-50 border border-blue-200 rounded-xl alert-animate">
                        <div class="flex items-center gap-2 text-blue-800">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium">Vous avez été déconnecté avec succès.</span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($loginErr): ?>
                    <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl alert-animate">
                        <div class="flex items-center gap-2 text-red-800">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium"><?php echo $loginErr; ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mb-5">
                    <label for="email" class="block text-gray-700 font-medium mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                        Adresse email
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                        placeholder="votre@email.com"
                        autofocus
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:border-transparent transition-all <?php echo $emailErr ? 'border-red-500' : ''; ?>">
                    <?php if ($emailErr): ?>
                        <span class="text-red-600 text-sm mt-1 block"><?php echo $emailErr; ?></span>
                    <?php endif; ?>
                </div>

                <div class="mb-5">
                    <label for="password" class="block text-gray-700 font-medium mb-2">
                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                        Mot de passe
                    </label>
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
                    <?php if ($passwordErr): ?>
                        <span class="text-red-600 text-sm mt-1 block"><?php echo $passwordErr; ?></span>
                    <?php endif; ?>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700 group-hover:text-gray-900">Se souvenir de moi</span>
                    </label>
                    <a href="forgot-password.php" class="text-sm text-indigo-600 hover:text-indigo-700 font-medium hover:underline">Mot de passe oublié ?</a>
                </div>

                <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-xl font-medium hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 mb-6">
                    Se connecter
                </button>

                <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-indigo-100">
                    <p class="text-sm font-medium text-gray-700 mb-2">🚀 Connexion rapide de test :</p>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-white p-2 rounded-lg">
                            <p class="font-semibold text-indigo-600">Enseignant</p>
                            <p class="text-gray-600">ahmed@enseignant.com</p>
                            <p class="text-gray-600">Test123456</p>
                        </div>
                        <div class="bg-white p-2 rounded-lg">
                            <p class="font-semibold text-green-600">Étudiant</p>
                            <p class="text-gray-600">youssef@etudiant.com</p>
                            <p class="text-gray-600">Test123456</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-start gap-3 text-sm text-gray-600 bg-gray-50 p-4 rounded-xl">
                    <svg class="w-5 h-5 text-green-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <p>Vos données sont protégées par chiffrement SSL/TLS et vos mots de passe sont hashés avec bcrypt.</p>
                </div>
            </div>
        </form>

        <div class="text-center mt-6">
            <p class="text-white">
                Pas encore de compte ?
                <a href="register.php" class="text-white font-bold hover:underline">S'inscrire</a>
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
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', () => {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            const svg = togglePassword.querySelector('svg');
            if (type === 'text') {
                svg.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                `;
            } else {
                svg.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        });

        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert-animate');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>

</html>