<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
refresh_session_user();

$user = current_user();
$title = 'Dashboard';

$adminStats = [
    'total_clients' => 0,
    'active_projects' => 0,
    'pending_payments' => 0,
];
$clientProjects = [];
$clientPayments = [];

if ($user['role'] === 'admin') {
    $adminStats['total_clients'] = (int) db()->query('SELECT COUNT(*) FROM clients')->fetchColumn();
    $stmt = db()->query("SELECT COUNT(*) FROM projects WHERE status IN ('Pending', 'In Progress')");
    $adminStats['active_projects'] = (int) $stmt->fetchColumn();
    $stmt = db()->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'unpaid'");
    $adminStats['pending_payments'] = (float) $stmt->fetchColumn();
} else {
    $stmt = db()->prepare("SELECT p.title, p.status FROM projects p JOIN clients c ON p.client_id = c.id WHERE c.user_id = ? ORDER BY p.id DESC");
    $stmt->execute([$user['id']]);
    $clientProjects = $stmt->fetchAll();

    $stmt = db()->prepare("SELECT p.title, pay.amount, pay.status, pay.date FROM payments pay JOIN projects p ON pay.project_id = p.id JOIN clients c ON p.client_id = c.id WHERE c.user_id = ? ORDER BY pay.date DESC");
    $stmt->execute([$user['id']]);
    $clientPayments = $stmt->fetchAll();
}

require_once __DIR__ . '/partials/header.php';
?>
<div class="md:flex">
  <?php require __DIR__ . '/partials/sidebar.php'; ?>
  <div class="flex-1 min-h-screen">
    <?php require __DIR__ . '/partials/topbar.php'; ?>
    <main class="p-4 md:p-6">
      <?php if ($user['role'] === 'admin'): ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="bg-white p-5 rounded shadow-sm border">
            <p class="text-sm text-slate-500">Total Clients</p>
            <h2 class="text-3xl font-bold"><?= $adminStats['total_clients'] ?></h2>
          </div>
          <div class="bg-white p-5 rounded shadow-sm border">
            <p class="text-sm text-slate-500">Active Projects</p>
            <h2 class="text-3xl font-bold"><?= $adminStats['active_projects'] ?></h2>
          </div>
          <div class="bg-white p-5 rounded shadow-sm border">
            <p class="text-sm text-slate-500">Pending Payments</p>
            <h2 class="text-3xl font-bold">$<?= number_format($adminStats['pending_payments'], 2) ?></h2>
          </div>
        </div>
      <?php else: ?>
        <section class="bg-white p-5 rounded shadow-sm border mb-5">
          <h2 class="font-semibold text-lg mb-3">Project Status</h2>
          <div class="table-wrap">
            <table class="w-full text-sm">
              <thead class="bg-slate-50">
                <tr><th class="text-left p-2">Project</th><th class="text-left p-2">Status</th></tr>
              </thead>
              <tbody>
                <?php foreach ($clientProjects as $project): ?>
                  <tr class="border-t"><td class="p-2"><?= esc($project['title']) ?></td><td class="p-2"><?= esc($project['status']) ?></td></tr>
                <?php endforeach; ?>
                <?php if (!$clientProjects): ?><tr><td class="p-2" colspan="2">No projects assigned yet.</td></tr><?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>
        <section class="bg-white p-5 rounded shadow-sm border">
          <h2 class="font-semibold text-lg mb-3">Payment History</h2>
          <div class="table-wrap">
            <table class="w-full text-sm">
              <thead class="bg-slate-50">
                <tr><th class="text-left p-2">Project</th><th class="text-left p-2">Amount</th><th class="text-left p-2">Status</th><th class="text-left p-2">Date</th></tr>
              </thead>
              <tbody>
                <?php foreach ($clientPayments as $payment): ?>
                  <tr class="border-t">
                    <td class="p-2"><?= esc($payment['title']) ?></td>
                    <td class="p-2">$<?= number_format((float) $payment['amount'], 2) ?></td>
                    <td class="p-2"><?= esc($payment['status']) ?></td>
                    <td class="p-2"><?= esc($payment['date']) ?></td>
                  </tr>
                <?php endforeach; ?>
                <?php if (!$clientPayments): ?><tr><td class="p-2" colspan="4">No payments available.</td></tr><?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>
      <?php endif; ?>
    </main>
  </div>
</div>
<?php require_once __DIR__ . '/partials/footer.php'; ?>
