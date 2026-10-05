# Tugas Rutin 7 — Sistem Login/Register (PHP Native)

Sistem otentikasi sederhana pakai PHP Native (tanpa framework, tanpa
database) — data user disimpan di `data/users.json`.

**Sudah diuji langsung** lewat PHP built-in server: alur register →
cek duplikat email → login (dengan remember me) → akses dashboard →
proteksi halaman tanpa login → login gagal (password salah) → logout,
semuanya berjalan sesuai harapan.

## Struktur folder

```
├── index.php           -> router: ke dashboard kalau login, ke login kalau belum
├── register.php        -> form + proses registrasi
├── login.php            -> form + proses login (+ checkbox remember me)
├── dashboard.php        -> halaman terproteksi, wajib login
├── edit_profile.php     -> bonus: ubah nama & password
├── logout.php           -> session_destroy() + hapus cookie remember-me
├── includes/
│   ├── functions.php    -> baca/tulis users.json, sanitasi, flash message
│   └── auth.php         -> requireLogin(), remember-me, isLoggedIn()
├── data/
│   └── users.json       -> "database" JSON (mulai kosong: [])
└── assets/
    └── style.css         -> styling terpusat, dipakai semua halaman
```

## Cara menjalankan

Butuh PHP terpasang di komputer kamu (cek dengan `php -v`; kalau
belum ada, download di php.net atau pakai XAMPP/Laragon).

```bash
php -S localhost:8000
```

Lalu buka `http://localhost:8000/register.php` di browser.

**Jangan** buka `index.php`/`login.php` langsung lewat `file://` di
browser (double-click file) — PHP butuh dijalankan lewat web server
(built-in server di atas, atau Apache/Nginx via XAMPP/Laragon) supaya
kode `<?php ... ?>`-nya benar-benar diproses.

## Ketentuan yang dipenuhi

| Ketentuan | Lokasi |
|---|---|
| Form registrasi (nama, email, password) + validasi | `register.php` |
| Form login | `login.php` |
| Sanitasi `htmlspecialchars()` | `includes/functions.php` → `sanitize()`, dipakai di semua form |
| Validasi email `filter_var()` | `register.php`, `login.php` |
| Password hashing `password_hash()` | `register.php` saat simpan user baru |
| Cek duplikasi email | `register.php`, pakai `findUserByEmail()` |
| Data disimpan di `users.json` | `includes/functions.php` → `readUsers()` / `saveUsers()` |
| Sistem login dengan session | `login.php`, `includes/auth.php` |
| Dashboard diproteksi (redirect kalau belum login) | `dashboard.php` → `requireLogin()` |
| Logout (`session_destroy()`) | `logout.php` |
| Pesan error & sukses jelas | Semua halaman, lewat `<div class="alert">` |

**Bonus (semua 3 dikerjakan):**
- **Remember Me** — checkbox di form login, pakai cookie + token yang
  di-hash (bukan token mentah) disimpan di `users.json`
- **Edit profile** — `edit_profile.php`, bisa ubah nama & password
  (password lama wajib diisi untuk verifikasi)
- **CSS rapi** — `assets/style.css`, desain kartu terpusat, konsisten
  di semua halaman, responsif untuk layar kecil

## Cara push ke GitHub

```bash
git init
git add .
git commit -m "Tugas Rutin 7: Sistem Login/Register PHP Native"
git branch -M main
git remote add origin https://github.com/username/nama-repo.git
git push -u origin main
```

**Catatan:** kalau repo-nya publik, `data/users.json` sebaiknya jangan
ikut berisi data asli (password sudah di-hash jadi relatif aman, tapi
tetap ada email orang). File ini sudah saya kosongkan lagi
(`[]`) sebelum dikirim.
