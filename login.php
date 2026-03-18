<?php
require_once __DIR__ . '/config.php';

if (!empty($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = db()->prepare('SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        unset($user['password']);
        $_SESSION['user'] = $user;
        $_SESSION['flash_success'] = 'Welcome back, ' . $user['name'] . '!';
        header('Location: dashboard.php');
        exit;
    }

    $_SESSION['flash_error'] = 'Invalid email or password.';
}

$success = $_SESSION['flash_success'] ?? null;
$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100">
  <div class="max-w-md mx-auto mt-16 bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-4">Login</h1>
    <?php if ($success): ?><div class="bg-emerald-100 text-emerald-700 p-3 rounded mb-3"><?= esc($success) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="bg-rose-100 text-rose-700 p-3 rounded mb-3"><?= esc($error) ?></div><?php endif; ?>
    <form method="post" class="space-y-3">
      <input name="email" type="email" class="w-full border rounded px-3 py-2" placeholder="Email" required>
      <input name="password" type="password" class="w-full border rounded px-3 py-2" placeholder="Password" required>
      <button class="w-full bg-blue-600 text-white py-2 rounded">Login</button>
      <p class="text-sm">No account? <a href="register.php" class="text-blue-600">Register</a></p>
    </form>
  </div>
</body>
</html>
