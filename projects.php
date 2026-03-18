<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

if ($user['role'] === 'admin' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project_id'], $_POST['status'])) {
    $projectId = (int) $_POST['project_id'];
    $status = $_POST['status'];
    $allowed = ['Pending', 'In Progress', 'Completed'];
    if (in_array($status, $allowed, true)) {
        $stmt = db()->prepare('UPDATE projects SET status = ? WHERE id = ?');
        $stmt->execute([$status, $projectId]);
        $_SESSION['flash_success'] = 'Project status updated.';
    }
    header('Location: projects.php');
    exit;
}

if ($user['role'] === 'admin') {
    $sql = 'SELECT p.id, p.title, p.status, p.description, c.name AS client_name FROM projects p JOIN clients c ON p.client_id = c.id ORDER BY p.id DESC';
    $projects = db()->query($sql)->fetchAll();
} else {
    $stmt = db()->prepare('SELECT p.id, p.title, p.status, p.description, c.name AS client_name FROM projects p JOIN clients c ON p.client_id = c.id WHERE c.user_id = ? ORDER BY p.id DESC');
    $stmt->execute([$user['id']]);
    $projects = $stmt->fetchAll();
}

$title = $user['role'] === 'admin' ? 'Projects' : 'My Projects';
require_once __DIR__ . '/partials/header.php';
?>
<div class="md:flex">
  <?php require __DIR__ . '/partials/sidebar.php'; ?>
  <div class="flex-1 min-h-screen">
    <?php require __DIR__ . '/partials/topbar.php'; ?>
    <main class="p-4 md:p-6">
      <?php if ($user['role'] === 'admin'): ?>
        <div class="flex justify-end mb-4">
          <a href="add-project.php" class="bg-blue-600 text-white px-4 py-2 rounded">Add Project</a>
        </div>
      <?php endif; ?>
      <div class="bg-white rounded border shadow-sm table-wrap">
        <table class="w-full text-sm">
          <thead class="bg-slate-50">
            <tr><th class="p-3 text-left">Title</th><th class="p-3 text-left">Client</th><th class="p-3 text-left">Description</th><th class="p-3 text-left">Status</th></tr>
          </thead>
          <tbody>
            <?php foreach ($projects as $project): ?>
              <tr class="border-t align-top">
                <td class="p-3"><?= esc($project['title']) ?></td>
                <td class="p-3"><?= esc($project['client_name']) ?></td>
                <td class="p-3"><?= esc($project['description']) ?></td>
                <td class="p-3">
                  <?php if ($user['role'] === 'admin'): ?>
                    <form method="post" class="flex gap-2">
                      <input type="hidden" name="project_id" value="<?= $project['id'] ?>">
                      <select name="status" class="border rounded px-2 py-1 text-sm">
                        <?php foreach (['Pending','In Progress','Completed'] as $state): ?>
                          <option value="<?= $state ?>" <?= $project['status'] === $state ? 'selected' : '' ?>><?= $state ?></option>
                        <?php endforeach; ?>
                      </select>
                      <button class="bg-slate-800 text-white px-2 rounded text-xs">Save</button>
                    </form>
                  <?php else: ?>
                    <?= esc($project['status']) ?>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$projects): ?><tr><td class="p-3" colspan="4">No projects found.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
