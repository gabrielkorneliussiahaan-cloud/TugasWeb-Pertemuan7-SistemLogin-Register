<?php
// ====================================================================
// edit_profile.php (BONUS)
// Halaman terproteksi untuk mengubah nama, dan opsional ganti password
// (wajib isi password lama untuk verifikasi).
// ====================================================================

require_once __DIR__ . '/includes/auth.php';
requireLogin();

$userEmail = $_SESSION['user_email'];
$users = readUsers();
$currentUser = findUserByEmail($userEmail, $users);

$errors = [];
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitize($_POST['nama'] ?? '');
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmNewPassword = $_POST['confirm_new_password'] ?? '';

    if ($nama === '') {
        $errors[] = 'Nama tidak boleh kosong.';
    }

    $wantsPasswordChange = $newPassword !== '' || $confirmNewPassword !== '';

    if ($wantsPasswordChange) {
        if (!password_verify($currentPassword, $currentUser['password'])) {
            $errors[] = 'Password saat ini salah.';
        } elseif (strlen($newPassword) < 6) {
            $errors[] = 'Password baru minimal 6 karakter.';
        } elseif ($newPassword !== $confirmNewPassword) {
            $errors[] = 'Konfirmasi password baru tidak cocok.';
        }
    }

    if (empty($errors)) {
        $updateData = ['nama' => $nama];
        if ($wantsPasswordChange) {
            $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        if (updateUser($userEmail, $updateData)) {
            $_SESSION['user_nama'] = $nama;
            $currentUser['nama'] = $nama;
            $success = 'Profil berhasil diperbarui.';
        } else {
            $errors[] = 'Gagal menyimpan perubahan. Coba lagi.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Edit Profil — Sistem Login/Register</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
  <div class="auth-wrap">
    <div class="auth-card auth-card--wide">
      <p class="auth-card__eyebrow">Tugas Rutin 7 — Bonus</p>
      <h1 class="auth-card__title">Edit Profil</h1>

      <?php if ($success): ?>
        <div class="alert alert--success"><?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?></div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="alert alert--error">
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="edit_profile.php" class="auth-form" novalidate>
        <label class="auth-form__label" for="nama">Nama</label>
        <input
          class="auth-form__input"
          type="text"
          id="nama"
          name="nama"
          value="<?= htmlspecialchars($currentUser['nama'], ENT_QUOTES, 'UTF-8') ?>"
          required
        />

        <label class="auth-form__label">Email</label>
        <input class="auth-form__input" type="email" value="<?= htmlspecialchars($userEmail, ENT_QUOTES, 'UTF-8') ?>" disabled />

        <hr class="auth-form__divider" />
        <p class="auth-form__hint">Kosongkan bagian di bawah kalau tidak ingin ganti password.</p>

        <label class="auth-form__label" for="current_password">Password Saat Ini</label>
        <input class="auth-form__input" type="password" id="current_password" name="current_password" placeholder="Wajib diisi kalau ganti password" />

        <label class="auth-form__label" for="new_password">Password Baru</label>
        <input class="auth-form__input" type="password" id="new_password" name="new_password" placeholder="Minimal 6 karakter" />

        <label class="auth-form__label" for="confirm_new_password">Konfirmasi Password Baru</label>
        <input class="auth-form__input" type="password" id="confirm_new_password" name="confirm_new_password" />

        <button class="btn btn--primary" type="submit">Simpan Perubahan</button>
      </form>

      <p class="auth-card__switch">
        <a href="dashboard.php">&larr; Kembali ke Dashboard</a>
      </p>
    </div>
  </div>
</body>
</html>
