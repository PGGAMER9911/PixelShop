<?php
require_once __DIR__ . '/config.php';

if (!empty($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = ($_POST['role'] ?? 'client') === 'admin' ? 'admin' : 'client';

    if ($name === '' || $email === '' || $password === '') {
        $_SESSION['flash_error'] = 'All fields are required.';
    } else {
        $exists = db()->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $exists->execute([$email]);
        if ($exists->fetch()) {
            $_SESSION['flash_error'] = 'Email already exists.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $insert = db()->prepare('INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)');
            $insert->execute([$name, $email, $hashed, $role]);

            if ($role === 'client') {
                $userId = (int) db()->lastInsertId();
                $clientInsert = db()->prepare('INSERT INTO clients (user_id, name, email, phone) VALUES (?, ?, ?, ?)');
                $clientInsert->execute([$userId, $name, $email, $_POST['phone'] ?? '']);
            }

            $_SESSION['flash_success'] = 'Registration successful. Please login.';
            header('Location: login.php');
            exit;
        }
    }
}

$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100">
  <div class="max-w-md mx-auto mt-16 bg-white p-6 rounded-lg shadow">
    <h1 class="text-2xl font-bold mb-4">Register</h1>
    <?php if ($error): ?><div class="bg-rose-100 text-rose-700 p-3 rounded mb-3"><?= esc($error) ?></div><?php endif; ?>
    <form method="post" class="space-y-3">
      <input name="name" class="w-full border rounded px-3 py-2" placeholder="Name" required>
      <input name="email" type="email" class="w-full border rounded px-3 py-2" placeholder="Email" required>
      <input name="phone" class="w-full border rounded px-3 py-2" placeholder="Phone (for client)">
      <input name="password" type="password" class="w-full border rounded px-3 py-2" placeholder="Password" required>
      <select name="role" class="w-full border rounded px-3 py-2" required>
        <option value="client">Client</option>
        <option value="admin">Admin</option>
      </select>
      <button class="w-full bg-blue-600 text-white py-2 rounded">Create Account</button>
      <p class="text-sm">Already registered? <a href="login.php" class="text-blue-600">Login</a></p>
    </form>
  </div>
</body>
</html>
