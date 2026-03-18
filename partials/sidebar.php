<?php $role = $_SESSION['user']['role'] ?? 'client'; ?>
<aside class="w-64 bg-slate-900 text-slate-100 min-h-screen hidden md:block">
  <div class="p-5 border-b border-slate-700">
    <h2 class="font-bold text-lg">Freelancer Panel</h2>
    <p class="text-sm text-slate-400"><?= esc($_SESSION['user']['name'] ?? 'User') ?></p>
  </div>
  <nav class="p-4 space-y-2 text-sm">
    <a class="block px-3 py-2 rounded hover:bg-slate-700" href="dashboard.php">Dashboard</a>
    <?php if ($role === 'admin'): ?>
      <a class="block px-3 py-2 rounded hover:bg-slate-700" href="clients.php">Clients</a>
      <a class="block px-3 py-2 rounded hover:bg-slate-700" href="projects.php">Projects</a>
      <a class="block px-3 py-2 rounded hover:bg-slate-700" href="payments.php">Payments</a>
    <?php else: ?>
      <a class="block px-3 py-2 rounded hover:bg-slate-700" href="projects.php">My Projects</a>
      <a class="block px-3 py-2 rounded hover:bg-slate-700" href="payments.php">My Payments</a>
    <?php endif; ?>
    <a class="block px-3 py-2 rounded hover:bg-slate-700" href="messages.php">Messages</a>
    <a class="block px-3 py-2 rounded hover:bg-slate-700" href="settings.php">Settings</a>
    <a class="block px-3 py-2 rounded hover:bg-rose-700" href="logout.php">Logout</a>
  </nav>
</aside>
