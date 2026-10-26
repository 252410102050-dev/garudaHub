<?php

namespace Database\Seeders;

use App\Models\Bid;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Semua akun dummy: password = "password"
        $users = [
            [1, 'Pak RT Admin', 'admin@example.com', 'admin'],
            [2, 'Agus Kakap', 'agus@example.com', 'klien'],
            [3, 'Mie Ayam Mas Hambali', 'hambali@example.com', 'klien'],
            [4, 'Basnya Mendat Mendut', 'basnya@example.com', 'klien'],
            [5, 'Cak Ngoding Subuh', 'ngoding@example.com', 'developer'],
            [6, 'Mbak Stack Overflow', 'stackoverflow@example.com', 'developer'],
            [7, 'Bang Copas Handal', 'copas@example.com', 'developer'],
            [8, 'Kang Debug Sampai Pagi', 'debug@example.com', 'developer'],
        ];
        foreach ($users as [$id, $name, $email, $role]) {
            User::forceCreate([
                'id' => $id, 'name' => $name, 'email' => $email,
                'password' => 'password', 'role' => $role,
            ]);
        }

        $projects = [
            [1, 2, 'Mancing Mania Mantap', 'Sistem mendeteksi umpan sudah dimakan kakap, biar Agus bisa bawa makan ke rumah. Notifikasi harus bunyi sebelum ikannya kabur.', 'IoT', 5000000, 10000000, '2027-12-31', 'terbuka'],
            [2, 2, 'Buat Judol', 'Lagi butuh kerjaan, jadi bikin aplikasi untuk jual diri online (maksudnya memamerkan portofolio, jangan salah paham).', 'Web', 8000000, 15000000, '2027-11-30', 'terbuka'],
            [3, 3, 'Logo Mie Ayam Biar Kelihatan Mahal', 'Redesain logo gerobak supaya terlihat seperti startup kuliner, meski harganya tetap sepuluh ribu.', 'Desain', 2000000, 4000000, '2026-09-01', 'ditutup'],
            [4, 3, 'Dashboard Mangkok Terjual', 'Grafik penjualan mangkok per cabang, lengkap dengan filter jam rame dan ekspor laporan buat dipamerin ke mertua.', 'Data', 6000000, 12000000, '2027-10-15', 'terbuka'],
            [5, 4, 'Bot Penjawab "Kak, Ready?"', 'Chatbot yang otomatis menjawab pertanyaan "ready kak?" tanpa membuat pemilik bus ikut emosi.', 'AI', 4000000, 8000000, '2026-08-20', 'terbuka'],
            [6, 4, 'Pelacak Bus Anti Mendat-Mendut', 'Sistem pemantau posisi bus supaya penumpang tahu busnya sedang ngetem, mogok, atau memang sudah takdir telat.', 'Web', 7000000, 14000000, '2026-07-10', 'ditutup'],
            [7, 2, 'Pindahin Utang Warung ke MySQL', 'Memindahkan catatan utang tetangga dari buku tulis ke database, plus skrip impor dan pengingat halus.', 'Data', 3000000, 6000000, '2026-06-30', 'ditutup'],
            [8, 3, 'Landing Page Hajatan Anak Kos', 'Satu halaman info acara syukuran akhir bulan, dengan formulir pendaftaran bagi yang mau datang cuma buat makan.', 'Web', 1500000, 3000000, '2027-09-01', 'terbuka'],
        ];
        foreach ($projects as [$id, $client, $judul, $desk, $kat, $min, $max, $batas, $status]) {
            Project::forceCreate([
                'id' => $id, 'client_id' => $client, 'judul' => $judul,
                'deskripsi' => $desk, 'kategori' => $kat,
                'budget_min' => $min, 'budget_max' => $max,
                'batas_lelang' => $batas, 'status' => $status,
            ]);
        }

        $bids = [
            [1, 1, 5, 8500000, 30, 'Pakai sensor dan ESP32, dijamin bunyi sebelum umpan habis. Kakap pun kalah cepat.', 'pending'],
            [2, 1, 6, 7000000, 40, 'Tiga tahun mancing di empang, tiga tahun ngoding. Dua-duanya sabar.', 'pending'],
            [3, 1, 7, 9500000, 25, 'Hasil copy-paste terbaik se-Indonesia, tapi jalan. Garansi perawatan 3 bulan.', 'pending'],
            [4, 2, 5, 12000000, 45, 'Portofolio dipajang rapi, dijamin klien tertarik. Yang tertarik bayar, bukan cuma lihat.', 'pending'],
            [5, 2, 8, 10000000, 50, 'Dikerjakan sambil begadang. Kalau ada bug, itu fitur kejutan.', 'pending'],
            [6, 3, 6, 3000000, 14, 'Tiga konsep logo di Figma. Satu pasti dipilih, dua lainnya buat kenang-kenangan.', 'diterima'],
            [7, 3, 7, 3500000, 10, 'Revisi tak terbatas selama dua minggu. Sampai klien pusing sendiri.', 'ditolak'],
            [8, 4, 8, 9000000, 35, 'Grafik interaktif plus ekspor PDF. Mangkok kosong pun kami hitung.', 'pending'],
            [9, 4, 5, 11000000, 30, 'Termasuk impor CSV otomatis, supaya nggak perlu ngetik ulang sambil nangis.', 'pending'],
            [10, 4, 6, 7500000, 45, 'Fokus pada filter cabang dan jam rame. Kuah dan sambal di luar cakupan.', 'pending'],
            [11, 5, 7, 6000000, 30, 'Bot berbasis aturan dengan jawaban sabar. Kata "ready" dijawab "ready, silakan DM".', 'pending'],
            [12, 6, 8, 11000000, 40, 'Pelacak lengkap dengan notifikasi "bus ngetem". Ekspor rekap ke Excel biar kelihatan serius.', 'diterima'],
            [13, 6, 5, 13000000, 35, 'Ada pemindai QR buat penumpang. Mahal, tapi busnya jadi kelihatan canggih.', 'ditolak'],
            [14, 7, 6, 4500000, 15, 'Skrip impor dengan validasi data. Nama tetangga yang salah ketik ikut dibenerin.', 'diterima'],
            [15, 7, 7, 5000000, 12, 'Termasuk dokumentasi dan pelatihan singkat cara menagih tanpa bikin sungkan.', 'ditolak'],
        ];
        foreach ($bids as [$id, $project, $dev, $harga, $durasi, $proposal, $status]) {
            Bid::forceCreate([
                'id' => $id, 'project_id' => $project, 'developer_id' => $dev,
                'harga' => $harga, 'durasi' => $durasi,
                'proposal' => $proposal, 'status' => $status,
            ]);
        }
    }
}
