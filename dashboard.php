<?php
// ====================================================================
// dashboard.php
// REQUIREMENT: dashboard yang diproteksi (redirect kalau belum login)
// ====================================================================

require_once __DIR__ . '/includes/auth.php';
requireLogin();

$flash = getFlash();
$userNama = $_SESSION['user_nama'] ?? '';
$userEmail = $_SESSION['user_email'] ?? '';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard — Sistem Login/Register</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
  <div class="auth-wrap">
    <div class="auth-card auth-card--wide">
      <p class="auth-card__eyebrow">Tugas Rutin 7</p>
      <h1 class="auth-card__title">Dashboard</h1>

      <?php if ($flash): ?>
        <div class="alert alert--<?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?>">
          <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
        </div>
      <?php endif; ?>

      <div class="profile-box">
        <div class="profile-box__avatar">
          <?= htmlspecialchars(strtoupper(substr($userNama, 0, 1)), ENT_QUOTES, 'UTF-8') ?>
        </div>
        <div>
          <p class="profile-box__name">Halo, <?= htmlspecialchars($userNama, ENT_QUOTES, 'UTF-8') ?> 👋</p>
          <p class="profile-box__email"><?= htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
      </div>

      <p class="auth-card__desc">
        Ini halaman dashboard yang hanya bisa diakses kalau kamu sudah
        login. Coba buka <code>dashboard.php</code> langsung tanpa login
        (misalnya dari jendela browser lain / mode incognito) — kamu
        akan otomatis diarahkan ke halaman login.
      </p>

      <div class="btn-row">
        <a class="btn btn--secondary" href="edit_profile.php">Edit Profil</a>
        <a class="btn btn--danger" href="logout.php">Logout</a>
      </div>
    </div>
  </div>
</body>
</html>
