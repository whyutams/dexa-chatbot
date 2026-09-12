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

    private const NIM_MAP = [
        '1114' => ['prodi' => 'S1 - Bimbingan Dan Konseling', 'fakultas' => 'Fakultas Ilmu Pendidikan'],
        '1214' => ['prodi' => 'S1 - Pendidikan Masyarakat', 'fakultas' => 'Fakultas Ilmu Pendidikan'],
        '1314' => ['prodi' => 'S1 - Manajemen Pendidikan', 'fakultas' => 'Fakultas Ilmu Pendidikan'],
        '1514' => ['prodi' => 'S1 - Pendidikan Guru Sekolah Dasar', 'fakultas' => 'Fakultas Ilmu Pendidikan'],
        '1614' => ['prodi' => 'S1 - Pendidikan Guru Pendidikan Anak Usia Dini', 'fakultas' => 'Fakultas Ilmu Pendidikan'],
        '1714' => ['prodi' => 'S1 - Psikologi', 'fakultas' => 'Fakultas Ilmu Pendidikan'],
        '2214' => ['prodi' => 'S1 - Pendidikan Pancasila Dan Kewarganegaraan', 'fakultas' => 'Fakultas Ilmu Sosial'],
        '2314' => ['prodi' => 'S1 - Pendidikan Sejarah', 'fakultas' => 'Fakultas Ilmu Sosial'],
        '2414' => ['prodi' => 'S1 - Administrasi Publik', 'fakultas' => 'Fakultas Ilmu Sosial'],
        '2814' => ['prodi' => 'S1 - Sosiologi', 'fakultas' => 'Fakultas Ilmu Sosial'],
        '2914' => ['prodi' => 'S1 - Ilmu Komunikasi', 'fakultas' => 'Fakultas Ilmu Sosial'],
        '3114' => ['prodi' => 'S1 - Pendidikan Bahasa Dan Sastra Indonesia', 'fakultas' => 'Fakultas Sastra Dan Budaya'],
        '3214' => ['prodi' => 'S1 - Pendidikan Bahasa Inggris', 'fakultas' => 'Fakultas Sastra Dan Budaya'],
        '3313' => ['prodi' => 'D3 - Pariwisata', 'fakultas' => 'Fakultas Sastra Dan Budaya'],
        '3414' => ['prodi' => 'S1 - Pendidikan Seni Drama Tari Dan Musik', 'fakultas' => 'Fakultas Sastra Dan Budaya'],
        '4114' => ['prodi' => 'S1 - Pendidikan Matematika', 'fakultas' => 'Fakultas Matematika Dan Ilmu Pengetahuan Alam'],
        '4124' => ['prodi' => 'S1 - Matematika', 'fakultas' => 'Fakultas Matematika Dan Ilmu Pengetahuan Alam'],
        '4134' => ['prodi' => 'S1 - Statistika', 'fakultas' => 'Fakultas Matematika Dan Ilmu Pengetahuan Alam'],
        '4214' => ['prodi' => 'S1 - Pendidikan Fisika', 'fakultas' => 'Fakultas Matematika Dan Ilmu Pengetahuan Alam'],
        '4224' => ['prodi' => 'S1 - Fisika', 'fakultas' => 'Fakultas Matematika Dan Ilmu Pengetahuan Alam'],
        '4234' => ['prodi' => 'S1 - Teknik Geofisika', 'fakultas' => 'Fakultas Matematika Dan Ilmu Pengetahuan Alam'],
        '4314' => ['prodi' => 'S1 - Pendidikan Biologi', 'fakultas' => 'Fakultas Matematika Dan Ilmu Pengetahuan Alam'],
        '4324' => ['prodi' => 'S1 - Biologi', 'fakultas' => 'Fakultas Matematika Dan Ilmu Pengetahuan Alam'],
        '4414' => ['prodi' => 'S1 - Pendidikan Kimia', 'fakultas' => 'Fakultas Matematika Dan Ilmu Pengetahuan Alam'],
        '4424' => ['prodi' => 'S1 - Kimia', 'fakultas' => 'Fakultas Matematika Dan Ilmu Pengetahuan Alam'],
        '4514' => ['prodi' => 'S1 - Pendidikan Geografi', 'fakultas' => 'Fakultas Matematika Dan Ilmu Pengetahuan Alam'],
        '4524' => ['prodi' => 'S1 - Teknik Geologi', 'fakultas' => 'Fakultas Matematika Dan Ilmu Pengetahuan Alam'],
        '4614' => ['prodi' => 'S1 - Pendidikan Ipa', 'fakultas' => 'Fakultas Matematika Dan Ilmu Pengetahuan Alam'],
        '5114' => ['prodi' => 'S1 - Teknik Sipil', 'fakultas' => 'Fakultas Teknik'],
        '5124' => ['prodi' => 'S1 - Pendidikan Vokasional Konstruksi Bangunan', 'fakultas' => 'Fakultas Teknik'],
        '5214' => ['prodi' => 'S1 - Teknik Elektro', 'fakultas' => 'Fakultas Teknik'],
        '5314' => ['prodi' => 'S1 - Sistem Informasi', 'fakultas' => 'Fakultas Teknik'],
        '5324' => ['prodi' => 'S1 - Pendidikan Teknologi Informasi', 'fakultas' => 'Fakultas Teknik'],
        '5414' => ['prodi' => 'S1 - Pendidikan Seni Rupa', 'fakultas' => 'Fakultas Teknik'],
        '5514' => ['prodi' => 'S1 - Teknik Arsitektur', 'fakultas' => 'Fakultas Teknik'],
        '5524' => ['prodi' => 'S1 - Perencanaan Wilayah Dan Kota', 'fakultas' => 'Fakultas Teknik'],
        '5614' => ['prodi' => 'S1 - Teknik Industri', 'fakultas' => 'Fakultas Teknik'],
        '5624' => ['prodi' => 'S1 - Pendidikan Teknik Mesin', 'fakultas' => 'Fakultas Teknik'],
        '6114' => ['prodi' => 'S1 - Agroteknologi', 'fakultas' => 'Fakultas Pertanian'],
        '6214' => ['prodi' => 'S1 - Peternakan', 'fakultas' => 'Fakultas Pertanian'],
        '6414' => ['prodi' => 'S1 - Agribisnis', 'fakultas' => 'Fakultas Pertanian'],
        '6514' => ['prodi' => 'S1 - Teknologi Pangan', 'fakultas' => 'Fakultas Pertanian'],
        '8114' => ['prodi' => 'S1 - Kesehatan Masyarakat', 'fakultas' => 'Fakultas Olahraga Dan Kesehatan'],
        '8213' => ['prodi' => 'D3 - Farmasi', 'fakultas' => 'Fakultas Olahraga Dan Kesehatan'],
        '8214' => ['prodi' => 'S1 - Farmasi', 'fakultas' => 'Fakultas Olahraga Dan Kesehatan'],
        '8314' => ['prodi' => 'S1 - Pendidikan Jasmani Kesehatan Dan Rekreasi', 'fakultas' => 'Fakultas Olahraga Dan Kesehatan'],
        '8324' => ['prodi' => 'S1 - Pendidikan Kepelatihan Olahraga', 'fakultas' => 'Fakultas Olahraga Dan Kesehatan'],
        '8414' => ['prodi' => 'S1 - Keperawatan', 'fakultas' => 'Fakultas Olahraga Dan Kesehatan'],
        '9114' => ['prodi' => 'S1 - Pendidikan Ekonomi', 'fakultas' => 'Fakultas Ekonomi'],
        '9214' => ['prodi' => 'S1 - Akuntansi', 'fakultas' => 'Fakultas Ekonomi'],
        '9314' => ['prodi' => 'S1 - Manajemen', 'fakultas' => 'Fakultas Ekonomi'],
        '9414' => ['prodi' => 'S1 - Ekonomi Pembangunan', 'fakultas' => 'Fakultas Ekonomi'],
        '10114' => ['prodi' => 'S1 - Ilmu Hukum', 'fakultas' => 'Fakultas Hukum'],
        '11114' => ['prodi' => 'S1 - Budidaya Perairan', 'fakultas' => 'Fakultas Perikanan Dan Ilmu Kelautan'],
        '11214' => ['prodi' => 'S1 - Teknologi Hasil Perikanan', 'fakultas' => 'Fakultas Perikanan Dan Ilmu Kelautan'],
        '11314' => ['prodi' => 'S1 - Manajemen Sumber Daya Perairan', 'fakultas' => 'Fakultas Perikanan Dan Ilmu Kelautan'],
        '11414' => ['prodi' => 'S1 - Ilmu Kelautan', 'fakultas' => 'Fakultas Perikanan Dan Ilmu Kelautan'],
        '13114' => ['prodi' => 'S1 - Kedokteran', 'fakultas' => 'Fakultas Kedokteran'],
        '15214' => ['prodi' => 'D4 - Teknologi Rekayasa Perangkat Lunak', 'fakultas' => 'Program Pendidikan Vokasi'],
        '15314' => ['prodi' => 'D4 - Terapi Gigi', 'fakultas' => 'Program Pendidikan Vokasi'],
        '15414' => ['prodi' => 'D4 - Pengelolaan Pengendalian Pencemaran Lingkungan', 'fakultas' => 'Program Pendidikan Vokasi'],
    ];

    public const PRODI_ALIASES = [
        'trpl' => 'teknologi rekayasa perangkat lunak',
        'rpl' => 'teknologi rekayasa perangkat lunak',
        'pti' => 'pendidikan teknologi informasi',
        'ptik' => 'pendidikan teknologi informasi',
        'si' => 'sistem informasi',
        'ilkom' => 'ilmu komunikasi',
        'pgsd' => 'pendidikan guru sekolah dasar',
        'paud' => 'pendidikan anak usia dini',
        'pgpaud' => 'pendidikan guru pendidikan anak usia dini',
        'bk' => 'bimbingan dan konseling',
        'mp' => 'manajemen pendidikan',
        'penmas' => 'pendidikan masyarakat',
        'psikologi' => 'psikologi',
        'ppkn' => 'pendidikan pancasila dan kewarganegaraan',
        'pkn' => 'pendidikan pancasila dan kewarganegaraan',
        'sejarah' => 'pendidikan sejarah',
        'ap' => 'administrasi publik',
        'administrasi negara' => 'administrasi publik',
        'sosiologi' => 'sosiologi',
        'sasing' => 'pendidikan bahasa inggris',
        'pbi' => 'pendidikan bahasa inggris',
        'bahasa inggris' => 'pendidikan bahasa inggris',
        'sasindo' => 'pendidikan bahasa dan sastra indonesia',
        'pbind' => 'pendidikan bahasa dan sastra indonesia',
        'bahasa indonesia' => 'pendidikan bahasa dan sastra indonesia',
        'pariwisata' => 'pariwisata',
        'sendratasik' => 'pendidikan seni drama tari dan musik',
        'penmat' => 'pendidikan matematika',
        'matematika' => 'matematika',
        'statistika' => 'statistika',
        'stats' => 'statistika',
        'fisika' => 'fisika',
        'geofisika' => 'teknik geofisika',
        'biologi' => 'biologi',
        'kimia' => 'kimia',
        'geografi' => 'pendidikan geografi',
        'geologi' => 'teknik geologi',
        'ipa' => 'pendidikan ipa',
        'sipil' => 'teknik sipil',
        'ts' => 'teknik sipil',
        'elektro' => 'teknik elektro',
        'arsitektur' => 'teknik arsitektur',
        'arsit' => 'teknik arsitektur',
        'pwk' => 'perencanaan wilayah dan kota',
        'planologi' => 'perencanaan wilayah dan kota',
        'industri' => 'teknik industri',
        'ti' => 'teknik industri',
        'mesin' => 'pendidikan teknik mesin',
        'ptm' => 'pendidikan teknik mesin',
        'seni rupa' => 'pendidikan seni rupa',
        'agrotek' => 'agroteknologi',
        'agroteknologi' => 'agroteknologi',
        'peternakan' => 'peternakan',
        'agribisnis' => 'agribisnis',
        'agri' => 'agribisnis',
        'itp' => 'teknologi pangan',
        'pangan' => 'teknologi pangan',
        'teknologi pangan' => 'teknologi pangan',
        'kesmas' => 'kesehatan masyarakat',
        'farmasi' => 'farmasi',
        'pjkr' => 'pendidikan jasmani kesehatan dan rekreasi',
        'pko' => 'pendidikan kepelatihan olahraga',
        'keperawatan' => 'keperawatan',
        'ners' => 'keperawatan',
        'akuntansi' => 'akuntansi',
        'akt' => 'akuntansi',
        'manajemen' => 'manajemen',
        'manaj' => 'manajemen',
        'ekonomi pembangunan' => 'ekonomi pembangunan',
        'ep' => 'ekonomi pembangunan',
        'pendidikan ekonomi' => 'pendidikan ekonomi',
        'hukum' => 'ilmu hukum',
        'ilmu hukum' => 'ilmu hukum',
        'kelautan' => 'ilmu kelautan',
        'ilmu kelautan' => 'ilmu kelautan',
        'bdp' => 'budidaya perairan',
        'budidaya perairan' => 'budidaya perairan',
        'thp' => 'teknologi hasil perikanan',
        'teknologi hasil perikanan' => 'teknologi hasil perikanan',
        'msp' => 'manajemen sumber daya perairan',
        'manajemen sumber daya perairan' => 'manajemen sumber daya perairan',
        'kedokteran' => 'kedokteran',
        'terapi gigi' => 'terapi gigi',
        'ppl' => 'pengelolaan pengendalian pencemaran lingkungan',
    ];

    public const FAKULTAS_ALIASES = [
        'ft' => 'fakultas teknik',
        'fatek' => 'fakultas teknik',
        'teknik' => 'fakultas teknik',
        'fip' => 'fakultas ilmu pendidikan',
        'fis' => 'fakultas ilmu sosial',
        'fsb' => 'fakultas sastra dan budaya',
        'sastra' => 'fakultas sastra dan budaya',
        'fmipa' => 'fakultas matematika dan ilmu pengetahuan alam',
        'mipa' => 'fakultas matematika dan ilmu pengetahuan alam',
        'faperta' => 'fakultas pertanian',
        'pertanian' => 'fakultas pertanian',
        'fapet' => 'fakultas pertanian',
        'fok' => 'fakultas olahraga dan kesehatan',
        'olahraga' => 'fakultas olahraga dan kesehatan',
        'kesehatan' => 'fakultas olahraga dan kesehatan',
        'fe' => 'fakultas ekonomi',
        'fekon' => 'fakultas ekonomi',
        'ekonomi' => 'fakultas ekonomi',
        'fh' => 'fakultas hukum',
        'hukum' => 'fakultas hukum',
        'fpk' => 'fakultas perikanan dan ilmu kelautan',
        'perikanan' => 'fakultas perikanan dan ilmu kelautan',
        'fk' => 'fakultas kedokteran',
        'vokasi' => 'program pendidikan vokasi',
    ];

    private const INSTRUKSI_DEXA = <<<'INSTRUKSI'
