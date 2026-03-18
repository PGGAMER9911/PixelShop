<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiverId = (int) ($_POST['receiver_id'] ?? 0);
    $messageText = trim($_POST['message'] ?? '');

    if ($receiverId > 0 && $messageText !== '') {
        $stmt = db()->prepare('INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)');
        $stmt->execute([$user['id'], $receiverId, $messageText]);
        $_SESSION['flash_success'] = 'Message sent.';
    } else {
        $_SESSION['flash_error'] = 'Receiver and message are required.';
    }
    header('Location: messages.php');
    exit;
}

if ($user['role'] === 'admin') {
    $receivers = db()->query("SELECT id, name, email FROM users WHERE id != " . (int) $user['id'] . " ORDER BY role DESC, name ASC")->fetchAll();
} else {
    $receivers = db()->query("SELECT id, name, email FROM users WHERE role = 'admin' ORDER BY name ASC")->fetchAll();
}

$stmt = db()->prepare("SELECT m.message, m.timestamp, m.sender_id, m.receiver_id, s.name AS sender_name, r.name AS receiver_name
                      FROM messages m
                      JOIN users s ON m.sender_id = s.id
                      JOIN users r ON m.receiver_id = r.id
                      WHERE m.sender_id = ? OR m.receiver_id = ?
                      ORDER BY m.timestamp DESC");
$stmt->execute([$user['id'], $user['id']]);
$messages = $stmt->fetchAll();

$title = 'Messages';
require_once __DIR__ . '/partials/header.php';
?>
<div class="md:flex">
  <?php require __DIR__ . '/partials/sidebar.php'; ?>
  <div class="flex-1 min-h-screen">
    <?php require __DIR__ . '/partials/topbar.php'; ?>
    <main class="p-4 md:p-6 space-y-4">
      <section class="bg-white rounded border p-4 max-w-2xl">
        <h2 class="font-semibold mb-3">Send Message</h2>
        <form method="post" class="space-y-3">
          <div>
            <label class="block text-sm mb-1">To</label>
            <select name="receiver_id" class="w-full border rounded px-3 py-2" required>
              <option value="">Select receiver</option>
              <?php foreach ($receivers as $receiver): ?>
                <option value="<?= $receiver['id'] ?>"><?= esc($receiver['name']) ?> (<?= esc($receiver['email']) ?>)</option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-sm mb-1">Message</label>
            <textarea name="message" rows="4" class="w-full border rounded px-3 py-2" required></textarea>
          </div>
          <button class="bg-blue-600 text-white px-4 py-2 rounded">Send</button>
        </form>
      </section>

      <section class="bg-white rounded border p-4">
        <h2 class="font-semibold mb-3">Conversation History</h2>
        <div class="space-y-3">
          <?php foreach ($messages as $row): ?>
            <div class="border rounded p-3">
              <div class="text-xs text-slate-500 mb-1"><?= esc($row['sender_name']) ?> to <?= esc($row['receiver_name']) ?> • <?= esc($row['timestamp']) ?></div>
              <p><?= esc($row['message']) ?></p>
            </div>
          <?php endforeach; ?>
          <?php if (!$messages): ?><p class="text-sm text-slate-500">No messages yet.</p><?php endif; ?>
        </div>
      </section>
    </main>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
