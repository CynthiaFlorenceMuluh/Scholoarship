<?php
session_start();
$error = "";
$success = "";
$step = $_SESSION['reset_step'] ?? 1;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'request') {
        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Enter a valid account email address.";
        } else {
            /*
             * DATABASE / EMAIL INTEGRATION POINT:
             * Look up the account, create a secure random reset token,
             * store a hashed token with an expiry, and email the reset link.
             * No database or email service is included here.
             */
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_step'] = 2;
            $step = 2;
            $success = "Verification step completed for the demo. In production, send a secure reset link by email.";
        }
    } elseif ($action === 'reset') {
        $password = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (strlen($password) < 8) {
            $error = "New password must contain at least 8 characters.";
        } elseif ($password !== $confirm) {
            $error = "The passwords do not match.";
        } else {
            /*
             * DATABASE INTEGRATION POINT:
             * Verify the reset token and expiry, then update the user's
             * password using password_hash($password, PASSWORD_DEFAULT).
             */
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $_SESSION['reset_step'] = 1;
            $step = 1;
            $success = "Password reset validated successfully. Connect this step to your database to save the new password.";
        }
    }
}

$pageTitle = "Forgot Password";
require_once "../includes/header.php";
?>
<section class="auth-shell">
    <div class="auth-visual">
        <div>
            <span class="pill">Account recovery</span>
            <h1>Let's get you back into your account.</h1>
            <p>Recover your password through a secure verification and reset process.</p>
            <ul class="feature-list">
                <li><span>1</span><div><b>Verify your email</b><span>Tell us which account you need to recover.</span></div></li>
                <li><span>2</span><div><b>Set a new password</b><span>Create a strong password for your account.</span></div></li>
                <li><span>3</span><div><b>Continue shopping</b><span>Return to your customer account.</span></div></li>
            </ul>
        </div>
    </div>

    <div class="auth-content">
        <div class="auth-card">
            <span class="eyebrow">Password recovery</span>
            <h2><?= $step === 1 ? 'Forgot your password?' : 'Create a new password' ?></h2>
            <p class="subtitle">
                <?= $step === 1 ? 'Enter your account email to begin the recovery process.' : 'Choose a new password for your account.' ?>
            </p>

            <?php if ($error): ?>
                <div class="alert alert-error" role="alert"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success" role="status"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <?php if ($step === 1): ?>
                <form method="post" class="form-grid">
                    <input type="hidden" name="action" value="request">
                    <div class="form-group">
                        <label for="email">Account email</label>
                        <input id="email" name="email" type="email" autocomplete="email" required>
                    </div>
                    <button class="btn btn-primary btn-full" type="submit">Continue</button>
                </form>
            <?php else: ?>
                <form method="post" class="form-grid">
                    <input type="hidden" name="action" value="reset">
                    <div class="form-group">
                        <label for="new_password">New password</label>
                        <div class="input-wrap">
                            <input id="new_password" name="new_password" type="password" minlength="8" autocomplete="new-password" required>
                            <button class="password-toggle" type="button" data-target="new_password" aria-label="Show password">Show</button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm new password</label>
                        <div class="input-wrap">
                            <input id="confirm_password" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required>
                            <button class="password-toggle" type="button" data-target="confirm_password" aria-label="Show password">Show</button>
                        </div>
                    </div>
                    <button class="btn btn-primary btn-full" type="submit">Reset password</button>
                </form>
            <?php endif; ?>

            <p class="form-footer"><a href="login.php">← Back to sign in</a></p>
        </div>
    </div>
</section>
<?php require_once "../includes/footer.php"; ?>
