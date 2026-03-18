<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $stmt = db()->prepare('DELETE FROM clients WHERE id = ?');
    $stmt->execute([$id]);
    $_SESSION['flash_success'] = 'Client deleted successfully.';
    header('Location: clients.php');
    exit;
}

$clients = db()->query('SELECT id, name, email, phone FROM clients ORDER BY id DESC')->fetchAll();
$title = 'Clients';
require_once __DIR__ . '/partials/header.php';
?>
<div class="md:flex">
  <?php require __DIR__ . '/partials/sidebar.php'; ?>
  <div class="flex-1 min-h-screen">
    <?php require __DIR__ . '/partials/topbar.php'; ?>
    <main class="p-4 md:p-6">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Client Management</h2>
        <a href="add-client.php" class="bg-blue-600 text-white px-4 py-2 rounded">Add Client</a>
      </div>
      <div class="bg-white rounded border shadow-sm table-wrap">
        <table class="w-full text-sm">
          <thead class="bg-slate-50">
            <tr><th class="p-3 text-left">Name</th><th class="p-3 text-left">Email</th><th class="p-3 text-left">Phone</th><th class="p-3 text-left">Action</th></tr>
          </thead>
          <tbody>
            <?php foreach ($clients as $client): ?>
              <tr class="border-t">
                <td class="p-3"><?= esc($client['name']) ?></td>
                <td class="p-3"><?= esc($client['email']) ?></td>
                <td class="p-3"><?= esc($client['phone']) ?></td>
                <td class="p-3 space-x-3">
                  <a href="edit-client.php?id=<?= $client['id'] ?>" class="text-blue-600">Edit</a>
                  <a href="clients.php?delete=<?= $client['id'] ?>" class="text-rose-600" data-confirm="Delete this client?">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$clients): ?><tr><td class="p-3" colspan="4">No clients found.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
