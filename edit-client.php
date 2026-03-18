<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);
$stmt = db()->prepare('SELECT * FROM clients WHERE id = ? LIMIT 1');
$stmt->execute([$id]);
$client = $stmt->fetch();

if (!$client) {
    $_SESSION['flash_error'] = 'Client not found.';
    header('Location: clients.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($name === '' || $email === '') {
        $_SESSION['flash_error'] = 'Name and email are required.';
    } else {
        $userStmt = db()->prepare("SELECT id FROM users WHERE email = ? AND role = 'client' LIMIT 1");
        $userStmt->execute([$email]);
        $linkedUser = $userStmt->fetch();

        $update = db()->prepare('UPDATE clients SET user_id = ?, name = ?, email = ?, phone = ? WHERE id = ?');
        $update->execute([$linkedUser['id'] ?? null, $name, $email, $phone, $id]);
        $_SESSION['flash_success'] = 'Client updated successfully.';
        header('Location: clients.php');
        exit;
    }
}

$title = 'Edit Client';
require_once __DIR__ . '/partials/header.php';
?>
<div class="md:flex">
  <?php require __DIR__ . '/partials/sidebar.php'; ?>
  <div class="flex-1 min-h-screen">
    <?php require __DIR__ . '/partials/topbar.php'; ?>
    <main class="p-4 md:p-6">
      <div class="bg-white rounded border p-6 max-w-xl">
        <form method="post" class="space-y-3">
          <div>
            <label class="block text-sm mb-1">Name</label>
            <input name="name" class="w-full border rounded px-3 py-2" value="<?= esc($client['name']) ?>" required>
          </div>
          <div>
            <label class="block text-sm mb-1">Email</label>
            <input type="email" name="email" class="w-full border rounded px-3 py-2" value="<?= esc($client['email']) ?>" required>
          </div>
          <div>
            <label class="block text-sm mb-1">Phone</label>
            <input name="phone" class="w-full border rounded px-3 py-2" value="<?= esc($client['phone']) ?>">
          </div>
          <div class="space-x-2">
            <button class="bg-blue-600 text-white px-4 py-2 rounded">Update Client</button>
            <a href="clients.php" class="px-4 py-2 border rounded">Cancel</a>
          </div>
        </form>
      </div>
    </main>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
