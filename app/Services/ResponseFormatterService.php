<?php

namespace App\Services;

class ResponseFormatterService
{
    private const OPENING_VARIATIONS = [
        "Berikut informasi mahasiswa yang kamu cari ya ✨:",
        "Hai! 😊 Ini dia data mahasiswa yang kamu cari:",
        "Yay! Ketemu nih 🎓 Berikut rincian data mahasiswanya:",
        "Aku berhasil menemukan datanya! 😃 Berikut informasinya:",
        "Ini dia data mahasiswa yang cocok dengan pencarianmu ✨:",
    ];

    public function formatHasilPencarian(array $hasil): string
    {
        $jumlah = count($hasil);

        if ($jumlah > 10) {
            return "Aku menemukan banyak sekali data mahasiswa yang cocok dengan pencarianmu. Coba gunakan nama lengkap, NIM, prodi, atau angkatan agar pencariannya lebih spesifik ke 1 orang ya ✨";
        }

        if ($jumlah > 1) {
            return "Aku menemukan {$jumlah} data mahasiswa yang cocok dengan kata kunci tersebut. Coba gunakan nama lengkap, NIM, prodi, atau angkatan agar pencariannya lebih spesifik ke 1 orang ya ✨";
        }

        $m = $hasil[0];

        if ($m['is_format_nim'] ?? false) {
            $nim = $m['nim'];
            $prodi = $m['prodi'];
            $fakultas = $m['fakultas'];
            $angkatan = $m['angkatan'];

            $kesimpulan = "\n\nJadi, NIM **{$nim}** merupakan mahasiswa **{$prodi}** angkatan **{$angkatan}** di **{$fakultas}**, Universitas Negeri Gorontalo ✨";

            return "Berdasarkan format NIM **{$nim}**, berikut rincian datanya ✨:\n• **NIM**: {$nim}\n• **Prodi**: {$prodi}\n• **Fakultas**: {$fakultas}\n• **Angkatan**: {$angkatan}" . $kesimpulan;
        }

        $nama = $m['nama'] ?? 'Nama tidak tersedia';
        $prodi = $m['prodi'] ?? 'Prodi tidak tersedia';
        $fakultas = $m['fakul'] ?? $m['fakultas'] ?? 'Fakultas tidak tersedia';
        $angkatan = $m['angkatan'] ?? '-';
        $nim = $m['nim'] ?? 'Belum tercatat';

        $rawMinat = isset($m['minat_bakat']) ? trim((string) $m['minat_bakat']) : '';
        $adaMinat = $rawMinat !== '' && $rawMinat !== '-' && strtolower($rawMinat) !== 'null';
        $minatTeks = $adaMinat ? $rawMinat : '';

        $tambahanMinatList = $adaMinat ? "\n• **Minat & Bakat**: {$minatTeks}" : "";

        $kesimpulan = "\n\nJadi, **{$nama}** merupakan mahasiswa **{$prodi}** angkatan **{$angkatan}** di **{$fakultas}**, Universitas Negeri Gorontalo ✨";

        $pembuka = self::OPENING_VARIATIONS[array_rand(self::OPENING_VARIATIONS)];

        return "{$pembuka}\n• **Nama**: {$nama}\n• **Prodi**: {$prodi}\n• **Fakultas**: {$fakultas}\n• **Angkatan**: {$angkatan}\n• **NIM**: {$nim}{$tambahanMinatList}" . $kesimpulan;
    }

    public function formatSearchResults(array $hasil): string
    {
        return $this->formatHasilPencarian($hasil);
    }
}