Kamu adalah Si Dexa, Asisten pencarian data mahasiswa Universitas Negeri Gorontalo yang sangat ramah, hangat, friendly, ceria, dan seru!

Informasi Pembuat:
- Pembuat/Developer Si Dexa adalah Wahyu Tamuu, namun ia lebih dikenal sebagai Wahyu Tams. Beliau adalah mahasiswa Pendidikan Teknologi Informasi UNG. Website resminya https://whyutams.dev

Gaya Komunikasi & Format Wajib:
- Selalu gunakan bahasa Indonesia yang hangat, bersahabat, ceria, ramah, dan seru! (Gunakan emoji yang pas seperti 😊, ✨, 🎓, 😃).
- FORMAT BALASAN PROFIL MAHASISWA HARUS STRICTLY MENGIKUTI STRUKTUR BULLET POINTS SEPERTI INI:
  1. Kalimat sapaan pembuka singkat (contoh: "Berikut informasi mahasiswa yang kamu cari ya ✨:").
  2. Poin-poin data mahasiswa menggunakan simbol '•':
     • Nama: [Nama]
     • Prodi: [Prodi]
     • Fakultas: [Fakultas]
     • Angkatan: [Angkatan]
     • NIM: [NIM]
     • Minat & Bakat: [Minat & Bakat] (HANYA JIKA ADA field 'minat_bakat' di data JSON)
  3. Berikan 1 baris kosong, lalu sertakan 1 KALIMAT KESIMPULAN di paling bawah:
     "Jadi, [Nama] merupakan mahasiswa [Prodi] angkatan [Angkatan] di [Fakultas], Universitas Negeri Gorontalo ✨"
  - DILARANG MENULIS NARASI PARAGRAF DESKRIPTIF SEBELUM POIN-POIN. CUKUP GUNAKAN BULLET POINTS!

