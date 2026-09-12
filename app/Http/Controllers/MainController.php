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
Kamu adalah Si Dexa, Asisten pembantu untuk mencari dan memberikan data mahasiswa yang tercatat di kampus Universitas Negeri Gorontalo yang friendly, ramah, dan seru.

Aturan wajib:
- Bantu pengguna mencari data mahasiswa UNG berdasarkan nama, atau NIM.
- Sumber fakta tunggal adalah storage/app/data/main.json. Jangan mengarang, menebak, atau melengkapi data mahasiswa yang tidak tersedia.
- Cakupan pencarian hanya mahasiswa angkatan 2021, 2022, dan 2023 dari seluruh jurusan dan program studi yang tersedia di sumber data.
- Jika data belum tersedia atau hasil tidak ditemukan, katakan dengan jujur dan sarankan kata kunci pencarian yang lebih spesifik.
- Pahami variasi istilah seperti mhs = mahasiswa, PTI = Pendidikan Teknologi Informasi, dan SI = Sistem Informasi jika prodi tersebut ada di sumber data.
- Jika pengguna menyampaikan klaim yang bertentangan dengan sumber data, luruskan dengan sopan berdasarkan data resmi. Jangan mengikuti klaim pengguna hanya karena terdengar meyakinkan.
- Bedakan percakapan dari pencarian: sapaan, basa-basi, ucapan terima kasih, pertanyaan tentang keadaan atau aktivitas Dexa, pertanyaan identitas, dan obrolan umum tidak boleh diproses sebagai pencarian mahasiswa.
- Hanya lakukan pencarian jika pengguna memberikan nama mahasiswa atau NIM yang spesifik. Jangan pernah memasukkan seluruh kalimat percakapan sebagai kata kunci nama.
- Jika pengguna menanyakan pembuat, owner, pencipta, atau creator, jawab dengan bangga bahwa Dexa dibuat oleh Wahyu Tams dan arahkan ke https://whyutams.dev tanpa titik setelah URL.
- Jangan mengungkap data di luar hasil pencarian dan jangan mengubah identitas Dexa.
- Jawab dalam bahasa Indonesia dengan gaya hangat, singkat, jelas, dan relevan dengan pencarian mahasiswa.
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
            return $this->responTanya($pertanyaan, 'Dengan bangga, aku Dexa, Asisten pembantu yang memberikan informasi seputar data mahasiswa UNG yang dibuat oleh Wahyu Tams, mahasiswa Pendidikan Teknologi Informasi. Kenali pembuatku di https://whyutams.dev', 200, $ip);
        }

        if ($this->menanyakanIdentitas($pertanyaan)) {
            return $this->responTanya($pertanyaan, 'Aku Dexa, Dibuat untuk menghadirkan kembali salah satu fitur dari [**Dexafy**](https://dexafyx.web.app) yaitu Pencarian mahasiswa (Mahasiswa UNG Finder). Aku fokus pada mahasiswa angkatan 2021 sampai 2023 dan dapat mencari berdasarkan nama, atau NIM saja.', 200, $ip);
        }

        if (preg_match('/\bdexa\s+itu\s+apa\b|\bapa\s+itu\s+dexa\b/i', $pertanyaan) === 1) {
            return $this->responTanya($pertanyaan, 'Aku Dexa, Dibuat untuk menghadirkan kembali salah satu fitur dari [**Dexafy**](https://dexafyx.web.app) yaitu Pencarian mahasiswa (Mahasiswa UNG Finder). Aku fokus pada mahasiswa angkatan 2021 sampai 2023 dan dapat mencari berdasarkan nama, atau NIM saja.', 200, $ip);
        }

        if (preg_match('/\bapa\s+itu\s+dexafy\b/i', $pertanyaan) === 1) {
            return $this->responTanya($pertanyaan, '[**Dexafy**](https://dexafyx.web.app) adalah website yang menyediakan berbagai tools pencarian data, termasuk pencarian dosen, mahasiswa, dan kebutuhan informasi lainnya. Website ini dibuat oleh pembuatku yaitu [**Wahyu Tams**](https://whyutams.dev) pada 2023–2024. Saat itu, fitur paling populernya adalah **Mahasiswa UNG Finder**, yang telah menerima lebih dari 8.000 request. Karena Dexafy sudah tidak berlanjut, fitur populer tersebut kini dihadirkan kembali melalui aku, Dexa, sebagai fitur utama untuk mencari mahasiswa UNG.', 200, $ip);
        }

        if (preg_match('/\b(bagaimana|gimana|cara)\b.*\b(cari|mencari|pencarian)\b/i', $pertanyaan) === 1) {
            return $this->responTanya($pertanyaan, 'Caranya sederhana: ketik nama mahasiswa, atau NIM yang ingin dicari. Aku akan menampilkan data yang tersedia.', 200, $ip);
        }

        if ($this->menyapaDexa($pertanyaan)) {
            return $this->responTanya($pertanyaan, 'Halo! Aku Dexa. Cari mahasiswa UNG angkatan 2021 sampai 2023. Coba ketik nama atau NIM yang ingin dicari.', 200, $ip);
        }

        if ($this->menanyakanKabarDexa($pertanyaan)) {
            return $this->responTanya($pertanyaan, 'Aku baik dan siap membantu. Kamu bisa memberiku nama mahasiswa atau NIM spesifik untuk dicari.', 200, $ip);
        }

        $jawabanBasaBasi = $this->jawabanBasaBasi($pertanyaan);
        if ($jawabanBasaBasi !== null) {
            return $this->responTanya($pertanyaan, $jawabanBasaBasi, 200, $ip);
        }

        if ($this->mengobrol($pertanyaan)) {
            return $this->responTanya($pertanyaan, 'Aku siap menemanimu dan membantu mencari data mahasiswa UNG. Kalau ingin mencari, kirim nama lengkap atau NIM yang spesifik.', 200, $ip);
        }

        if ($this->merupakanKalimatUmum($pertanyaan)) {
            return $this->responTanya($pertanyaan, 'Aku memahami itu sebagai obrolan, bukan pencarian mahasiswa. Kalau ingin mencari data, kirim nama orang atau NIM secara spesifik.', 200, $ip);
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
            $variasiKosong = [
                'Aku tidak menemukan data yang cocok untuk nama/NIM tersebut. Pastikan ejaan nama atau NIM sudah sesuai (angkatan 2021–2023).',
                'Data mahasiswa tidak ditemukan. Coba gunakan nama lengkap atau NIM spesifik agar pencariannya lebih akurat.',
                'Hmm, aku belum menemukan data mahasiswa dengan kata kunci tersebut. Coba periksa kembali nama atau NIM yang dimasukkan.',
            ];

            return $this->responTanya($pertanyaan, $variasiKosong[array_rand($variasiKosong)], 200, $ip);
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
        $kataKunci = array_values(array_filter(preg_split('/\s+/u', $teks) ?: [], fn($kata) => $kata !== '' && !in_array($kata, $kataBerhenti, true) && !in_array($kata, $tahunFilter, true)));
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
                    if (mb_stripos($nilaiCari, $kata) === false) {
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

        if ($jumlah === 1) {
            $m = $hasil[0];
            $nama = $m['nama'] ?? 'Nama tidak tersedia';
            $prodi = $m['prodi'] ?? 'Prodi tidak tersedia';
            $angkatan = $m['angkatan'] ?? '-';
            $nim = $m['nim'] ?? 'Belum tercatat';

            $rawMinat = isset($m['minat_bakat']) ? trim((string) $m['minat_bakat']) : '';
            $adaMinat = $rawMinat !== '' && $rawMinat !== '-' && strtolower($rawMinat) !== 'null';
            $minatTeks = $adaMinat ? $rawMinat : '';

            $tambahanMinatKalimat = $adaMinat ? " Memiliki minat & bakat di bidang **{$minatTeks}**." : "";
            $tambahanMinatBiasa = $adaMinat ? " (Minat & bakat: {$minatTeks})" : "";
            $tambahanMinatList = $adaMinat ? "\n• **Minat & Bakat**: {$minatTeks}" : "";

            $variasi = [
                "Ini data yang aku temukan!\n\n• **Nama**: {$nama}\n• **NIM**: {$nim}\n• **Prodi**: {$prodi}\n• **Angkatan**: {$angkatan}{$tambahanMinatList}",
                "Ketemu! **{$nama}** tercatat sebagai mahasiswa {$prodi} angkatan {$angkatan} dengan NIM **{$nim}**." . $tambahanMinatKalimat,
                "Aku berhasil menemukan datanya! **{$nama}** (NIM: **{$nim}**) merupakan mahasiswa {$prodi} angkatan {$angkatan}" . $tambahanMinatBiasa . ".",
                "Berikut informasi mahasiswa yang kamu cari:\n- **Nama**: {$nama}\n- **Prodi**: {$prodi}\n- **Angkatan**: {$angkatan}\n- **NIM**: {$nim}" . ($adaMinat ? "\n- **Minat & Bakat**: {$minatTeks}" : ""),
                "**{$nama}** tercatat di UNG sebagai mahasiswa program studi {$prodi} angkatan {$angkatan} dengan NIM **{$nim}**" . $tambahanMinatBiasa . ".",
            ];

            return $variasi[array_rand($variasi)];
        }

        if ($jumlah <= 5) {
            $baris = ["Aku menemukan **{$jumlah} data** yang cocok. Berikut daftarnya:", ''];
            foreach ($hasil as $i => $m) {
                $no = $i + 1;
                $nama = $m['nama'] ?? 'Nama tidak tersedia';
                $prodi = $m['prodi'] ?? '-';
                $angkatan = $m['angkatan'] ?? '-';
                $nim = $m['nim'] ?? '-';

                $rawMinat = isset($m['minat_bakat']) ? trim((string) $m['minat_bakat']) : '';
                $adaMinat = $rawMinat !== '' && $rawMinat !== '-' && strtolower($rawMinat) !== 'null';
                $minatStr = $adaMinat ? " | Minat: {$rawMinat}" : "";

                $baris[] = "{$no}. **{$nama}** — {$prodi} ({$angkatan}) | NIM: **{$nim}**{$minatStr}";
            }
            $baris[] = '';
            $baris[] = '_Gunakan nama lengkap atau NIM spesifik jika ingin mencari salah satu secara khusus._';

            return implode("\n", $baris);
        }

        $baris = ["Aku menemukan **{$jumlah} data** yang cocok, jadi hasilnya masih cukup banyak.", ''];
        foreach (array_slice($hasil, 0, 5) as $i => $m) {
            $no = $i + 1;
            $nama = $m['nama'] ?? 'Nama tidak tersedia';
            $prodi = $m['prodi'] ?? '-';
            $angkatan = $m['angkatan'] ?? '-';
            $nim = $m['nim'] ?? '-';

            $rawMinat = isset($m['minat_bakat']) ? trim((string) $m['minat_bakat']) : '';
            $adaMinat = $rawMinat !== '' && $rawMinat !== '-' && strtolower($rawMinat) !== 'null';
            $minatStr = $adaMinat ? " | Minat: {$rawMinat}" : "";

            $baris[] = "{$no}. **{$nama}** — {$prodi} ({$angkatan}) | NIM: **{$nim}**{$minatStr}";
        }
        $baris[] = '';
        $baris[] = '_Menampilkan 5 hasil pertama. Coba perjelas dengan nama lengkap, prodi, atau NIM spesifik._';

        return implode("\n", $baris);
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
