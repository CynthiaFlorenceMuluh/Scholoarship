<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$currentPage = basename($_SERVER['PHP_SELF']);
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Accessible e-commerce customer account">
    <title><?= htmlspecialchars($pageTitle ?? 'ShopEase') ?> | ShopEase</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>

<header class="site-header">
    <div class="container nav-wrap">
        <a class="brand" href="../auth/login.php" aria-label="ShopEase home">
            <span class="brand-mark">S</span>
            <span>Shop<span>Ease</span></span>
        </a>

        <nav class="main-nav" aria-label="Primary navigation">
            <a href="../auth/login.php">Login</a>
            <a href="../auth/register.php">Create Account</a>
            <?php if ($user): ?>
                <a href="../account/profile.php">My Account</a>
                <a href="../account/addresses.php">Addresses</a>
            <?php endif; ?>
        </nav>

        <button class="icon-btn accessibility-trigger" type="button"
                aria-label="Open accessibility settings" aria-controls="accessibilityPanel"
                aria-expanded="false">Aa</button>
    </div>
</header>

<aside id="accessibilityPanel" class="accessibility-panel" aria-label="Accessibility settings" hidden>
    <div class="panel-head">
        <div>
            <span class="eyebrow">Personalize</span>
            <h2>Accessibility</h2>
        </div>
        <button class="icon-btn panel-close" type="button" aria-label="Close accessibility settings">×</button>
    </div>
    <div class="setting-group">
        <label for="fontSizeSetting">Text size</label>
        <select id="fontSizeSetting">
            <option value="normal">Default</option>
            <option value="large">Large</option>
            <option value="xlarge">Extra large</option>
        </select>
    </div>
    <label class="setting-row">
        <span>
            <strong>High contrast</strong>
            <small>Increase visual contrast</small>
        </span>
        <input id="contrastSetting" type="checkbox">
    </label>
    <label class="setting-row">
        <span>
            <strong>Reduced motion</strong>
            <small>Reduce non-essential animations</small>
        </span>
        <input id="motionSetting" type="checkbox">
    </label>
    <label class="setting-row">
        <span>
            <strong>Simplified layout</strong>
            <small>Reduce visual complexity</small>
        </span>
        <input id="simpleSetting" type="checkbox">
    </label>
    <button id="resetAccessibility" class="btn btn-outline btn-full" type="button">Reset preferences</button>
    <p class="settings-saved" id="settingsSaved" role="status" aria-live="polite"></p>
</aside>

<main id="main-content">
