-- Garuda-Hub: skema + data dummy (M0). Semua data fiktif, domain example.com.
-- Password dummy hanya berupa hash bcrypt; kredensial demo ada di README.
CREATE DATABASE IF NOT EXISTS garuda_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE garuda_hub;

DROP TABLE IF EXISTS bids;
DROP TABLE IF EXISTS projects;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('klien','developer','admin') NOT NULL DEFAULT 'klien',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  client_id INT NOT NULL,
  judul VARCHAR(200) NOT NULL,
  deskripsi TEXT NOT NULL,
  kategori VARCHAR(50) NOT NULL,
  budget_min BIGINT NOT NULL DEFAULT 0,
  budget_max BIGINT NOT NULL DEFAULT 0,
  batas_lelang DATE NOT NULL,
  status ENUM('terbuka','ditutup') NOT NULL DEFAULT 'terbuka',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT chk_projects_budget CHECK (budget_min >= 0 AND budget_max >= budget_min),
  CONSTRAINT fk_projects_client FOREIGN KEY (client_id) REFERENCES users (id) ON DELETE CASCADE,
  INDEX idx_projects_status (status),
  INDEX idx_projects_client (client_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE bids (
  id INT AUTO_INCREMENT PRIMARY KEY,
  project_id INT NOT NULL,
  developer_id INT NOT NULL,
  harga BIGINT NOT NULL,
  durasi INT NOT NULL,
  proposal TEXT NOT NULL,
  is_winner TINYINT NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT chk_bids_nilai CHECK (harga > 0 AND durasi > 0),
  CONSTRAINT uq_bids_project_developer UNIQUE (project_id, developer_id),
  CONSTRAINT fk_bids_project FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE CASCADE,
  CONSTRAINT fk_bids_developer FOREIGN KEY (developer_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Pengguna (1 admin, 3 klien, 4 developer)
INSERT INTO users (id, nama, email, password, role) VALUES
(1, 'Admin Garuda',   'admin@example.com',  '$2y$12$Whu4zQHp93BcHk.eOQI3kO3gQ0ByvWBQ4cGOLQlljMHGhnEfgi5ue', 'admin'),
(2, 'Budi Santoso',   'budi@example.com',   '$2y$12$AW89bpkcNUdJpelLaz3CSuFoq24tXbzQFb7JHtT2gwK4HKOjKOIAS', 'klien'),
(3, 'Siti Rahayu',    'siti@example.com',   '$2y$12$AW89bpkcNUdJpelLaz3CSuFoq24tXbzQFb7JHtT2gwK4HKOjKOIAS', 'klien'),
(4, 'Agus Wijaya',    'agus@example.com',   '$2y$12$AW89bpkcNUdJpelLaz3CSuFoq24tXbzQFb7JHtT2gwK4HKOjKOIAS', 'klien'),
(5, 'Dewi Lestari',   'dewi@example.com',   '$2y$12$72QTYKig6d0/wW8itXUS5OCiccRGDcgsbo4Wlcaa6nxKrJls3pER.', 'developer'),
(6, 'Eko Prasetyo',   'eko@example.com',    '$2y$12$72QTYKig6d0/wW8itXUS5OCiccRGDcgsbo4Wlcaa6nxKrJls3pER.', 'developer'),
(7, 'Fitri Handayani','fitri@example.com',  '$2y$12$72QTYKig6d0/wW8itXUS5OCiccRGDcgsbo4Wlcaa6nxKrJls3pER.', 'developer'),
(8, 'Gilang Ramadhan','gilang@example.com', '$2y$12$72QTYKig6d0/wW8itXUS5OCiccRGDcgsbo4Wlcaa6nxKrJls3pER.', 'developer');

-- Proyek (8)
INSERT INTO projects (id, client_id, judul, deskripsi, kategori, budget_min, budget_max, batas_lelang, status) VALUES
(1, 2, 'Website Toko Batik Online', 'Membutuhkan toko online dengan katalog produk, keranjang, dan halaman kontak. Desain responsif.', 'Web', 5000000, 10000000, '2027-12-31', 'terbuka'),
(2, 2, 'Aplikasi Kasir Android', 'Aplikasi kasir sederhana untuk warung: catat transaksi, laporan harian, cetak struk via Bluetooth.', 'Mobile', 8000000, 15000000, '2027-11-30', 'terbuka'),
(3, 3, 'Redesain Logo dan UI Kit', 'Redesain identitas visual startup kuliner beserta UI kit untuk aplikasi pemesanan.', 'Desain', 2000000, 4000000, '2026-09-01', 'ditutup'),
(4, 3, 'Dashboard Analitik Penjualan', 'Dashboard grafik penjualan bulanan dari file CSV, dengan filter per cabang dan ekspor laporan.', 'Data', 6000000, 12000000, '2027-10-15', 'terbuka'),
(5, 4, 'Chatbot Layanan Pelanggan', 'Chatbot FAQ untuk situs sekolah, dapat dijawab dalam bahasa Indonesia.', 'AI', 4000000, 8000000, '2026-08-20', 'terbuka'),
(6, 4, 'Sistem Absensi Sekolah', 'Sistem absensi siswa berbasis web dengan rekap per kelas dan laporan bulanan untuk wali kelas.', 'Web', 7000000, 14000000, '2026-07-10', 'ditutup'),
(7, 2, 'Migrasi Database ke MySQL', 'Memindahkan data inventaris dari spreadsheet ke MySQL dan membuat skrip impor.', 'Data', 3000000, 6000000, '2026-06-30', 'ditutup'),
(8, 3, 'Landing Page Event Kampus', 'Satu halaman informasi acara seminar teknologi dengan formulir pendaftaran sederhana.', 'Web', 1500000, 3000000, '2027-09-01', 'terbuka');

-- Penawaran (15)
INSERT INTO bids (id, project_id, developer_id, harga, durasi, proposal, is_winner) VALUES
(1,  1, 5, 8500000,  30, 'Saya akan membangun dengan PHP dan MySQL, lengkap dengan panel admin produk.', 0),
(2,  1, 6, 7000000,  40, 'Pengalaman 3 tahun membuat toko online. Termasuk pelatihan penggunaan.', 0),
(3,  1, 7, 9500000,  25, 'Desain premium bernuansa batik, dukungan perawatan 3 bulan.', 0),
(4,  2, 5, 12000000, 45, 'Aplikasi native Kotlin, dukungan printer Bluetooth termasuk.', 0),
(5,  2, 8, 10000000, 50, 'Memakai Flutter agar bisa juga dipakai di iOS nanti.', 0),
(6,  3, 6, 3000000,  14, 'Tiga konsep logo dan UI kit lengkap di Figma.', 1),
(7,  3, 7, 3500000,  10, 'Revisi tak terbatas selama 2 minggu.', 0),
(8,  4, 8, 9000000,  35, 'Dashboard dengan Chart dan ekspor PDF.', 0),
(9,  4, 5, 11000000, 30, 'Termasuk modul impor CSV otomatis.', 0),
(10, 4, 6, 7500000,  45, 'Fokus pada grafik interaktif dan filter cabang.', 0),
(11, 5, 7, 6000000,  30, 'Chatbot berbasis aturan dengan basis pengetahuan FAQ sekolah.', 0),
(12, 6, 8, 11000000, 40, 'Sistem lengkap dengan rekap otomatis dan ekspor Excel.', 1),
(13, 6, 5, 13000000, 35, 'Termasuk aplikasi pemindai kode QR untuk absensi.', 0),
(14, 7, 6, 4500000,  15, 'Skrip impor dengan validasi data dan laporan galat.', 1),
(15, 7, 7, 5000000,  12, 'Termasuk dokumentasi skema dan pelatihan singkat.', 0);
