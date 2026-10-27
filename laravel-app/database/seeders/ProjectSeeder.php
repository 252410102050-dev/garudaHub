<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [2, 'Mancing Mania Mantap', 'Sistem mendeteksi umpan sudah dimakan kakap, biar Agus bisa bawa makan ke rumah. Notifikasi harus bunyi sebelum ikannya kabur.', 'IoT', 5000000, 10000000, '2027-12-31', 'terbuka'],
            [2, 'Buat Judol', 'Lagi butuh kerjaan, jadi bikin aplikasi untuk jual diri online (maksudnya memamerkan portofolio, jangan salah paham).', 'Web', 8000000, 15000000, '2027-11-30', 'terbuka'],
            [3, 'Logo Mie Ayam Biar Kelihatan Mahal', 'Redesain logo gerobak supaya terlihat seperti startup kuliner, meski harganya tetap sepuluh ribu.', 'Desain', 2000000, 4000000, '2026-09-01', 'ditutup'],
            [3, 'Dashboard Mangkok Terjual', 'Grafik penjualan mangkok per cabang, lengkap dengan filter jam rame dan ekspor laporan buat dipamerin ke mertua.', 'Data', 6000000, 12000000, '2027-10-15', 'terbuka'],
            [4, 'Bot Penjawab "Kak, Ready?"', 'Chatbot yang otomatis menjawab pertanyaan "ready kak?" tanpa membuat pemilik bus ikut emosi.', 'AI', 4000000, 8000000, '2026-08-20', 'terbuka'],
            [4, 'Pelacak Bus Anti Mendat-Mendut', 'Sistem pemantau posisi bus supaya penumpang tahu busnya sedang ngetem, mogok, atau memang sudah takdir telat.', 'Web', 7000000, 14000000, '2026-07-10', 'ditutup'],
            [2, 'Pindahin Utang Warung ke MySQL', 'Memindahkan catatan utang tetangga dari buku tulis ke database, plus skrip impor dan pengingat halus.', 'Data', 3000000, 6000000, '2026-06-30', 'ditutup'],
            [3, 'Landing Page Hajatan Anak Kos', 'Satu halaman info acara syukuran akhir bulan, dengan formulir pendaftaran bagi yang mau datang cuma buat makan.', 'Web', 1500000, 3000000, '2027-09-01', 'terbuka'],
        ];

        foreach ($projects as [$client, $judul, $deskripsi, $kategori, $min, $max, $batas, $status]) {
            Project::create([
                'client_id' => $client,
                'judul' => $judul,
                'deskripsi' => $deskripsi,
                'kategori' => $kategori,
                'budget_min' => $min,
                'budget_max' => $max,
                'batas_lelang' => $batas,
                'status' => $status,
            ]);
        }
    }
}
