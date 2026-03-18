<header class="bg-white border-b border-slate-200">
  <div class="flex items-center justify-between px-4 md:px-6 py-4">
    <h1 class="font-semibold text-lg"><?= esc($title ?? 'Dashboard') ?></h1>
    <div class="text-sm text-slate-600">
      <?= esc($_SESSION['user']['email'] ?? '') ?>
    </div>
  </div>
</header>
