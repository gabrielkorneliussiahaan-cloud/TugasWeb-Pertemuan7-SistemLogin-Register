<?php
// ====================================================================
// register.php
// REQUIREMENT: form registrasi (nama, email, password) + validasi,
// sanitasi htmlspecialchars(), validasi email filter_var(), cek
// duplikasi email, password_hash(), simpan ke users.json
// ====================================================================

session_start();
require_once __DIR__ . '/includes/functions.php';

// Kalau sudah login, tidak perlu daftar lagi
if (isset($_SESSION['user_email'])) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$old = ['nama' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaRaw = $_POST['nama'] ?? '';
    $emailRaw = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // REQUIREMENT: sanitasi input dengan htmlspecialchars()
    $nama = sanitize($namaRaw);
    $email = sanitize($emailRaw);

    $old = ['nama' => $nama, 'email' => $email];

    // ---------- Validasi ----------
    if ($nama === '') {
        $errors[] = 'Nama tidak boleh kosong.';
    }

    // REQUIREMENT: validasi email dengan filter_var()
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    if (strlen($password) < 6) {
        $errors[] = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirmPassword) {
        $errors[] = 'Konfirmasi password tidak cocok.';
    }

    $users = readUsers();

    // REQUIREMENT: cek duplikasi email saat registrasi
    if (empty($errors) && findUserByEmail($email, $users) !== null) {
        $errors[] = 'Email ini sudah terdaftar. Coba login, atau pakai email lain.';
    }

    // ---------- Simpan kalau tidak ada error ----------
    if (empty($errors)) {
        $newUser = [
            'id' => count($users) > 0 ? max(array_column($users, 'id')) + 1 : 1,
            'nama' => $nama,
            'email' => $email,
            // REQUIREMENT: password di-hash dengan password_hash()
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'remember_token' => null,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $users[] = $newUser;

        if (saveUsers($users)) {
            setFlash('success', 'Registrasi berhasil! Silakan login.');
            header('Location: login.php');
            exit;
        }

        $errors[] = 'Gagal menyimpan data. Coba lagi.';
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daftar Akun — Sistem Login/Register</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
  <div class="auth-wrap">
    <div class="auth-card">
      <p class="auth-card__eyebrow">Tugas Rutin 7</p>
      <h1 class="auth-card__title">Buat Akun Baru</h1>

      <?php if (!empty($errors)): ?>
        <div class="alert alert--error">
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form method="post" action="register.php" class="auth-form" novalidate>
        <label class="auth-form__label" for="nama">Nama Lengkap</label>
        <input
          class="auth-form__input"
          type="text"
          id="nama"
          name="nama"
          value="<?= htmlspecialchars($old['nama'], ENT_QUOTES, 'UTF-8') ?>"
          placeholder="Nama kamu"
          required
        />

        <label class="auth-form__label" for="email">Email</label>
        <input
          class="auth-form__input"
          type="email"
          id="email"
          name="email"
          value="<?= htmlspecialchars($old['email'], ENT_QUOTES, 'UTF-8') ?>"
          placeholder="nama@email.com"
          required
        />

        <label class="auth-form__label" for="password">Password</label>
        <input
          class="auth-form__input"
          type="password"
          id="password"
          name="password"
          placeholder="Minimal 6 karakter"
          required
        />

        <label class="auth-form__label" for="confirm_password">Konfirmasi Password</label>
        <input
          class="auth-form__input"
          type="password"
          id="confirm_password"
          name="confirm_password"
          placeholder="Ulangi password"
          required
        />

        <button class="btn btn--primary" type="submit">Daftar</button>
      </form>

      <p class="auth-card__switch">
        Sudah punya akun? <a href="login.php">Login di sini</a>
      </p>
    </div>
  </div>
</body>
</html>
