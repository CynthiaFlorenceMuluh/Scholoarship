<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php?success=Please sign in to view your account.");
    exit;
}

$user = $_SESSION['user'];
$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'profile') {
        $name = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($name === '' || $phone === '') {
            $error = "Full name and phone number are required.";
        } else {
            /*
             * DATABASE INTEGRATION POINT:
             * Update the authenticated customer's profile in your database.
             * Use the authenticated session user ID and prepared statements.
             *
             * PROFILE IMAGE:
             * In production, validate MIME type/size, generate a random server filename,
             * store outside executable paths, and save only its path in the database.
             */
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['phone'] = $phone;

            if (!empty($_FILES['profile_image']['tmp_name']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                $mime = mime_content_type($_FILES['profile_image']['tmp_name']);
                if (isset($allowed[$mime]) && $_FILES['profile_image']['size'] <= 2 * 1024 * 1024) {
                    // Demo only: data URI avoids creating an upload directory.
                    $raw = file_get_contents($_FILES['profile_image']['tmp_name']);
                    $_SESSION['user']['profile_image'] = 'data:' . $mime . ';base64,' . base64_encode($raw);
                }
            }

            $user = $_SESSION['user'];
            $success = "Profile details validated and updated for this demo session.";
        }
    } elseif ($action === 'password') {
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($current === '' || $new === '' || $confirm === '') {
            $error = "Complete all password fields.";
        } elseif (strlen($new) < 8) {
            $error = "New password must contain at least 8 characters.";
        } elseif ($new !== $confirm) {
            $error = "New passwords do not match.";
        } else {
            /*
             * DATABASE INTEGRATION POINT:
             * Retrieve the stored password hash and use password_verify($current, $hash).
             * Then update with password_hash($new, PASSWORD_DEFAULT).
             */
            $success = "Password change validated. Connect this section to your database to persist it.";
        }
    }
}

$pageTitle = "My Account";
require_once "../includes/header.php";
?>
<section class="dashboard">
    <div class="container">
        <div class="page-heading">
            <div>
                <span class="eyebrow">Customer account</span>
                <h1>My profile</h1>
                <p>Manage your personal information and account preferences.</p>
            </div>
            <a class="btn btn-outline" href="logout.php">Logout</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error" role="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success" role="status"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="profile-layout">
            <aside class="side-card">
                <?php if (!empty($user['profile_image'])): ?>
                    <img class="avatar" src="<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile picture">
                <?php else: ?>
                    <div class="avatar" aria-hidden="true"><?= strtoupper(substr($user['name'], 0, 1)) ?></div>
                <?php endif; ?>
                <p class="account-name"><?= htmlspecialchars($user['name']) ?></p>
                <p class="account-role"><?= htmlspecialchars(ucfirst($user['role'])) ?> account</p>
                <nav class="account-nav" aria-label="Account navigation">
                    <a class="active" href="profile.php">Profile & account</a>
                    <a href="addresses.php">My addresses</a>
                    <a href="../auth/forgot-password.php">Reset password</a>
                    <a href="logout.php">Logout</a>
                </nav>
            </aside>

            <div class="content-card">
                <div class="section-header">
                    <div>
                        <span class="eyebrow">Personal information</span>
                        <h2>Account details</h2>
                    </div>
                </div>

                <form method="post" class="form-grid" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="profile">
                    <div class="grid-2">
                        <div class="form-group">
                            <label for="profileImage">Profile picture</label>
                            <input id="profileImage" name="profile_image" type="file" accept="image/png,image/jpeg,image/webp">
                            <small class="subtitle">JPG, PNG or WebP. In production, validate and securely store the uploaded file.</small>
                        </div>
                        <div class="form-group">
                            <label>Preview</label>
                            <div>
                                <img id="avatarPreview" alt="Profile picture preview"
                                     style="width:82px;height:82px;border-radius:50%;object-fit:cover;background:#dceff4"
                                     src="<?= htmlspecialchars($user['profile_image'] ?? '') ?>"
                                     onerror="this.style.display='none'">
                            </div>
                        </div>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label for="full_name">Full name</label>
                            <input id="full_name" name="full_name" type="text" value="<?= htmlspecialchars($user['name']) ?>" autocomplete="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <input id="email" type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                            <small class="subtitle">Email changes should be verified through your backend.</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone number</label>
                        <input id="phone" name="phone" type="tel" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" autocomplete="tel" required>
                    </div>
                    <button class="btn btn-primary" type="submit">Save profile</button>
                </form>

                <div class="divider"></div>

                <div class="section-header">
                    <div>
                        <span class="eyebrow">Account settings</span>
                        <h2>Preferences & account controls</h2>
                    </div>
                </div>
                <div class="grid-2">
                    <div class="address-card">
                        <h3>Accessibility</h3>
                        <p>Change font size, high contrast, reduced motion and simplified layout.</p>
                        <button class="btn btn-outline accessibility-trigger" type="button"
                                aria-controls="accessibilityPanel" aria-expanded="false">Open accessibility settings</button>
                    </div>
                    <div class="address-card">
                        <h3>Account status</h3>
                        <p><strong>Role:</strong> <?= htmlspecialchars(ucfirst($user['role'])) ?></p>
                        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                        <a class="btn btn-outline" href="logout.php">Sign out</a>
                    </div>
                </div>

                <div class="divider"></div>

                <div class="section-header">
                    <div>
                        <span class="eyebrow">Security</span>
                        <h2>Change password</h2>
                    </div>
                </div>

                <form method="post" class="form-grid">
                    <input type="hidden" name="action" value="password">
                    <div class="form-group">
                        <label for="current_password">Current password</label>
                        <input id="current_password" name="current_password" type="password" autocomplete="current-password" required>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label for="new_password">New password</label>
                            <input id="new_password" name="new_password" type="password" minlength="8" autocomplete="new-password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm new password</label>
                            <input id="confirm_password" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required>
                        </div>
                    </div>
                    <button class="btn btn-outline" type="submit">Update password</button>
                </form>
            </div>
        </div>
    </div>
</section>
<?php require_once "../includes/footer.php"; ?>
