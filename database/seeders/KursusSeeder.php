<?php
// database/seeders/KursusSeeder.php

namespace Database\Seeders;

use App\Models\Kursus;
use Illuminate\Database\Seeder;

class KursusSeeder extends Seeder
{
    public function run()
    {
        $kursus = [
            [
                'judul_kursus' => 'Computational Thinking: Cara Berpikir Logis untuk Mengatasi Masalah (Tingkat SD)',
                'deskripsi_kursus' => 'Pelatihan ini dirancang untuk memberikan pemahaman tentang konsep dasar berpikir komputasional (Computational Thinking) dan bagaimana mengaplikasikannya dalam pemecahan masalah secara sistematis.',
                'penerbit' => 'Tim Pengembang',
                'tingkat_kesulitan' => 'pemula',
                'durasi_jam' => 10,
                'status' => 'draft',
                'output_pelatihan' => "Peserta memahami konsep dasar berpikir komputasional dan manfaatnya dalam pemecahan masalah.\nPeserta mampu menerapkan decomposition, pattern recognition, abstraction, dan algorithm design dalam berbagai skenario.\nPeserta dapat mengimplementasikan konsep berpikir komputasional dalam memecahkan masalah kehidupan sehari-hari.",
                'persyaratan' => 'Tidak ada persyaratan khusus',
                'fasilitas' => 'Sertifikat, Materi digital, Forum diskusi',
                'kuota_peserta' => 50,
                'peserta_terdaftar' => 0
            ],
            [
                'judul_kursus' => 'Pengantar Mindset Digital 1: Mengubah Masa Depan Anda Dengan Pola Pikir Digital',
                'deskripsi_kursus' => 'Pelatihan ini membekali peserta untuk memiliki mindset digital yang diperlukan untuk menghadapi tantangan era transformasi digital.',
                'penerbit' => 'Tim Digital',
                'tingkat_kesulitan' => 'pemula',
                'durasi_jam' => 8,
                'status' => 'aktif',
                'output_pelatihan' => "Peserta memahami pentingnya mindset digital.\nPeserta mampu mengadopsi pola pikir digital dalam pekerjaan.\nPeserta dapat menerapkan konsep digital dalam kehidupan sehari-hari.",
                'persyaratan' => 'Minimal SMA/sederajat',
                'fasilitas' => 'Sertifikat, Video pembelajaran, Quiz',
                'kuota_peserta' => 100,
                'peserta_terdaftar' => 24
            ],
            [
                'judul_kursus' => 'Associate Data Scientist',
                'deskripsi_kursus' => 'Program pelatihan data scientist dengan sertifikasi standar kompetensi nasional untuk menjadi associate data scientist yang kompeten.',
                'penerbit' => 'Tim Data Science',
                'tingkat_kesulitan' => 'lanjutan',
                'durasi_jam' => 120,
                'status' => 'aktif',
                'output_pelatihan' => "Peserta mampu mengumpulkan, memvalidasi, dan membersihkan data.\nPeserta dapat menganalisis data dan membuat visualisasi.\nPeserta mampu membangun model machine learning untuk menyelesaikan masalah bisnis.",
                'persyaratan' => 'Lulusan D3/S1, Memahami dasar pemrograman',
                'fasilitas' => 'Sertifikat kompetensi, Project based learning, Mentoring 1-on-1',
                'kuota_peserta' => 30,
                'peserta_terdaftar' => 15
            ]
        ];

        foreach ($kursus as $data) {
            Kursus::create($data);
        }
    }
}