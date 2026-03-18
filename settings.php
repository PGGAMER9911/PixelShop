<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
refresh_session_user();
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($name === '' || $email === '') {
        $_SESSION['flash_error'] = 'Name and email are required.';
    } else {
        $stmt = db()->prepare('SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1');
        $stmt->execute([$email, $user['id']]);
        if ($stmt->fetch()) {
            $_SESSION['flash_error'] = 'Email is already used by another account.';
        } else {
            $stmt = db()->prepare('UPDATE users SET name = ?, email = ? WHERE id = ?');
            $stmt->execute([$name, $email, $user['id']]);
            if ($user['role'] === 'client') {
                $sync = db()->prepare('UPDATE clients SET name = ?, email = ? WHERE user_id = ?');
                $sync->execute([$name, $email, $user['id']]);
            }
            $_SESSION['flash_success'] = 'Profile updated successfully.';
            header('Location: settings.php');
            exit;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';

    $stmt = db()->prepare('SELECT password FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$user['id']]);
    $row = $stmt->fetch();

    if (!$row || !password_verify($currentPassword, $row['password'])) {
        $_SESSION['flash_error'] = 'Current password is incorrect.';
    } elseif (strlen($newPassword) < 6) {
        $_SESSION['flash_error'] = 'New password must be at least 6 characters.';
    } else {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $update = db()->prepare('UPDATE users SET password = ? WHERE id = ?');
        $update->execute([$hash, $user['id']]);
        $_SESSION['flash_success'] = 'Password changed successfully.';
    }
    header('Location: settings.php');
    exit;
}

$stmt = db()->prepare('SELECT name, email FROM users WHERE id = ? LIMIT 1');
$stmt->execute([$user['id']]);
$profile = $stmt->fetch();

$title = 'Settings';
require_once __DIR__ . '/partials/header.php';
?>
<div class="md:flex">
  <?php require __DIR__ . '/partials/sidebar.php'; ?>
  <div class="flex-1 min-h-screen">
    <?php require __DIR__ . '/partials/topbar.php'; ?>
    <main class="p-4 md:p-6 grid grid-cols-1 lg:grid-cols-2 gap-4">
      <section class="bg-white rounded border p-5">
        <h2 class="font-semibold text-lg mb-3">Update Profile</h2>
        <form method="post" class="space-y-3">
          <input type="hidden" name="update_profile" value="1">
          <div>
            <label class="block text-sm mb-1">Name</label>
            <input name="name" class="w-full border rounded px-3 py-2" value="<?= esc($profile['name']) ?>" required>
          </div>
          <div>
            <label class="block text-sm mb-1">Email</label>
            <input name="email" type="email" class="w-full border rounded px-3 py-2" value="<?= esc($profile['email']) ?>" required>
          </div>
          <button class="bg-blue-600 text-white px-4 py-2 rounded">Save Profile</button>
        </form>
      </section>

      <section class="bg-white rounded border p-5">
        <h2 class="font-semibold text-lg mb-3">Change Password</h2>
        <form method="post" class="space-y-3">
          <input type="hidden" name="change_password" value="1">
          <div>
            <label class="block text-sm mb-1">Current Password</label>
            <input name="current_password" type="password" class="w-full border rounded px-3 py-2" required>
          </div>
          <div>
            <label class="block text-sm mb-1">New Password</label>
            <input name="new_password" type="password" class="w-full border rounded px-3 py-2" required>
          </div>
          <button class="bg-slate-900 text-white px-4 py-2 rounded">Change Password</button>
        </form>
      </section>
    </main>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
