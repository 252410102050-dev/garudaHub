# Kebijakan Keamanan

Garuda-Hub adalah aplikasi **pembelajaran** yang ditulis dengan praktik PHP standar: prepared statement, escaping keluaran, token CSRF, hash password, serta pengecekan peran dan kepemilikan di sisi server. Aplikasi ini tidak melalui audit keamanan menyeluruh dan **tidak boleh dipasang di server publik**.

## Batasan yang diketahui

- Database bawaan memakai `root` tanpa password (khusus lokal).
- Tidak ada rate limit pada login atau pendaftaran.
- Tidak ada header `X-Frame-Options` atau `Content-Security-Policy`.
- Hanya data dummy; jangan masukkan data pribadi atau password asli.

## Melaporkan celah

Temuan keamanan boleh dilaporkan karena justru bagian dari tujuan belajar.

1. Jangan membuka isu publik berisi detail eksploit yang bisa dipakai menyerang pihak lain.
2. Laporkan lewat fitur *private vulnerability reporting* GitHub pada repositori ini (tab Security), atau hubungi pemilik repositori lewat profil GitHub.
3. Sertakan langkah reproduksi, halaman terkait, dan dampaknya.

Pengujian hanya boleh dilakukan pada instalasi milik Anda sendiri (lokal atau lingkungan belajar), bukan pada sistem orang lain.

Ini proyek belajar tanpa jaminan waktu tanggapan.
