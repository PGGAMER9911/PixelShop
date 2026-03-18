<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$user = current_user();

if ($user['role'] === 'admin' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_payment'])) {
    $projectId = (int) ($_POST['project_id'] ?? 0);
    $amount = (float) ($_POST['amount'] ?? 0);
    $status = ($_POST['status'] ?? 'unpaid') === 'paid' ? 'paid' : 'unpaid';
    $date = $_POST['date'] ?? date('Y-m-d');

    if ($projectId > 0 && $amount > 0) {
        $stmt = db()->prepare('INSERT INTO payments (project_id, amount, status, `date`) VALUES (?, ?, ?, ?)');
        $stmt->execute([$projectId, $amount, $status, $date]);
        $_SESSION['flash_success'] = 'Payment added successfully.';
    } else {
        $_SESSION['flash_error'] = 'Valid project and amount are required.';
    }
    header('Location: payments.php');
    exit;
}

if ($user['role'] === 'admin' && isset($_GET['toggle'])) {
    $id = (int) $_GET['toggle'];
    $stmt = db()->prepare("UPDATE payments SET status = CASE WHEN status = 'paid' THEN 'unpaid' ELSE 'paid' END WHERE id = ?");
    $stmt->execute([$id]);
    $_SESSION['flash_success'] = 'Payment status updated.';
    header('Location: payments.php');
    exit;
}

if ($user['role'] === 'admin') {
    $projects = db()->query('SELECT id, title FROM projects ORDER BY id DESC')->fetchAll();
    $sql = "SELECT pay.id, pay.amount, pay.status, pay.date, p.title AS project_title, c.name AS client_name
            FROM payments pay
            JOIN projects p ON pay.project_id = p.id
            JOIN clients c ON p.client_id = c.id
            ORDER BY pay.date DESC";
    $payments = db()->query($sql)->fetchAll();
} else {
    $projects = [];
    $stmt = db()->prepare("SELECT pay.id, pay.amount, pay.status, pay.date, p.title AS project_title, c.name AS client_name
                          FROM payments pay
                          JOIN projects p ON pay.project_id = p.id
                          JOIN clients c ON p.client_id = c.id
                          WHERE c.user_id = ?
                          ORDER BY pay.date DESC");
    $stmt->execute([$user['id']]);
    $payments = $stmt->fetchAll();
}

$title = $user['role'] === 'admin' ? 'Payments' : 'My Payments';
require_once __DIR__ . '/partials/header.php';
?>
<div class="md:flex">
  <?php require __DIR__ . '/partials/sidebar.php'; ?>
  <div class="flex-1 min-h-screen">
    <?php require __DIR__ . '/partials/topbar.php'; ?>
    <main class="p-4 md:p-6 space-y-4">
      <?php if ($user['role'] === 'admin'): ?>
      <section class="bg-white rounded border p-4">
        <h2 class="font-semibold mb-3">Add Payment</h2>
        <form method="post" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
          <input type="hidden" name="add_payment" value="1">
          <div>
            <label class="block text-sm mb-1">Project</label>
            <select name="project_id" class="w-full border rounded px-3 py-2" required>
              <option value="">Select</option>
              <?php foreach ($projects as $project): ?>
                <option value="<?= $project['id'] ?>"><?= esc($project['title']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="block text-sm mb-1">Amount</label>
            <input type="number" step="0.01" name="amount" class="w-full border rounded px-3 py-2" required>
          </div>
          <div>
            <label class="block text-sm mb-1">Status</label>
            <select name="status" class="w-full border rounded px-3 py-2">
              <option value="unpaid">Unpaid</option>
              <option value="paid">Paid</option>
            </select>
          </div>
          <div>
            <label class="block text-sm mb-1">Date</label>
            <input type="date" name="date" class="w-full border rounded px-3 py-2" value="<?= date('Y-m-d') ?>">
          </div>
          <button class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
        </form>
      </section>
      <?php endif; ?>
      <section class="bg-white rounded border table-wrap">
        <table class="w-full text-sm">
          <thead class="bg-slate-50">
            <tr><th class="p-3 text-left">Project</th><th class="p-3 text-left">Client</th><th class="p-3 text-left">Amount</th><th class="p-3 text-left">Status</th><th class="p-3 text-left">Date</th><th class="p-3 text-left">Action</th></tr>
          </thead>
          <tbody>
            <?php foreach ($payments as $payment): ?>
              <tr class="border-t">
                <td class="p-3"><?= esc($payment['project_title']) ?></td>
                <td class="p-3"><?= esc($payment['client_name']) ?></td>
                <td class="p-3">$<?= number_format((float) $payment['amount'], 2) ?></td>
                <td class="p-3"><?= esc($payment['status']) ?></td>
                <td class="p-3"><?= esc($payment['date']) ?></td>
                <td class="p-3">
                  <?php if ($user['role'] === 'admin'): ?>
                    <a class="text-blue-600" href="payments.php?toggle=<?= $payment['id'] ?>">Toggle Paid/Unpaid</a>
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (!$payments): ?><tr><td class="p-3" colspan="6">No payments found.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
