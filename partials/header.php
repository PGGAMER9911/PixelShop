<?php
require_once __DIR__ . '/../config.php';
$currentUser = $_SESSION['user'] ?? null;
$success = $_SESSION['flash_success'] ?? null;
$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= esc($title ?? 'Freelancer Management System') ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="bg-slate-100 text-slate-800">
<?php if ($success): ?>
<div class="max-w-7xl mx-auto mt-3 px-4">
  <div class="bg-emerald-100 text-emerald-800 px-4 py-3 rounded border border-emerald-300"><?= esc($success) ?></div>
</div>
<?php endif; ?>
<?php if ($error): ?>
<div class="max-w-7xl mx-auto mt-3 px-4">
  <div class="bg-rose-100 text-rose-800 px-4 py-3 rounded border border-rose-300"><?= esc($error) ?></div>
</div>
<?php endif; ?>
