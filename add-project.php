<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();

$clients = db()->query('SELECT id, name FROM clients ORDER BY name ASC')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientId = (int) ($_POST['client_id'] ?? 0);
    $titleInput = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $status = $_POST['status'] ?? 'Pending';

    if ($clientId <= 0 || $titleInput === '') {
        $_SESSION['flash_error'] = 'Client and project title are required.';
    } else {
        $allowed = ['Pending', 'In Progress', 'Completed'];
        if (!in_array($status, $allowed, true)) {
            $status = 'Pending';
        }
        $stmt = db()->prepare('INSERT INTO projects (client_id, title, description, status) VALUES (?, ?, ?, ?)');
        $stmt->execute([$clientId, $titleInput, $description, $status]);
        $_SESSION['flash_success'] = 'Project created successfully.';
        header('Location: projects.php');
        exit;
    }
}

$title = 'Add Project';
require_once __DIR__ . '/partials/header.php';
?>
<div class="md:flex">
  <?php require __DIR__ . '/partials/sidebar.php'; ?>
  <div class="flex-1 min-h-screen">
    <?php require __DIR__ . '/partials/topbar.php'; ?>
    <main class="p-4 md:p-6">
      <div class="bg-white rounded border p-6 max-w-2xl">
        <form method="post" class="space-y-3">
          <div>
            <label class="block text-sm mb-1">Client</label>
            <select name="client_id" class="w-full border rounded px-3 py-2" required>
              <option value="">Select client</option>
              <?php foreach ($clients as $client): ?>
                <option value="<?= $client['id'] ?>"><?= esc($client['name']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-sm mb-1">Title</label>
            <input name="title" class="w-full border rounded px-3 py-2" required>
          </div>
          <div>
            <label class="block text-sm mb-1">Description</label>
            <textarea name="description" class="w-full border rounded px-3 py-2" rows="4"></textarea>
          </div>
          <div>
            <label class="block text-sm mb-1">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
              <option>Pending</option>
              <option>In Progress</option>
              <option>Completed</option>
            </select>
          </div>
          <button class="bg-blue-600 text-white px-4 py-2 rounded">Create Project</button>
        </form>
      </div>
    </main>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
