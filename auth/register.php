<?php
session_start();
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $terms = isset($_POST['terms']);

    if ($name === '' || $email === '' || $phone === '' || $password === '' || $confirm === '') {
        $error = "Please complete all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $error = "Password must contain at least 8 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } elseif (!$terms) {
        $error = "You must accept the Terms & Conditions.";
    } else {
        /*
         * DATABASE INTEGRATION POINT:
         * 1. Check whether $email already exists.
         * 2. Hash with password_hash($password, PASSWORD_DEFAULT).
         * 3. Insert the customer.
         * 4. Create a session and redirect to profile/login.
         *
         * No SQL/database code is included.
         */
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $success = "Registration validated successfully. Connect this section to your database to create the customer account.";
    }
}

$pageTitle = "Create Account";
require_once "../includes/header.php";
?>
<section class="auth-shell">
    <div class="auth-visual">
        <div>
            <span class="pill">Create your account</span>
            <h1>A better shopping experience starts here.</h1>
            <p>Create a customer account to manage your personal details, delivery addresses and accessibility preferences.</p>
            <ul class="feature-list">
                <li><span>✓</span><div><b>One account</b><span>Keep your customer information in one secure place.</span></div></li>
                <li><span>✓</span><div><b>Accessible controls</b><span>Personalize your shopping interface to your needs.</span></div></li>
                <li><span>✓</span><div><b>Ready for PHP</b><span>Validation and password hashing are prepared for database integration.</span></div></li>
            </ul>
        </div>
    </div>

    <div class="auth-content">
        <div class="auth-card">
            <span class="eyebrow">New customer</span>
            <h2>Create your account</h2>
            <p class="subtitle">Fill in your details to get started.</p>

            <?php if ($error): ?>
                <div class="alert alert-error" role="alert"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success" role="status"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <form method="post" class="form-grid">
                <div class="form-group">
                    <label for="full_name">Full name</label>
                    <input id="full_name" name="full_name" type="text" autocomplete="name" required>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="email">Email address</label>
                        <input id="email" name="email" type="email" autocomplete="email" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone number</label>
                        <input id="phone" name="phone" type="tel" autocomplete="tel" required>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required>
                            <button class="password-toggle" type="button" data-target="password" aria-label="Show password">Show</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm password</label>
                        <div class="input-wrap">
                            <input id="confirm_password" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required>
                            <button class="password-toggle" type="button" data-target="confirm_password" aria-label="Show password">Show</button>
                        </div>
                    </div>
                </div>
                <label class="check">
                    <input type="checkbox" name="terms" required>
                    <span>I agree to the <a href="#">Terms & Conditions</a> and <a href="#">Privacy Policy</a>.</span>
                </label>
                <button class="btn btn-primary btn-full" type="submit">Create account</button>
            </form>

            <p class="form-footer">Already have an account? <a href="login.php"><strong>Sign in</strong></a></p>
        </div>
    </div>
</section>
<?php require_once "../includes/footer.php"; ?>
