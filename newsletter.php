<!-- This newsletter.php page is from Isaac Ferrer -->

<?php
// Part C: escaping helper
function esc_html(string $stringToChange): string {
    return htmlspecialchars($stringToChange, ENT_QUOTES, 'UTF-8');
}

$user = '';
$email = '';
$ERRORS = [];

// Part D: input handling with filter_input
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user  = filter_input(INPUT_POST, 'user', FILTER_UNSAFE_RAW);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);

    // Validation
    if ($user === null || trim($user) === '') {
        $ERRORS['user'] = "Username is required.";
    }
    if ($email === null || $email === false) {
        $ERRORS['email'] = "Please enter a valid email address.";
    }

    // --- Part E + F: PRG redirect ---
    if (empty($ERRORS)) {
        $qs = http_build_query([
            'ok'    => 1,
            'user'  => $user,
            'email' => $email
        ]);
        header('Location: newsletter.php?' . $qs);
        exit;
    }
}

// Sticky fields if not POST or invalid
if ($user === '' && isset($_GET['user'])) {
    $user = $_GET['user'];
}
if ($email === '' && isset($_GET['email'])) {
    $email = $_GET['email'];
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Newsletter</title>
    <?php require __DIR__ . '/includes/bootstrapcdnlinks.php'; ?>
</head>
<body class="p-3">
    <?php require __DIR__ . '/includes/navigation.php'; ?>
    <div class="container">
        <h1>Newsletter</h1>

        <?php if (isset($_GET['ok']) && $_GET['ok'] === '1'): ?>
            <div class="alert alert-success">
                Thanks <?= esc_html($_GET['user'] ?? '') ?>.
                Subscribed as <?= esc_html($_GET['email'] ?? '') ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($ERRORS)): ?>
            <div class="alert alert-danger">
                Please fix:
                <ul class="mb-0">
                    <?php foreach ($ERRORS as $msg): ?>
                        <li><?= esc_html($msg) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $ERRORS): ?>
            <form action="newsletter.php" method="post" class="mb-3">
                <label class="form-label">Username
                    <input class="form-control" type="text" name="user"
                           value="<?= esc_html($user) ?>">
                </label>
                <label class="form-label mt-2">Email
                    <input class="form-control" type="text" name="email"
                           value="<?= esc_html($email) ?>">
                </label>
                <button class="btn btn-primary mt-3" type="submit">Subscribe</button>
            </form>
        <?php else: ?>
            <h2>Raw POST</h2>
            <pre><?php var_dump($_POST); ?></pre>
        <?php endif; ?>
    </div>
</body>
</html>
