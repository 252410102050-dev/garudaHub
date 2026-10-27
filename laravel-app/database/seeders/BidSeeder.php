<?php

namespace Database\Seeders;

use App\Models\Bid;
use Illuminate\Database\Seeder;

class BidSeeder extends Seeder
{
    public function run(): void
    {
        $bids = [
            [1, 5, 8500000, 30, 'Pakai sensor dan ESP32, dijamin bunyi sebelum umpan habis. Kakap pun kalah cepat.', 'pending'],
            [1, 6, 7000000, 40, 'Tiga tahun mancing di empang, tiga tahun ngoding. Dua-duanya sabar.', 'pending'],
            [1, 7, 9500000, 25, 'Hasil copy-paste terbaik se-Indonesia, tapi jalan. Garansi perawatan 3 bulan.', 'pending'],
            [2, 5, 12000000, 45, 'Portofolio dipajang rapi, dijamin klien tertarik. Yang tertarik bayar, bukan cuma lihat.', 'pending'],
            [2, 8, 10000000, 50, 'Dikerjakan sambil begadang. Kalau ada bug, itu fitur kejutan.', 'pending'],
            [3, 6, 3000000, 14, 'Tiga konsep logo di Figma. Satu pasti dipilih, dua lainnya buat kenang-kenangan.', 'diterima'],
            [3, 7, 3500000, 10, 'Revisi tak terbatas selama dua minggu. Sampai klien pusing sendiri.', 'ditolak'],
            [4, 8, 9000000, 35, 'Grafik interaktif plus ekspor PDF. Mangkok kosong pun kami hitung.', 'pending'],
            [4, 5, 11000000, 30, 'Termasuk impor CSV otomatis, supaya nggak perlu ngetik ulang sambil nangis.', 'pending'],
            [4, 6, 7500000, 45, 'Fokus pada filter cabang dan jam rame. Kuah dan sambal di luar cakupan.', 'pending'],
            [5, 7, 6000000, 30, 'Bot berbasis aturan dengan jawaban sabar. Kata "ready" dijawab "ready, silakan DM".', 'pending'],
            [6, 8, 11000000, 40, 'Pelacak lengkap dengan notifikasi "bus ngetem". Ekspor rekap ke Excel biar kelihatan serius.', 'diterima'],
            [6, 5, 13000000, 35, 'Ada pemindai QR buat penumpang. Mahal, tapi busnya jadi kelihatan canggih.', 'ditolak'],
            [7, 6, 4500000, 15, 'Skrip impor dengan validasi data. Nama tetangga yang salah ketik ikut dibenerin.', 'diterima'],
            [7, 7, 5000000, 12, 'Termasuk dokumentasi dan pelatihan singkat cara menagih tanpa bikin sungkan.', 'ditolak'],
        ];

        foreach ($bids as [$project, $developer, $harga, $durasi, $proposal, $status]) {
            Bid::create([
                'project_id' => $project,
                'developer_id' => $developer,
                'harga' => $harga,
                'durasi' => $durasi,
                'proposal' => $proposal,
                'status' => $status,
            ]);
        }
    }
}
