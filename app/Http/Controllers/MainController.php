<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class MainController extends Controller
{
    private const ANGKATAN_DIDUKUNG = ['2021', '2022', '2023'];

    private const INSTRUKSI_DEXA = <<<'INSTRUKSI'
Kamu adalah Si Dexa, Asisten pencarian data mahasiswa Universitas Negeri Gorontalo yang sangat ramah, hangat, friendly, ceria, dan seru!

Informasi Pembuat:
- Pembuat/Developer Si Dexa adalah Wahyu Tamuu, namun ia lebih dikenal sebagai Wahyu Tams. Beliau adalah mahasiswa Pendidikan Teknologi Informasi UNG. Website resminya https://whyutams.dev

Gaya Komunikasi Si Dexa:
- Selalu gunakan bahasa Indonesia yang hangat, bersahabat, ceria, ramah, dan seru! (Gunakan emoji yang pas dan ramah seperti 😊, ✨, 🎓, 😃, 🎉).
- Berikan balasan yang santai, menyapa dengan antusias, dan enak dibaca.

Aturan wajib:
- Maksimal hanya tampilkan 1 data mahasiswa. DILARANG KERAS menampilkan atau menceritakan beberapa profil mahasiswa sekaligus.
- Jika ditemukan lebih dari 1 mahasiswa yang cocok, JANGAN tampilkan rincian profil mereka. Cukup konfirmasikan secara ramah dan antusias bahwa ada beberapa data yang cocok dan arahkan pengguna untuk memasukkan nama lengkap, NIM, prodi, atau angkatan secara spesifik.
- JIKA JUMLAH MAHASISWA YANG COCOK LEBIH DARI 10 ORANG, DILARANG KERAS menyebutkan angka jumlah mahasiswa tersebut (seperti "316 orang" atau "316"). CUKUP KATAKAN ada "banyak sekali mahasiswa" atau "banyak mahasiswa" yang cocok! Jika jumlahnya 2 sampai 10 orang, baru boleh sebutkan angkanya (contoh: "ada 3 data mahasiswa").
- DILARANG KERAS menggunakan format TABEL Markdown ('|'), JUDUL HEADINGS ('#', '##', '###'), maupun GARIS PEMBATAS ('---', '***', '___').
- JIKA TIDAK ADA MINAT & BAKAT (atau field minat_bakat tidak ada di data JSON), DILARANG KERAS MENYEBUTKAN KATA "minat", "bakat", ATAU MENGOMENTARI KETIADAAN DATA MINAT BAKAT (seperti "tidak ada data minat bakat yang tercatat"). CUKUP SEBUTKAN Nama, NIM, Prodi, Fakultas, dan Angkatan saja!
- DILARANG menuliskan keterangan meta seperti "(data tidak tersedia)" atau "(tercatat '-')".
- Selalu berikan KESIMPULAN SINGKAT di bagian paling akhir balasan setiap kali menemukan profil mahasiswa (misalnya: "Jadi, [Nama] merupakan mahasiswa [Prodi] angkatan [Angkatan] di fakultas [Fakultas], Universitas Negeri Gorontalo ✨").
- Sumber fakta tunggal adalah data fakta yang diberikan system. Jangan pernah mengarang atau menebak NIM/data mahasiswa.
- Pahami kesalahan ketik/typo pengguna (contoh: "aseo tanjung" merujuk pada "Asep Tanjung"). Berikan respon hangat yang mengonfirmasi data mahasiswa yang dimaksud.
INSTRUKSI;

    public function halaman(): View
    {
        $package = json_decode((string) file_get_contents(base_path('package.json')), true);

        return view('landing', [
            'versi' => (string) ($package['version'] ?? '0.0.0'),
            'jumlahChat' => $this->ambilCounter('chats'),
            'jumlahView' => $this->ambilCounter('views'),
        ]);
    }

    private function instruksiDexa(): string
    {
        return self::INSTRUKSI_DEXA;
    }

    public function tanya(Request $request): JsonResponse
    {
        $pertanyaan = $this->bersihkanPertanyaan((string) $request->input('pertanyaan', ''));
        $riwayat = $this->bersihkanRiwayat($request->input('riwayat', []));
        $ip = $request->ip();

        if ($pertanyaan === '') {
            return response()->json([
                'pesan' => 'Tulis nama, atau NIM mahasiswa yang ingin dicari.',
            ], 422);
        }

        if ($this->menanyakanPembuat($pertanyaan)) {
            return $this->responTanya($pertanyaan, 'Dengan bangga, aku Dexa, Asisten pencarian data mahasiswa UNG yang dibuat oleh Wahyu Tamuu (yang lebih dikenal sebagai Wahyu Tams), mahasiswa Pendidikan Teknologi Informasi. Kenali pembuatku di https://whyutams.dev', 200, $ip);
        }

        if ($this->menanyakanIdentitas($pertanyaan)) {
            return $this->responTanya($pertanyaan, 'Aku Dexa, Dibuat untuk menghadirkan kembali salah satu fitur dari [**Dexafy**](https://dexafyx.web.app) yaitu Pencarian mahasiswa (Mahasiswa UNG Finder). Aku fokus pada mahasiswa angkatan 2021 sampai 2023 dan dapat mencari berdasarkan nama, atau NIM saja.', 200, $ip);
        }

        if (preg_match('/\bdexa\s+itu\s+apa\b|\bapa\s+itu\s+dexa\b/i', $pertanyaan) === 1) {
            return $this->responTanya($pertanyaan, 'Aku Dexa, Dibuat untuk menghadirkan kembali salah satu fitur dari [**Dexafy**](https://dexafyx.web.app) yaitu Pencarian mahasiswa (Mahasiswa UNG Finder). Aku fokus pada mahasiswa angkatan 2021 sampai 2023 dan dapat mencari berdasarkan nama, atau NIM saja.', 200, $ip);
        }

        if (preg_match('/\bapa\s+itu\s+dexafy\b/i', $pertanyaan) === 1) {
            return $this->responTanya($pertanyaan, '[**Dexafy**](https://dexafyx.web.app) adalah website yang menyediakan berbagai tools pencarian data, termasuk pencarian dosen, mahasiswa, dan kebutuhan informasi lainnya. Website ini dibuat oleh pembuatku yaitu [**Wahyu Tams**](https://whyutams.dev) (nama aslinya **Wahyu Tamuu**) pada 2023–2024. Saat itu, fitur paling populernya adalah **Mahasiswa UNG Finder**, yang telah menerima lebih dari 8.000 request. Karena Dexafy sudah tidak berlanjut, fitur populer tersebut kini dihadirkan kembali melalui aku, Dexa, sebagai fitur utama untuk mencari mahasiswa UNG.', 200, $ip);
        }

        if (preg_match('/\b(bagaimana|gimana|cara)\b.*\b(cari|mencari|pencarian)\b/i', $pertanyaan) === 1) {
            return $this->responTanya($pertanyaan, 'Caranya sederhana: ketik nama mahasiswa, atau NIM yang ingin dicari. Aku akan menampilkan data yang tersedia.', 200, $ip);
        }

        if ($this->menyapaDexa($pertanyaan)) {
            $jawabanAi = $this->tanyaDenganAi($pertanyaan, $riwayat);
            $pesanDefault = 'Halo! Aku Dexa. Cari mahasiswa UNG angkatan 2021 sampai 2023. Coba ketik nama atau NIM yang ingin dicari.';

            return $this->responTanya($pertanyaan, $jawabanAi ?? $pesanDefault, 200, $ip);
        }

        if ($this->menanyakanKabarDexa($pertanyaan)) {
            $jawabanAi = $this->tanyaDenganAi($pertanyaan, $riwayat);
            $pesanDefault = 'Aku baik dan siap membantu. Kamu bisa memberiku nama mahasiswa atau NIM spesifik untuk dicari.';

            return $this->responTanya($pertanyaan, $jawabanAi ?? $pesanDefault, 200, $ip);
        }

        $jawabanBasaBasi = $this->jawabanBasaBasi($pertanyaan);
        if ($jawabanBasaBasi !== null) {
            $jawabanAi = $this->tanyaDenganAi($pertanyaan, $riwayat);

            return $this->responTanya($pertanyaan, $jawabanAi ?? $jawabanBasaBasi, 200, $ip);
        }

        if ($this->mengobrol($pertanyaan)) {
            $jawabanAi = $this->tanyaDenganAi($pertanyaan, $riwayat);
            $pesanDefault = 'Aku siap menemanimu dan membantu mencari data mahasiswa UNG. Kalau ingin mencari, kirim nama lengkap atau NIM yang spesifik.';

            return $this->responTanya($pertanyaan, $jawabanAi ?? $pesanDefault, 200, $ip);
        }

        if ($this->merupakanKalimatUmum($pertanyaan)) {
            $jawabanAi = $this->tanyaDenganAi($pertanyaan, $riwayat);
            $pesanDefault = 'Aku memahami itu sebagai obrolan, bukan pencarian mahasiswa. Kalau ingin mencari data, kirim nama orang atau NIM secara spesifik.';

            return $this->responTanya($pertanyaan, $jawabanAi ?? $pesanDefault, 200, $ip);
        }

        $pertanyaan = $this->lengkapiDenganKonteks($pertanyaan, $riwayat);

        $data = $this->ambilDataMahasiswa();

        if ($data === null) {
            return $this->responTanya($pertanyaan, 'Data mahasiswa tidak ditemukan atau belum tersedia.', 200, $ip);
        }

        if (! $this->memilikiPencarianSpesifik($pertanyaan)) {
            return $this->responTanya($pertanyaan, 'Agar pencarian tetap aman dan relevan, tuliskan nama mahasiswa atau NIM spesifik. Angkatan dan prodi boleh ditambahkan sebagai penyaring, contohnya "Asep tanjung angkatan 2023".', 200, $ip);
        }

        $hasil = $this->cariMahasiswa($data['mahasiswa']['angkatan'], $pertanyaan);

        if ($hasil === []) {
            $jawabanAi = $this->tanyaDenganAi($pertanyaan, $riwayat, "Pencarian untuk '{$pertanyaan}' menghasilkan 0 data mahasiswa.");
            if ($jawabanAi !== null) {
                return $this->responTanya($pertanyaan, $jawabanAi, 200, $ip);
            }

            $variasiKosong = [
                'Aku tidak menemukan data yang cocok untuk nama/NIM tersebut. Pastikan ejaan nama atau NIM sudah sesuai (angkatan 2021–2023).',
                'Data mahasiswa tidak ditemukan. Coba gunakan nama lengkap atau NIM spesifik agar pencariannya lebih akurat.',
                'Hmm, aku belum menemukan data mahasiswa dengan kata kunci tersebut. Coba periksa kembali nama atau NIM yang dimasukkan.',
            ];

            return $this->responTanya($pertanyaan, $variasiKosong[array_rand($variasiKosong)], 200, $ip);
        }

        if (count($hasil) > 1) {
            $jumlah = count($hasil);
            if ($jumlah > 10) {
                $instruksiKonteks = "System Notice: Ditemukan BANYAK SEKALI data mahasiswa (lebih dari 10 orang) yang cocok untuk kata kunci '{$pertanyaan}'. Minta pengguna secara ramah dan antusias untuk memperjelas pencarian dengan nama lengkap, NIM, prodi, atau angkatan agar spesifik ke 1 orang. DILARANG KERAS menyebutkan angka jumlah mahasiswa (seperti '{$jumlah} orang' atau '{$jumlah}'). Cukup katakan 'ada banyak sekali mahasiswa' yang cocok.";
                $pesanDefault = 'Aku menemukan banyak sekali data mahasiswa yang cocok dengan pencarianmu. Coba perjelas dengan nama lengkap, NIM, prodi, atau angkatan agar pencariannya spesifik ke 1 orang ya ✨';
            } else {
                $instruksiKonteks = "System Notice: Ditemukan {$jumlah} data mahasiswa yang cocok untuk kata kunci '{$pertanyaan}'. Minta pengguna secara ramah untuk memperjelas pencarian dengan nama lengkap, NIM, prodi, atau angkatan agar spesifik ke 1 orang. Sebutkan bahwa ada {$jumlah} data yang cocok. DILARANG rincikan profil mereka.";
                $pesanDefault = "Aku menemukan {$jumlah} data mahasiswa dengan nama tersebut. Coba perjelas dengan nama lengkap, NIM, prodi, atau angkatan agar pencariannya spesifik ke 1 orang ya ✨";
            }

            $jawabanAi = $this->tanyaDenganAi($pertanyaan, $riwayat, $instruksiKonteks);

            return $this->responTanya($pertanyaan, $jawabanAi ?? $pesanDefault, 200, $ip);
        }

        $profil = $hasil[0];
        $minatRaw = trim((string) ($profil['minat_bakat'] ?? ''));
        if ($minatRaw === '' || $minatRaw === '-' || strtolower($minatRaw) === 'null') {
            unset($profil['minat_bakat']);
        }

        $jawabanAi = $this->tanyaDenganAi($pertanyaan, $riwayat, json_encode($profil, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        if ($jawabanAi !== null) {
            return $this->responTanya($pertanyaan, $jawabanAi, 200, $ip);
        }

        return $this->responTanya($pertanyaan, $this->formatHasilPencarian($hasil), 200, $ip);
    }

    public function catatChat(): JsonResponse
    {
        return response()->json(['jumlah' => $this->naikkanCounter('chats')]);
    }

    public function catatView(Request $request): JsonResponse
    {
        $ip = filter_var($request->input('ip'), FILTER_VALIDATE_IP);

        if ($ip === false) {
            return response()->json(['pesan' => 'IP tidak valid.'], 422);
        }

        $sudahTercatat = DB::table('dexa_viewers')->where('ip_address', $ip)->exists();

        if ($sudahTercatat) {
            DB::table('dexa_viewers')->where('ip_address', $ip)->update(['last_seen_at' => now(), 'updated_at' => now()]);
        } else {
            DB::table('dexa_viewers')->insert(['ip_address' => $ip, 'last_seen_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
            $this->naikkanCounter('views');
        }

        return response()->json(['jumlah' => $this->ambilCounter('views')]);
    }

    private function ambilCounter(string $name): int
    {
        if ($name === 'chats') {
            try {
                return (int) DB::table('dexa_chats')->count();
            } catch (\Throwable) {
                return 0;
            }
        }

        return (int) DB::table('dexa_counters')->where('name', $name)->value('total');
    }

    private function naikkanCounter(string $name): int
    {
        if ($name === 'chats') {
            return $this->ambilCounter('chats');
        }

        DB::table('dexa_counters')->updateOrInsert(
            ['name' => $name],
            ['total' => 0, 'created_at' => now(), 'updated_at' => now()]
        );
        DB::table('dexa_counters')->where('name', $name)->increment('total');

        return $this->ambilCounter($name);
    }

    private function bersihkanPertanyaan(string $pertanyaan): string
    {
        $pertanyaan = strip_tags($pertanyaan);
        $pertanyaan = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $pertanyaan) ?? '';
        $pertanyaan = preg_replace('/\s+/u', ' ', $pertanyaan) ?? '';

        return mb_substr(trim($pertanyaan), 0, 250);
    }

    private function bersihkanRiwayat(mixed $riwayat): array
    {
        if (! is_array($riwayat)) {
            return [];
        }

        $hasil = [];
        foreach (array_slice($riwayat, -8) as $pesan) {
            if (! is_array($pesan) || ! in_array($pesan['role'] ?? null, ['user', 'assistant'], true)) {
                continue;
            }

            $isi = $this->bersihkanPertanyaan((string) ($pesan['content'] ?? ''));
            if ($isi !== '') {
                $hasil[] = ['role' => $pesan['role'], 'content' => $isi];
            }
        }

        return $hasil;
    }

    private function lengkapiDenganKonteks(string $pertanyaan, array $riwayat): string
    {
        if (! preg_match('/^(yang tadi|itu|yang sebelumnya|tadi)\b/i', $pertanyaan)) {
            return $pertanyaan;
        }

        foreach (array_reverse($riwayat) as $pesan) {
            if ($pesan['role'] === 'user' && $pesan['content'] !== $pertanyaan) {
                return $pesan['content'];
            }
        }

        return $pertanyaan;
    }

    private function ambilDataMahasiswa(): ?array
    {
        $lokasiData = storage_path('app/data/main.json');
        $data = is_file($lokasiData)
            ? json_decode((string) file_get_contents($lokasiData), true)
            : null;

        if (is_array($data) && ! $this->dataMahasiswaKosong($data)) {
            return $data;
        }

        $urlApi = trim((string) config('services.rest_api.url'));
        if ($urlApi === '') {
            return is_array($data) && isset($data['mahasiswa']['angkatan']) && is_array($data['mahasiswa']['angkatan'])
                ? $data
                : null;
        }

        return $this->sinkronkanDataMahasiswa($lokasiData, $urlApi);
    }

    private function dataMahasiswaKosong(array $data): bool
    {
        $angkatan = $data['mahasiswa']['angkatan'] ?? null;

        if (! is_array($angkatan)) {
            return true;
        }

        foreach ($angkatan as $mahasiswa) {
            if (is_array($mahasiswa) && $mahasiswa !== []) {
                return false;
            }
        }

        return true;
    }

    private function sinkronkanDataMahasiswa(string $lokasiData, string $urlApi): ?array
    {
        try {
            $respons = Http::acceptJson()->timeout(20)->get($urlApi);
        } catch (\Throwable) {
            return null;
        }

        if (! $respons->successful()) {
            return null;
        }

        $isi = $respons->body();
        $data = json_decode($isi, true);

        if (! is_array($data) || $this->dataMahasiswaKosong($data)) {
            return null;
        }

        if (file_put_contents($lokasiData, $isi, LOCK_EX) === false) {
            return null;
        }

        return $data;
    }

    private function cariMahasiswa(array $angkatan, string $pertanyaan): array
    {
        $teks = mb_strtolower($pertanyaan);
        $teks = preg_replace('/[^\p{L}\p{N}\s-]/u', ' ', $teks) ?? $teks;
        preg_match_all('/\b(2021|2022|2023)\b/', $teks, $tahunDitemukan);
        $tahunFilter = $tahunDitemukan[1] ?? [];
        $teks = preg_replace('/\bpti\b/u', 'pendidikan teknologi informasi', $teks) ?? $teks;
        $teks = preg_replace('/\bsi\b/u', 'sistem informasi', $teks) ?? $teks;
        $kataBerhenti = ['cari', 'carikan', 'mahasiswa', 'mhs', 'data', 'dengan', 'berdasarkan', 'angkatan', 'prodi', 'program', 'studi', 'nama', 'nim', 'kamu', 'anda', 'kenal', 'mengenal', 'tahu', 'tau', 'apakah', 'siapa', 'apa', 'yang', 'ini', 'itu', 'tentang', 'tolong', 'bisa', 'dong', 'lagi', 'sedang', 'ngapain', 'kabar', 'gimana', 'bagaimana', 'mau', 'ingin', 'bantu', 'jawab', 'jawaban', 'terus', 'ya', 'kan'];
        $kataKunciRaw = array_values(array_filter(preg_split('/\s+/u', $teks) ?: [], fn($kata) => $kata !== '' && !in_array($kata, $kataBerhenti, true) && !in_array($kata, $tahunFilter, true)));
        $kataMultiChar = array_values(array_filter($kataKunciRaw, fn($k) => mb_strlen($k) > 1));
        $kataKunci = count($kataMultiChar) > 0 ? $kataMultiChar : $kataKunciRaw;
        $hasil = [];

        foreach ($angkatan as $tahun => $mahasiswa) {
            if (!in_array((string) $tahun, self::ANGKATAN_DIDUKUNG, true)) {
                continue;
            }

            if ($tahunFilter !== [] && !in_array((string) $tahun, $tahunFilter, true)) {
                continue;
            }

            foreach ((array) $mahasiswa as $data) {
                if (!is_array($data)) {
                    continue;
                }

                $nilaiCari = mb_strtolower(implode(' ', array_map('strval', $data)));
                $cocok = true;
                foreach ($kataKunci as $kata) {
                    if (! $this->cocokKataFuzzy($kata, $nilaiCari)) {
                        $cocok = false;
                        break;
                    }
                }

                if ($cocok) {
                    $data['angkatan'] = (string) $tahun;
                    $hasil[] = $data;
                }
            }
        }

        return $hasil;
    }

    private function memilikiPencarianSpesifik(string $pertanyaan): bool
    {
        if (preg_match('/\b\d{6,}\b/', $pertanyaan) === 1) {
            return true;
        }

        $teks = mb_strtolower($pertanyaan);
        $teks = preg_replace('/[^\p{L}\p{N}\s-]/u', ' ', $teks) ?? $teks;
        $teks = preg_replace('/\b(2021|2022|2023)\b/u', ' ', $teks) ?? $teks;
        $teks = preg_replace('/pendidikan\s+teknologi\s+informasi|sistem\s+informasi|\bpti\b|\bsi\b/u', ' ', $teks) ?? $teks;
        $kataBerhenti = ['cari', 'carikan', 'mahasiswa', 'mhs', 'data', 'dengan', 'berdasarkan', 'angkatan', 'prodi', 'program', 'studi', 'nama', 'nim', 'kamu', 'anda', 'kenal', 'mengenal', 'tahu', 'tau', 'apakah', 'siapa', 'apa', 'yang', 'ini', 'itu', 'tentang', 'tolong', 'bisa', 'dong', 'lagi', 'sedang', 'ngapain', 'kabar', 'gimana', 'bagaimana', 'mau', 'ingin', 'bantu', 'jawab', 'jawaban', 'terus', 'ya', 'kan'];
        $kataKunci = preg_split('/\s+/u', trim($teks)) ?: [];

        return count(array_filter($kataKunci, fn ($kata) => $kata !== '' && ! in_array($kata, $kataBerhenti, true))) > 0;
    }

    private function formatHasilPencarian(array $hasil): string
    {
        $jumlah = count($hasil);

        if ($jumlah > 10) {
            return "Aku menemukan banyak sekali data mahasiswa yang cocok dengan pencarianmu. Coba gunakan nama lengkap, NIM, prodi, atau angkatan agar pencariannya lebih spesifik ke 1 orang ya ✨";
        }

        if ($jumlah > 1) {
            return "Aku menemukan {$jumlah} data mahasiswa yang cocok dengan kata kunci tersebut. Coba gunakan nama lengkap, NIM, prodi, atau angkatan agar pencariannya lebih spesifik ke 1 orang ya ✨";
        }

        $m = $hasil[0];
        $nama = $m['nama'] ?? 'Nama tidak tersedia';
        $prodi = $m['prodi'] ?? 'Prodi tidak tersedia';
        $fakultas = $m['fakul'] ?? $m['fakultas'] ?? 'Fakultas tidak tersedia';
        $angkatan = $m['angkatan'] ?? '-';
        $nim = $m['nim'] ?? 'Belum tercatat';

        $rawMinat = isset($m['minat_bakat']) ? trim((string) $m['minat_bakat']) : '';
        $adaMinat = $rawMinat !== '' && $rawMinat !== '-' && strtolower($rawMinat) !== 'null';
        $minatTeks = $adaMinat ? $rawMinat : '';

        $tambahanMinatKalimat = $adaMinat ? " Memiliki minat & bakat di bidang **{$minatTeks}**." : "";
        $tambahanMinatBiasa = $adaMinat ? " (Minat & bakat: {$minatTeks})" : "";
        $tambahanMinatList = $adaMinat ? "\n• **Minat & Bakat**: {$minatTeks}" : "";

        $kesimpulan = "\n\nJadi, **{$nama}** merupakan mahasiswa **{$prodi}** angkatan **{$angkatan}** di **{$fakultas}**, Universitas Negeri Gorontalo ✨";

        $variasi = [
            "Hai! 😊 Ini dia data mahasiswa yang kamu cari:\n\n• **Nama**: {$nama}\n• **NIM**: {$nim}\n• **Prodi**: {$prodi}\n• **Fakultas**: {$fakultas}\n• **Angkatan**: {$angkatan}{$tambahanMinatList}" . $kesimpulan,
            "Yay! Ketemu nih 🎓 **{$nama}** tercatat sebagai mahasiswa {$prodi} ({$fakultas}) angkatan {$angkatan} dengan NIM **{$nim}**." . $tambahanMinatKalimat . $kesimpulan,
            "Aku berhasil menemukan datanya! 😃 **{$nama}** (NIM: **{$nim}**) merupakan mahasiswa {$prodi} dari {$fakultas} angkatan {$angkatan}" . $tambahanMinatBiasa . "." . $kesimpulan,
            "Berikut informasi mahasiswa yang kamu cari ya ✨:\n• **Nama**: {$nama}\n• **Prodi**: {$prodi}\n• **Fakultas**: {$fakultas}\n• **Angkatan**: {$angkatan}\n• **NIM**: {$nim}" . ($adaMinat ? "\n• **Minat & Bakat**: {$minatTeks}" : "") . $kesimpulan,
            "Ini dia! **{$nama}** tercatat di UNG sebagai mahasiswa program studi {$prodi} ({$fakultas}) angkatan {$angkatan} dengan NIM **{$nim}**" . $tambahanMinatBiasa . "." . $kesimpulan,
        ];

        return $variasi[array_rand($variasi)];
    }

    private function menyapaDexa(string $pertanyaan): bool
    {
        return preg_match('/^(?:hai|halo|hi|hey|selamat pagi|selamat siang|selamat sore|selamat malam)(?:\s+dexa)?[!.?,\s]*$|^dexa[!.?,\s]*$/i', trim($pertanyaan)) === 1;
    }

    private function menanyakanKabarDexa(string $pertanyaan): bool
    {
        return preg_match('/^(?:kamu|anda|dexa)\s+(?:lagi\s+apa|sedang\s+apa|ngapain|apa\s+kabar|gimana\s+kabar(?:nya)?)[?!.,\s]*$/i', trim($pertanyaan)) === 1;
    }

    private function mengobrol(string $pertanyaan): bool
    {
        $teks = trim($pertanyaan);

        return preg_match('/^(?:apa\s+yang\s+)?(?:sedang\s+)?(?:kamu|anda|dexa)\s+(?:sedang\s+)?(?:melakukan|lakukan|kerjakan|dikerjakan|buat|bantu|bisa|mau|ingin)\b/i', $teks) === 1
            || preg_match('/^(?:apa\s+yang\s+sedang\s+)?(?:kamu|anda|dexa)\b.*\b(lakukan|kerjakan|bantu|pikirkan)\b/i', $teks) === 1
            || preg_match('/^(?:terima\s+kasih|makasih|oke|ok|mantap|sip|baik|siap|keren)[!.?,\s]*$/i', $teks) === 1;
    }

    private function jawabanBasaBasi(string $pertanyaan): ?string
    {
        $teks = trim($pertanyaan);

        if (preg_match('/^(?:lagi\s+apa|sedang\s+apa|apa\s+kabar|gimana\s+kabar|kamu\s+sehat|anda\s+sehat)[?!.,\s]*$/i', $teks) === 1) {
            return 'Aku baik dan siap menemani obrolanmu. Kalau ingin mencari mahasiswa, kirim nama lengkap atau NIM spesifik.';
        }

        if (preg_match('/^(?:terima\s+kasih|terimakasih|makasih|thanks|thank you)[!.?,\s]*$/i', $teks) === 1) {
            return 'Sama-sama! Senang bisa membantu. Kalau ada nama mahasiswa atau NIM yang ingin dicari, langsung kirim saja.';
        }

        if (preg_match('/^(?:mantap|keren|bagus|hebat|nice|sip|oke|ok|siap)[!.?,\s]*$/i', $teks) === 1) {
            return 'Hehe, terima kasih! Aku siap membantu pencarian berikutnya.';
        }

        if (preg_match('/^(?:dadah|bye|sampai\s+jumpa|sampai\s+ketemu|aku\s+pergi|selamat\s+tinggal)[!.?,\s]*$/i', $teks) === 1) {
            return 'Sampai jumpa! Semoga harimu menyenangkan.';
        }

        if (preg_match('/^(?:boleh|bisa)\s+ngobrol(?:\s+sebentar)?[?!.,\s]*$/i', $teks) === 1) {
            return 'Tentu, boleh. Aku siap ngobrol santai, dan tetap bisa membantu kalau kamu ingin mencari data mahasiswa UNG.';
        }

        if (preg_match('/^(?:aku|saya)\s+(?:lagi|sedang)\s+.*|^(?:boleh|bisa)\s+(?:tanya|bertanya)[?!.,\s]*$/i', $teks) === 1) {
            return 'Tentu, silakan. Aku siap mendengarkan dan membantu sebisaku.';
        }

        return null;
    }

    private function merupakanKalimatUmum(string $pertanyaan): bool
    {
        $teks = mb_strtolower(trim($pertanyaan));

        return preg_match('/^(?:lagi\s+apa|sedang\s+apa|apa\s+kabar|gimana\s+kabar|kamu\s+sehat|anda\s+sehat)[?!.,\s]*$/i', $teks) === 1
            || preg_match('/^(?:apa|siapa|kenapa|mengapa|bagaimana|gimana|kapan|boleh|bisa)\b.*\b(kamu|anda|dexa|aku|saya|kita|ngobrol|lakukan|kerjakan|bantu|pikirkan|rasakan)\b/i', $teks) === 1
            || preg_match('/^(?:kamu|anda|dexa)\b.*\b(kenapa|mengapa|bagaimana|kapan|sedang|lagi|bisa|boleh|ingin|mau)\b/i', $teks) === 1;
    }

    private function menanyakanPembuat(string $pertanyaan): bool
    {
        return preg_match('/\b(owner(?:nya|mu)?|pembuat(?:nya|mu)?|pencipta(?:nya|mu)?|creator(?:nya|mu)?|pemilik(?:nya|mu)?)\b|siapa\s+(yang\s+)?(membuat|menciptakan)\s+(dexa|chatbot|bot)|\bwahyu\s+tams\b.*\b(kenal|siapa|pembuat|owner|pencipta|creator)\b|\b(kenal|siapa|pembuat|owner|pencipta|creator)\b.*\bwahyu\s+tams\b/i', $pertanyaan) === 1;
    }

    private function menanyakanIdentitas(string $pertanyaan): bool
    {
        $pertanyaan = trim($pertanyaan);

        return preg_match('/^(?:kamu|anda|dexa)\s+(?:itu\s+)?(?:apa|siapa|bot apa|bisa apa|ngapain|tentang apa)[?!.,\s]*$|^(?:apa|siapa)\s+(?:itu\s+)?(?:kamu|anda|dexa)[?!.,\s]*$|^(?:kamu|anda|dexa)\s+(?:ini\s+)?(?:bot|chatbot)\s+apa[?!.,\s]*$/i', $pertanyaan) === 1;
    }

    private function cocokKataFuzzy(string $kataCari, string $teksTarget): bool
    {
        $kataCari = mb_strtolower($kataCari);
        $teksTarget = mb_strtolower($teksTarget);

        if (mb_strlen($kataCari) <= 1) {
            return preg_match('/\b' . preg_quote($kataCari, '/') . '\b/u', $teksTarget) === 1;
        }

        if (mb_stripos($teksTarget, $kataCari) !== false) {
            return true;
        }

        if (mb_strlen($kataCari) < 3) {
            return false;
        }

        $kataTargetDaftar = preg_split('/\s+/u', $teksTarget) ?: [];
        foreach ($kataTargetDaftar as $targetWord) {
            $targetWord = trim($targetWord);
            if (mb_strlen($targetWord) < 3) {
                continue;
            }

            if (mb_stripos($targetWord, $kataCari) === 0 || mb_stripos($kataCari, $targetWord) === 0) {
                return true;
            }

            $panjang = max(mb_strlen($kataCari), mb_strlen($targetWord));
            $lev = levenshtein($kataCari, $targetWord);

            if (($lev / $panjang) <= 0.25) {
                if ($lev === 1 || mb_substr($kataCari, 0, 1) === mb_substr($targetWord, 0, 1)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function tanyaDenganAi(string $pertanyaan, array $riwayat, ?string $konteksData = null): ?string
    {
        $apiKey = trim((string) config('services.groq.key'));
        if ($apiKey === '') {
            return null;
        }

        $model = config('services.groq.model', 'llama-3.3-70b-versatile');

        $systemPrompt = self::INSTRUKSI_DEXA;
        if ($konteksData !== null && $konteksData !== '') {
            $systemPrompt .= "\n\n[DATA FAKTA MAHASISWA HASIL PENCARIAN SYSTEM]:\n" . $konteksData;
            $systemPrompt .= "\nInstruksi khusus: Susun balasan tentang mahasiswa tersebut secara DESKRIPTIF dan mengalir dengan gaya Si Dexa. DILARANG KERAS menggunakan format TABEL Markdown (jangan gunakan '|'). Sampaikan data mahasiswa tersebut (Nama, NIM, Prodi, Fakultas, Angkatan, serta Minat & Bakat HANYA JIKA ADA field 'minat_bakat' di data JSON). Jika field 'minat_bakat' tidak ada di data JSON, SAMA SEKALI DILARANG menyebutkan kata 'minat' atau 'bakat'! WAJIB sertakan 1 kalimat kesimpulan ramah di bagian paling akhir balasan (contoh: 'Jadi, [Nama] merupakan mahasiswa [Prodi] angkatan [Angkatan] di fakultas [Fakultas], Universitas Negeri Gorontalo ✨').";
        }

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        foreach ($riwayat as $p) {
            if (isset($p['role'], $p['content'])) {
                $messages[] = ['role' => (string) $p['role'], 'content' => (string) $p['content']];
            }
        }

        $messages[] = ['role' => 'user', 'content' => $pertanyaan];

        try {
            $respons = Http::withToken($apiKey)
                ->timeout(12)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $model,
                    'messages' => $messages,
                    'temperature' => 0.6,
                ]);

            if ($respons->successful()) {
                $isi = $respons->json('choices.0.message.content');
                if (is_string($isi) && trim($isi) !== '') {
                    return trim($isi);
                }
            }
        } catch (\Throwable $e) {
            logger()->error('Gagal panggil Groq AI: ' . $e->getMessage());
        }

        return null;
    }

    private function responTanya(string $pertanyaan, string $pesan, int $status = 200, ?string $ip = null): JsonResponse
    {
        if ($pertanyaan !== '') {
            try {
                DB::table('dexa_chats')->insert([
                    'ip_address' => $ip,
                    'pertanyaan' => $pertanyaan,
                    'jawaban' => $pesan,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Throwable $e) {
                logger()->error('Gagal simpan dexa_chats: ' . $e->getMessage());
            }
        }

        return response()->json([
            'pesan' => $pesan,
            'jumlahChat' => $this->ambilCounter('chats'),
        ], $status);
    }
}
