<?php
// ====================================================================
// login.php
// REQUIREMENT: form login, sistem login dengan session
// BONUS: checkbox "Remember Me"
// ====================================================================

require_once __DIR__ . '/includes/auth.php'; // sudah session_start() + attemptRememberLogin()

// Kalau sudah login (termasuk lewat remember-me), langsung ke dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$old = ['email' => ''];
$flash = getFlash();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailRaw = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $rememberMe = isset($_POST['remember_me']);

    $email = sanitize($emailRaw);
    $old = ['email' => $email];

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }
    if ($password === '') {
        $errors[] = 'Password tidak boleh kosong.';
    }

    if (empty($errors)) {
        $users = readUsers();
        $user = findUserByEmail($email, $users);

        if (!$user || !password_verify($password, $user['password'])) {
            $errors[] = 'Email atau password salah.';
        } else {
            // Login berhasil: regenerate session id (keamanan) lalu simpan data user
            session_regenerate_id(true);
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_nama'] = $user['nama'];

            if ($rememberMe) {
                setRememberCookie($user['email']);
            }

            header('Location: dashboard.php');
            exit;
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login — Sistem Login/Register</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
  <div class="auth-wrap">
    <div class="auth-card">
      <p class="auth-card__eyebrow">Tugas Rutin 7</p>
      <h1 class="auth-card__title">Masuk ke Akunmu</h1>

      <?php if ($flash): ?>
        <div class="alert alert--<?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?>">
          <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
        </div>
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

      <form method="post" action="login.php" class="auth-form" novalidate>
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
          placeholder="Password kamu"
          required
        />

        <label class="auth-form__checkbox">
          <input type="checkbox" name="remember_me" value="1" />
          Ingat saya selama 30 hari
        </label>

        <button class="btn btn--primary" type="submit">Login</button>
      </form>

      <p class="auth-card__switch">
        Belum punya akun? <a href="register.php">Daftar di sini</a>
      </p>
    </div>
  </div>
</body>
</html>
