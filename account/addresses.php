<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_SESSION['addresses'])) {
    $_SESSION['addresses'] = [
        [
            'id' => 1,
            'label' => 'Home',
            'full_name' => $_SESSION['user']['name'],
            'phone' => '',
            'region' => 'Centre',
            'city' => 'Yaoundé',
            'street' => 'Demo address — update me',
            'instructions' => 'Call before delivery.',
            'default' => true
        ]
    ];
}

$success = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'save') {
        $id = (int)($_POST['id'] ?? 0);
        $label = trim($_POST['label'] ?? 'Home');
        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $region = trim($_POST['region'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $street = trim($_POST['street'] ?? '');
        $instructions = trim($_POST['instructions'] ?? '');
        $isDefault = isset($_POST['is_default']);

        if ($fullName === '' || $phone === '' || $region === '' || $city === '' || $street === '') {
            $error = "Please complete all required address fields.";
        } else {
            if ($isDefault) {
                foreach ($_SESSION['addresses'] as &$address) {
                    $address['default'] = false;
                }
                unset($address);
            }

            $record = [
                'id' => $id ?: time(),
                'label' => $label ?: 'Address',
                'full_name' => $fullName,
                'phone' => $phone,
                'region' => $region,
                'city' => $city,
                'street' => $street,
                'instructions' => $instructions,
                'default' => $isDefault
            ];

            if ($id) {
                foreach ($_SESSION['addresses'] as $index => $address) {
                    if ($address['id'] === $id) {
                        $_SESSION['addresses'][$index] = $record;
                    }
                }
                $success = "Address updated successfully for this demo session.";
            } else {
                $_SESSION['addresses'][] = $record;
                $success = "New address added successfully for this demo session.";
            }

            /*
             * DATABASE INTEGRATION POINT:
             * Replace session storage with INSERT/UPDATE/DELETE queries using
             * the authenticated customer ID. Use prepared statements.
             */
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        $_SESSION['addresses'] = array_values(array_filter(
            $_SESSION['addresses'],
            fn($address) => $address['id'] !== $id
        ));
        $success = "Address removed for this demo session.";
    } elseif ($action === 'default') {
        $id = (int)($_POST['id'] ?? 0);
        foreach ($_SESSION['addresses'] as &$address) {
            $address['default'] = ($address['id'] === $id);
        }
        unset($address);
        $success = "Default address updated for this demo session.";
    }
}

$editId = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$editAddress = null;
foreach ($_SESSION['addresses'] as $address) {
    if ($address['id'] === $editId) {
        $editAddress = $address;
        break;
    }
}

$pageTitle = "My Addresses";
require_once "../includes/header.php";
?>
<section class="dashboard">
    <div class="container">
        <div class="page-heading">
            <div>
                <span class="eyebrow">Delivery information</span>
                <h1>My addresses</h1>
                <p>Save and manage the addresses you use for deliveries.</p>
            </div>
            <button class="btn btn-primary" type="button" data-modal-open="addressModal">+ Add address</button>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error" role="alert"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success" role="status"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div class="profile-layout">
            <aside class="side-card">
                <div class="avatar" aria-hidden="true"><?= strtoupper(substr($_SESSION['user']['name'], 0, 1)) ?></div>
                <p class="account-name"><?= htmlspecialchars($_SESSION['user']['name']) ?></p>
                <p class="account-role">Customer account</p>
                <nav class="account-nav" aria-label="Account navigation">
                    <a href="profile.php">Profile & account</a>
                    <a class="active" href="addresses.php">My addresses</a>
                    <a href="../auth/forgot-password.php">Reset password</a>
                    <a href="logout.php">Logout</a>
                </nav>
            </aside>

            <div class="content-card">
                <div class="section-header">
                    <div>
                        <span class="eyebrow">Saved locations</span>
                        <h2>Your delivery addresses</h2>
                    </div>
                    <span><?= count($_SESSION['addresses']) ?> saved</span>
                </div>

                <?php if (!$_SESSION['addresses']): ?>
                    <div class="empty-state">
                        <h3>No saved addresses</h3>
                        <p>Add an address so checkout can be faster next time.</p>
                        <button class="btn btn-primary" type="button" data-modal-open="addressModal">Add your first address</button>
                    </div>
                <?php else: ?>
                    <div class="grid-2">
                        <?php foreach ($_SESSION['addresses'] as $address): ?>
                            <article class="address-card <?= $address['default'] ? 'default' : '' ?>">
                                <?php if ($address['default']): ?><span class="default-badge">Default address</span><?php endif; ?>
                                <h3><?= htmlspecialchars($address['label']) ?></h3>
                                <p><strong><?= htmlspecialchars($address['full_name']) ?></strong></p>
                                <p><?= htmlspecialchars($address['phone']) ?></p>
                                <p><?= htmlspecialchars($address['street']) ?></p>
                                <p><?= htmlspecialchars($address['city']) ?>, <?= htmlspecialchars($address['region']) ?></p>
                                <?php if ($address['instructions']): ?>
                                    <p><small>Delivery note: <?= htmlspecialchars($address['instructions']) ?></small></p>
                                <?php endif; ?>

                                <div class="address-actions">
                                    <a class="btn btn-outline" href="addresses.php?edit=<?= $address['id'] ?>">Edit</a>
                                    <?php if (!$address['default']): ?>
                                        <form method="post" style="display:inline">
                                            <input type="hidden" name="action" value="default">
                                            <input type="hidden" name="id" value="<?= $address['id'] ?>">
                                            <button class="btn btn-outline" type="submit">Set default</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="post" style="display:inline">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $address['id'] ?>">
                                        <button class="btn btn-danger" type="submit" data-confirm="Delete this address?">Delete</button>
                                    </form>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<div id="addressModal" class="modal-backdrop <?= $editAddress ? 'open' : '' ?>" role="dialog" aria-modal="true" aria-labelledby="addressModalTitle">
    <div class="modal">
        <div class="modal-head">
            <div>
                <span class="eyebrow"><?= $editAddress ? 'Edit address' : 'New address' ?></span>
                <h2 id="addressModalTitle"><?= $editAddress ? 'Update address' : 'Add a delivery address' ?></h2>
            </div>
            <button class="icon-btn" type="button" data-modal-close aria-label="Close address form">×</button>
        </div>

        <form method="post" class="form-grid" style="margin-top:20px">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" value="<?= htmlspecialchars($editAddress['id'] ?? '') ?>">

            <div class="grid-2">
                <div class="form-group">
                    <label for="label">Address label</label>
                    <select id="label" name="label">
                       // <option <?= (($editAddress['label'] ?? '') === 'Home') ? 'selected' : '' ?>>Home</option>
                        <option <?= (($editAddress['label'] ?? '') === 'Work') ? 'selected' : '' ?>>Work</option>
                        <option <?= (($editAddress['label'] ?? '') === 'Other') ? 'selected' : '' ?>>Other</option>//
                    </select>
                </div>
                <div class="form-group">
                    <label for="address_full_name">Full name</label>
                    <input id="address_full_name" name="full_name" type="text" value="<?= htmlspecialchars($editAddress['full_name'] ?? $_SESSION['user']['name']) ?>" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="address_phone">Phone number</label>
                    <input id="address_phone" name="phone" type="tel" value="<?= htmlspecialchars($editAddress['phone'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="region">Region</label>
                    <input id="region" name="region" type="text" value="<?= htmlspecialchars($editAddress['region'] ?? '') ?>" placeholder="e.g. Centre" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="city">City</label>
                    <input id="city" name="city" type="text" value="<?= htmlspecialchars($editAddress['city'] ?? '') ?>" placeholder="e.g. Yaoundé" required>
                </div>
                <div class="form-group">
                    <label for="street">Street / address</label>
                    <input id="street" name="street" type="text" value="<?= htmlspecialchars($editAddress['street'] ?? '') ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label for="instructions">Delivery instructions</label>
                <textarea id="instructions" name="instructions" placeholder="Optional instructions for the delivery person."><?= htmlspecialchars($editAddress['instructions'] ?? '') ?></textarea>
            </div>

            <label class="check">
                <input type="checkbox" name="is_default" <?= !empty($editAddress['default']) ? 'checked' : '' ?>>
                <span>Set as my default delivery address</span>
            </label>

            <div class="form-row">
                <button class="btn btn-outline" type="button" data-modal-close>Cancel</button>
                <button class="btn btn-primary" type="submit"><?= $editAddress ? 'Save changes' : 'Add address' ?></button>
            </div>
        </form>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?>
