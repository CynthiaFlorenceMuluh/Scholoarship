<?php
session_start();
$error = "";
$success = $_GET['success'] ?? "";

/*
|--------------------------------------------------------------------------
| DATABASE INTEGRATION POINT
|--------------------------------------------------------------------------
| Replace the demo block below with your database user lookup later.
| Example flow:
| 1. Query user by email/username using PDO prepared statements.
| 2. Verify the submitted password with password_verify().
| 3. Store the authenticated user in $_SESSION['user'].
| 4. Redirect based on role.
|
| No database connection or SQL is included in this project.
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if ($login === '' || $password === '') {
        $error = "Please enter your email/username and password.";
    } else {
        // DEMO ONLY: remove this block when connecting your database.
        $demoUsers = [
            'customer@shopease.test' => [
                'id' => 1,
                'name' => 'Demo Customer',
                'email' => 'customer@shopease.test',
                'role' => 'customer',
                'password_hash' => password_hash('Customer@123', PASSWORD_DEFAULT)
            ],
            'admin@shopease.test' => [
                'id' => 2,
                'name' => 'Demo Administrator',
                'email' => 'admin@shopease.test',
                'role' => 'admin',
                'password_hash' => password_hash('Admin@123', PASSWORD_DEFAULT)
            ]
        ];

        $account = $demoUsers[strtolower($login)] ?? null;
        if (!$account || !password_verify($password, $account['password_hash'])) {
            $error = "Invalid login details. Please check your credentials.";
        } else {
            session_regenerate_id(true);
            $_SESSION['user'] = [
                'id' => $account['id'],
                'name' => $account['name'],
                'email' => $account['email'],
                'role' => $account['role']
            ];
            if ($remember) {
                $_SESSION['remember_requested'] = true;
            }

            if ($account['role'] === 'admin') {
                // Change this to your real admin dashboard path.
                header("Location: ../account/profile.php?welcome=admin");
            } else {
                header("Location: ../account/profile.php?welcome=1");
            }
            exit;
        }
    }
}

$pageTitle = "Login";
require_once "../includes/header.php";
?>
<section class="auth-shell">
    <div class="auth-visual">
        <div>
            <span class="pill">Accessible commerce</span>
            <h1>Shop with confidence. Shop your way.</h1>
            <p>Sign in to manage your profile, addresses and personalized accessibility preferences.</p>
            <ul class="feature-list">
                <li><span>✓</span><div><b>Personalized experience</b><span>Keep your preferred text, contrast and motion settings.</span></div></li>
                <li><span>✓</span><div><b>Secure account</b><span>Session-based authentication ready for your PHP backend.</span></div></li>
                <li><span>✓</span><div><b>Inclusive by design</b><span>Keyboard-friendly and accessibility-focused interface.</span></div></li>
            </ul>
        </div>
    </div>

    <div class="auth-content">
        <div class="auth-card">
            <span class="eyebrow">Welcome back</span>
            <h2>Sign in to your account</h2>
            <p class="subtitle">Access your customer account and saved preferences.</p>

            <?php if ($error): ?>
                <div class="alert alert-error" role="alert"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success" role="status"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="post" action="" class="form-grid" novalidate>
                <div class="form-group">
                    <label for="login">Email or username</label>
                    <input id="login" name="login" type="text" autocomplete="username" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <input id="password" name="password" type="password" autocomplete="current-password" required>
                        <button class="password-toggle" type="button" data-target="password" aria-label="Show password">Show</button>
                    </div>
                </div>

                <div class="form-row">
                    <label class="check">
                        <input type="checkbox" name="remember" value="1">
                        <span>Remember me</span>
                    </label>
                    <a href="forgot-password.php">Forgot password?</a>
                </div>

                <button class="btn btn-primary btn-full" type="submit">Sign in</button>
            </form>

            <div class="divider">or</div>
            <p class="form-footer">New to ShopEase? <a href="register.php"><strong>Create an account</strong></a></p>

            <p class="form-footer" style="font-size:.82rem">
                Demo customer: customer@shopease.test / Customer@123
            </p>
        </div>
    </div>
</section>
<?php require_once "../includes/footer.php"; ?>