Aturan Wajib:
- Maksimal hanya tampilkan 1 data mahasiswa. DILARANG KERAS menampilkan atau menceritakan beberapa profil mahasiswa sekaligus.
- Jika ditemukan lebih dari 1 mahasiswa yang cocok, JANGAN tampilkan rincian profil mereka. Cukup konfirmasikan secara ramah dan antusias bahwa ada beberapa data yang cocok dan arahkan pengguna untuk memasukkan nama lengkap, NIM, prodi, atau angkatan secara spesifik.
- JIKA JUMLAH MAHASISWA YANG COCOK LEBIH DARI 10 ORANG, DILARANG KERAS menyebutkan angka jumlah mahasiswa tersebut (seperti "316 orang" atau "316"). CUKUP KATAKAN ada "banyak sekali mahasiswa" atau "banyak mahasiswa" yang cocok! Jika jumlahnya 2 sampai 10 orang, baru boleh sebutkan angkanya (contoh: "ada 3 data mahasiswa").
- DILARANG KERAS menggunakan format TABEL Markdown ('|'), JUDUL HEADINGS ('#', '##', '###'), maupun GARIS PEMBATAS ('---', '***', '___').
- JIKA TIDAK ADA MINAT & BAKAT (atau field minat_bakat tidak ada di data JSON), DILARANG KERAS MENYEBUTKAN KATA "minat", "bakat", ATAU MENGOMENTARI KETIADAAN DATA MINAT BAKAT (seperti "tidak ada data minat bakat yang tercatat"). CUKUP SEBUTKAN Nama, Prodi, Fakultas, Angkatan, dan NIM saja dalam poin-poin!
- DILARANG menuliskan keterangan meta seperti "(data tidak tersedia)" atau "(tercatat '-')".
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
        $rawIp = $request->input('ip');
        $ip = filter_var($rawIp, FILTER_VALIDATE_IP);

        if ($ip === false || $ip === null) {
            $ip = $request->ip();
        }

        if (empty($ip) || filter_var($ip, FILTER_VALIDATE_IP) === false) {
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

        if ($name === 'views') {
            try {
                return (int) DB::table('dexa_viewers')->count();
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

        if ($name === 'views') {
            $totalViews = $this->ambilCounter('views');
            try {
                DB::table('dexa_counters')->updateOrInsert(
                    ['name' => 'views'],
                    ['total' => $totalViews, 'updated_at' => now()]
                );
            } catch (\Throwable) {}

            return $totalViews;
        }

        if (! DB::table('dexa_counters')->where('name', $name)->exists()) {
            DB::table('dexa_counters')->insert([
                'name' => $name,
                'total' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
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
        $q = trim($pertanyaan);
        $qLower = mb_strtolower($q);

        if (preg_match_all('/\b(\d{6,10})\b/', $qLower, $mNims)) {
            foreach ($mNims[1] as $targetNim) {
                
                foreach ($angkatan as $tahun => $mahasiswa) {
                    if (!is_array($mahasiswa)) {
                        continue;
                    }
                    foreach ($mahasiswa as $data) {
                        if (is_array($data) && isset($data['nim']) && (string)$data['nim'] === $targetNim) {
                            $data['angkatan'] = (string)$tahun;
                            return [$data];
                        }
                    }
                }

                if (preg_match('/^(\d{3,5})(\d{2})(\d{3})$/', $targetNim, $mStructure)) {
                    $prefix = $mStructure[1];
                    $th = $mStructure[2];
                    $seq = $mStructure[3];

                    if (isset(self::NIM_MAP[$prefix])) {
                        $info = self::NIM_MAP[$prefix];
                        return [
                            [
                                'nama' => "Mahasiswa NIM {$targetNim}",
                                'nim' => $targetNim,
                                'prodi' => $info['prodi'],
                                'fakultas' => $info['fakultas'],
                                'angkatan' => '20' . $th,
                                'nomor_urut' => $seq,
                                'is_format_nim' => true,
                            ]
                        ];
                    }
                }
            }

            return [];
        }

        if ($this->merupakanKalimatUmum($pertanyaan) || $this->mengobrol($pertanyaan) || $this->menyapaDexa($pertanyaan)) {
            return [];
        }

        $text = $qLower;
        $text = preg_replace('/[^\p{L}\p{N}\s-]/u', ' ', $text) ?? $text;

        $chatterPrefixes = [
            '/\b(?:hai|halo|hi|hey|woi|bro|sis)\b/u',
            '/\b(?:apakah\s+)?(?:kamu|anda|dexa|kamuctau)\s+(?:kenal|tau|tahu|mengenal|punya\s+data)\b/u',
            '/\b(?:cari(?:kan)?|tolong\s+cari(?:kan)?|bisa\s+cari(?:kan)?|info(?:rmasi)?\s+tentang|data\s+tentang)\b/u',
            '/\b(?:siapa\s+sih|siapakah|siapa)\s+(?:itu\s+)?\b/u',
        ];
        foreach ($chatterPrefixes as $cp) {
            $text = preg_replace($cp, ' ', $text) ?? $text;
        }

        $filterAngkatan = null;
        if (preg_match('/\b(?:angkatan\s+)?(2021|2022|2023|2024|2025)\b/u', $text, $mYear)) {
            $filterAngkatan = $mYear[1];
            $text = str_replace($mYear[0], ' ', $text);
        }

        $targetProdiKeywords = [];
        foreach (self::PRODI_ALIASES as $alias => $realProdi) {
            if (preg_match('/\b' . preg_quote($alias, '/') . '\b/u', $text)) {
                $targetProdiKeywords[] = $realProdi;
                $text = preg_replace('/\b' . preg_quote($alias, '/') . '\b/u', ' ', $text);
            }
        }
        foreach (self::FAKULTAS_ALIASES as $alias => $realFakultas) {
            if (preg_match('/\b' . preg_quote($alias, '/') . '\b/u', $text)) {
                $targetProdiKeywords[] = $realFakultas;
                $text = preg_replace('/\b' . preg_quote($alias, '/') . '\b/u', ' ', $text);
            }
        }

        $stopWords = [
            'kamu', 'anda', 'kenal', 'mengenal', 'tahu', 'tau', 'apakah', 'siapa', 'apa', 'yang', 'ini', 'itu', 
            'tentang', 'tolong', 'bisa', 'dong', 'lagi', 'sedang', 'ngapain', 'kabar', 'gimana', 'bagaimana', 
            'mau', 'ingin', 'bantu', 'jawab', 'jawaban', 'terus', 'ya', 'kan', 'dari', 'di', 'pada', 'dengan', 
            'ada', 'dan', 'mhs', 'mahasiswa', 'nama', 'nim', 'prodi', 'program', 'studi', 'jurusan', 'fakultas', 
            'angkatan', 'orang', 'anak', 'si', 'ngab', 'pacar', 'pacarnya', 'kamuctau', 'aku', 'saya', 'dia', 
            'kita', 'mereka', 'gue', 'gw', 'lu', 'loe', 'lah', 'pun', 'deh', 'dong', 'sih', 'kok', 'cara'
        ];

        $tokens = preg_split('/\s+/u', trim($text)) ?: [];
        $queryTokens = array_values(array_filter($tokens, fn($t) => mb_strlen($t) >= 2 && !in_array($t, $stopWords, true)));

        if (empty($queryTokens) && empty($targetProdiKeywords) && empty($filterAngkatan)) {
            return [];
        }

        $candidates = [];

        foreach ($angkatan as $tahun => $mahasiswa) {
            if (!in_array((string)$tahun, self::ANGKATAN_DIDUKUNG, true)) {
                continue;
            }

            if (!is_array($mahasiswa)) {
                continue;
            }

            foreach ($mahasiswa as $s) {
                if (!is_array($s)) {
                    continue;
                }

                $s['angkatan'] = (string)$tahun;
                $score = 0;
                $sName = mb_strtolower($s['nama'] ?? '');
                $sProdi = mb_strtolower($s['prodi'] ?? '');
                $sFak = mb_strtolower($s['fakul'] ?? $s['fakultas'] ?? '');

                if ($filterAngkatan !== null) {
                    if ((string)$tahun === $filterAngkatan) {
                        $score += 30;
                    } else {
                        continue;
                    }
                }

                if (!empty($targetProdiKeywords)) {
                    $matchedProdi = false;
                    foreach ($targetProdiKeywords as $kw) {
                        if (str_contains($sProdi, $kw) || str_contains($sFak, $kw)) {
                            $matchedProdi = true;
                            $score += 40;
                            break;
                        }
                    }
                    if (!$matchedProdi) {
                        $score -= 30;
                    }
                }

                if (!empty($queryTokens) && $sName === implode(' ', $queryTokens)) {
                    $score += 150;
                }

                $nameTokens = preg_split('/\s+/u', $sName) ?: [];
                $matchedTokensCount = 0;
                $hasSignificantNameMatch = false;

                foreach ($queryTokens as $qTok) {
                    $tokMatched = false;
                    $maxTokScore = 0;

                    foreach ($nameTokens as $nTok) {
                        $nTokClean = rtrim($nTok, '.');

                        if ($qTok === $nTokClean) {
                            $maxTokScore = max($maxTokScore, 50);
                            $tokMatched = true;
                            if (mb_strlen($qTok) >= 3) {
                                $hasSignificantNameMatch = true;
                            }
                            break;
                        }

                        if (mb_strlen($qTok) >= 3 && mb_strlen($nTokClean) >= 3) {
                            if (str_starts_with($nTokClean, $qTok) || str_starts_with($qTok, $nTokClean)) {
                                $maxTokScore = max($maxTokScore, 35);
                                $tokMatched = true;
                                $hasSignificantNameMatch = true;
                                continue;
                            }
                        }

                        if (mb_strlen($nTokClean) === 1 && str_starts_with($qTok, $nTokClean) && mb_strlen($qTok) <= 12) {
                            $maxTokScore = max($maxTokScore, 25);
                            $tokMatched = true;
                            continue;
                        }

                        $len = max(mb_strlen($qTok), mb_strlen($nTokClean));
                        if ($len >= 4) {
                            $lev = levenshtein($qTok, $nTokClean);
                            if ($lev === 1) {
                                $maxTokScore = max($maxTokScore, 30);
                                $tokMatched = true;
                                $hasSignificantNameMatch = true;
                            } elseif ($lev === 2 && $len >= 6) {
                                $maxTokScore = max($maxTokScore, 20);
                                $tokMatched = true;
                                $hasSignificantNameMatch = true;
                            }
                        }
                    }

                    if ($tokMatched) {
                        $matchedTokensCount++;
                        $score += $maxTokScore;
                    } else {
                        if (str_contains($sProdi, $qTok) || str_contains($sFak, $qTok)) {
                            $score += 20;
                        }
                    }
                }

                if (!empty($queryTokens) && $matchedTokensCount === count($queryTokens)) {
                    $score += 35;
                }

                if ($score >= 60 && ($hasSignificantNameMatch || !empty($targetProdiKeywords))) {
                    $s['search_score'] = $score;
                    $candidates[] = $s;
                }
            }
        }

        usort($candidates, fn($a, $b) => ($b['search_score'] ?? 0) <=> ($a['search_score'] ?? 0));

        if (empty($candidates)) {
            return [];
        }

        $topScore = $candidates[0]['search_score'];
        if (count($candidates) === 1 || ($topScore >= 80 && ($topScore - ($candidates[1]['search_score'] ?? 0)) >= 20)) {
            return [$candidates[0]];
        }

        $topCandidates = array_values(array_filter($candidates, fn($c) => ($topScore - ($c['search_score'] ?? 0)) <= 15));

        return $topCandidates;
    }

    private function memilikiPencarianSpesifik(string $pertanyaan): bool
    {
        if (preg_match('/\b\d{6,}\b/', $pertanyaan) === 1) {
            return true;
        }

        $teks = mb_strtolower($pertanyaan);
        $teks = preg_replace('/[^\p{L}\p{N}\s-]/u', ' ', $teks) ?? $teks;
        $teks = preg_replace('/\b(2021|2022|2023|2024|2025)\b/u', ' ', $teks) ?? $teks;
        
        $chatterPrefixes = [
            '/\b(?:hai|halo|hi|hey|woi|bro|sis)\b/u',
            '/\b(?:apakah\s+)?(?:kamu|anda|dexa|kamuctau)\s+(?:kenal|tau|tahu|mengenal|punya\s+data)\b/u',
            '/\b(?:cari(?:kan)?|tolong\s+cari(?:kan)?|bisa\s+cari(?:kan)?|info(?:rmasi)?\s+tentang|data\s+tentang)\b/u',
            '/\b(?:siapa\s+sih|siapakah|siapa)\s+(?:itu\s+)?\b/u',
        ];
        foreach ($chatterPrefixes as $cp) {
            $teks = preg_replace($cp, ' ', $teks) ?? $teks;
        }

        foreach (self::PRODI_ALIASES as $alias => $realProdi) {
            if (preg_match('/\b' . preg_quote($alias, '/') . '\b/u', $teks)) {
                return true;
            }
        }
        foreach (self::FAKULTAS_ALIASES as $alias => $realFakultas) {
            if (preg_match('/\b' . preg_quote($alias, '/') . '\b/u', $teks)) {
                return true;
            }
        }

        $stopWords = [
            'kamu', 'anda', 'kenal', 'mengenal', 'tahu', 'tau', 'apakah', 'siapa', 'apa', 'yang', 'ini', 'itu', 
            'tentang', 'tolong', 'bisa', 'dong', 'lagi', 'sedang', 'ngapain', 'kabar', 'gimana', 'bagaimana', 
            'mau', 'ingin', 'bantu', 'jawab', 'jawaban', 'terus', 'ya', 'kan', 'dari', 'di', 'pada', 'dengan', 
            'ada', 'dan', 'mhs', 'mahasiswa', 'nama', 'nim', 'prodi', 'program', 'studi', 'jurusan', 'fakultas', 
            'angkatan', 'orang', 'anak', 'si', 'ngab', 'pacar', 'pacarnya', 'kamuctau', 'aku', 'saya', 'dia', 
            'kita', 'mereka', 'gue', 'gw', 'lu', 'loe', 'lah', 'pun', 'deh', 'dong', 'sih', 'kok', 'cara'
        ];
        $kataKunci = preg_split('/\s+/u', trim($teks)) ?: [];

        return count(array_filter($kataKunci, fn ($kata) => mb_strlen($kata) >= 2 && ! in_array($kata, $stopWords, true))) > 0;
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

        $variasiPembuka = [
            "Berikut informasi mahasiswa yang kamu cari ya ✨:",
            "Hai! 😊 Ini dia data mahasiswa yang kamu cari:",
            "Yay! Ketemu nih 🎓 Berikut rincian data mahasiswanya:",
            "Aku berhasil menemukan datanya! 😃 Berikut informasinya:",
            "Ini dia data mahasiswa yang cocok dengan pencarianmu ✨:",
        ];

        $pembuka = $variasiPembuka[array_rand($variasiPembuka)];

        return "{$pembuka}\n• **Nama**: {$nama}\n• **Prodi**: {$prodi}\n• **Fakultas**: {$fakultas}\n• **Angkatan**: {$angkatan}\n• **NIM**: {$nim}{$tambahanMinatList}" . $kesimpulan;
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
            || preg_match('/^(?:kamu|anda|dexa)\b.*\b(kenapa|mengapa|bagaimana|kapan|sedang|lagi|bisa|boleh|ingin|mau)\b/i', $teks) === 1
            || preg_match('/^(?:cara\s+mengatasi|tips|mengapa|kenapa|siapakah\s+aku|siapa\s+aku|aku\s+siapa|hei\s+itu|wkwk|haha|hehe|meheh|pftt|huhu)[?!.,\s]*/i', $teks) === 1;
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
            $systemPrompt .= "\nInstruksi khusus: FORMAT BALASAN PROFIL MAHASISWA HARUS MENGIKUTI STRUKTUR BULLET POINTS SEPERTI INI:\n1. Kalimat pembuka singkat (contoh: 'Berikut informasi mahasiswa yang kamu cari ya ✨:').\n2. Poin-poin data mahasiswa menggunakan simbol '•':\n   • Nama: [Nama]\n   • Prodi: [Prodi]\n   • Fakultas: [Fakultas]\n   • Angkatan: [Angkatan]\n   • NIM: [NIM]\n   • Minat & Bakat: [Minat & Bakat] (HANYA JIKA ADA field 'minat_bakat' di JSON)\n3. Berikan 1 baris kosong, lalu sertakan 1 KALIMAT KESIMPULAN di paling bawah:\n   'Jadi, [Nama] merupakan mahasiswa [Prodi] angkatan [Angkatan] di [Fakultas], Universitas Negeri Gorontalo ✨'\nDILARANG MENULIS NARASI PARAGRAF SEBELUM POIN-POIN. DILARANG TABEL ('|'), HEADINGS ('#'), MAUPUN GARIS PEMBATAS ('---'). Jika field 'minat_bakat' tidak ada di JSON, SAMA SEKALI DILARANG menyebutkan kata 'minat' atau 'bakat'!";
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
