# Tugas Rutin 7 — Sistem Login/Register

Project PHP Native + JSON untuk Laragon.

## Requirement
- Register dengan validasi nama/email/password
- Validasi email dengan `filter_var()`
- Password dengan `password_hash()`
- Penyimpanan data di JSON
- Cek email duplikat
- Login + session
- Dashboard protected
- Redirect jika belum login
- Logout dengan `session_destroy()`
- Sanitasi output dengan `htmlspecialchars()`
- Pesan error/sukses
- Remember Me menggunakan cookie + token hash
- Edit profile
- Responsive mobile

## Instalasi Laragon
1. Extract folder `login-register` ke `C:\laragon\www\`
2. Jalankan Laragon dan klik **Start All**
3. Buka:
   `http://localhost/login-register/`
4. Atau jika Auto Virtual Hosts aktif:
   `http://login-register.test/`

## Catatan
File pengguna disimpan di `data/users.json`.
Jangan menghapus file tersebut; isi awalnya adalah `[]`.

## Pengujian
1. Register akun baru.
2. Pastikan password di JSON berbentuk hash `$2y$...`, bukan plaintext.
3. Coba register dengan email yang sama untuk menguji duplikasi.
4. Login.
5. Logout.
6. Coba membuka `dashboard.php` setelah logout; harus diarahkan ke login.
7. Uji Remember Me dengan mencentangnya saat login.
