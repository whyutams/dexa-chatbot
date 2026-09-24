# Dexa Chatbot 🤖🎓

![Version](https://img.shields.io/badge/version-1.3--beta-blue.svg)
![PHP Version](https://img.shields.io/badge/PHP-%3E%3D%208.2-777BB4.svg?logo=php)
![Laravel Version](https://img.shields.io/badge/Laravel-12.x-FF2D20.svg?logo=laravel)
![License](https://img.shields.io/badge/license-MIT-green.svg)
![Status](https://img.shields.io/badge/build-passing-brightgreen.svg)

**Dexa Chatbot** (Si Dexa) adalah aplikasi asisten cerdas berbasis web yang dirancang untuk mempermudah pencarian data mahasiswa Universitas Negeri Gorontalo (UNG), khususnya angkatan 2021 hingga 2023. 

Aplikasi ini menghadirkan kembali fitur populer **Mahasiswa UNG Finder** dari platform legendaris *Dexafy* (2023–2024) yang dikembangkan oleh [Wahyu Tams](https://whyutams.dev) (Wahyu Tamuu).

---

## 💡 Deskripsi Singkat

Mencari data mahasiswa di lingkungan kampus secara cepat sering kali memerlukan query spesifik atau akses portal yang kompleks. **Dexa Chatbot** menyelesaikan masalah ini dengan menyediakan antarmuka percakapan (conversational UI) yang ramah, responsif, dan presisi.

### ✨ Fitur Utama
- **Mesin Pencari Mahasiswa Hibrida (Weighted Scoring)**: Algoritma pencarian multi-faktor yang mengkombinasikan pencarian *exact match*, pencarian berbasis token kata nama, serta kecerdasan batas Levenshtein (*fuzzy/typo tolerance*).
- **Ekspansi Alias Prodi & Fakultas**: Mendukung lebih dari 61 alias program studi (contoh: `trpl`, `pti`, `si`, `sasing`, `bk`, `kesmas`) dan 12 fakultas UNG (contoh: `ft`, `fip`, `fmipa`, `fsb`, `fok`, `fe`, `fh`).
- **Parsing Struktur NIM UNG Universal**: Mengenali dan mengurai NIM 10-digit UNG secara otomatis (format prefix prodi, 2-digit angkatan, dan 3-digit nomor urut mahasiswa).
- **Integrasi Groq AI (`llama-3.3-70b-versatile`)**: Penanganan basa-basi, salam, dan obrolan umum dengan persona Si Dexa yang ramah, ceria, dan bersahabat.
- **Persona & Output Standar**: Format jawaban berbasis *bullet point*, bebas klausa meta kosong, dan dilengkapi kalimat kesimpulan profil.
- **Keamanan & Rate Limiting**: Proteksi endpoint dengan kustom middleware `EnsureAllowedAccessUrl` dan pembatasan request `10 req/min`.

---

## 🛠️ Tech Stack

### Backend & Core
- **Language**: PHP 8.2+
- **Framework**: Laravel 12.x
- **Database**: SQLite (Tabel `dexa_chats`, `dexa_viewers`, `dexa_counters`)
- **LLM Integration**: Groq API (`llama-3.3-70b-versatile`)

### Frontend & Styling
- **Templating**: Blade Engine
- **Styling**: Vanilla CSS & Tailwind CSS v4
- **Asset Pipeline**: Vite 6 (`laravel-vite-plugin`, `@tailwindcss/vite`)

### Testing & Tooling
- **Testing**: PHPUnit 11
- **Process Manager**: Concurrently (`npm run dev` / `composer run dev`)

---

## 📂 Arsitektur & Struktur Direktori

Berikut adalah direktori dan file inti yang membangun arsitektur aplikasi **Dexa Chatbot**:

```text
dexa-chatbot/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── MainController.php          # Controller utama pencarian, chat, & statistik
│   │   └── Middleware/
│   │       └── EnsureAllowedAccessUrl.php   # Security middleware validasi origin/referer
│   ├── Providers/
│   │   └── AppServiceProvider.php          # Konfigurasi Rate Limiter (tanya-dexa)
│   └── Services/
│       ├── ChatContextService.php           # Sanitasi riwayat & resolusi anaphora kata ganti
│       ├── ResponseFormatterService.php     # Format keluaran profil & persona Si Dexa
│       └── StudentSearchEngine.php         # Engine pencarian weighted scoring & NIM parser
├── config/
│   └── services.php                         # Konfigurasi Groq API Key, REST API, & URL Access
├── database/
│   └── database.sqlite                      # Storage SQLite database
├── resources/
│   ├── css/                                # Tailwind CSS & styling kustom
│   ├── js/                                 # Logika interaktif antarmuka chat
│   └── views/
│       └── landing.blade.php               # Halaman utama & modal antarmuka Dexa Chatbot
├── routes/
│   └── web.php                              # Definisi rute web & endpoint pencarian
├── storage/
│   └── app/
│       ├── data/
│       │   └── main.json                   # Dataset offline data mahasiswa UNG 2021-2023
│       └── images/
│           └── example.png                 # Asset gambar contoh penggunaan modal
├── tests/                                   # Suite pengujian unit & integrasi PHPUnit
├── .env.example                             # Template konfigurasi variabel lingkungan
├── composer.json                            # Dependensi PHP & skrip automasi Laravel
├── package.json                             # Dependensi Node.js & Vite build tool
└── vite.config.js                           # Konfigurasi bundler Vite
```

---

## 🚀 Cara Memulai (Getting Started)

### Prasyarat (Prerequisites)
Pastikan perangkat Anda telah terinstal dependensi berikut:
- **PHP** `>= 8.2` (dengan ekstensi `pdo_sqlite`, `mbstring`, `curl`, `json`)
- **Composer** `>= 2.x`
- **Node.js** `>= 18.x` & **NPM**
- **SQLite3**

### 1. Clone Repositori
```bash
git clone https://github.com/whyutams/dexa-chatbot.git
cd dexa-chatbot
```

### 2. Instal Dependensi
Instal dependensi backend (PHP) dan frontend (Node.js):
```bash
composer install
npm install
```

### 3. Konfigurasi Environment Variables
Salin file `.env.example` menjadi `.env` dan generate application key:
```bash
cp .env.example .env
php artisan key:generate
```

Buka file `.env` dan sesuaikan variabel berikut:
```env
APP_NAME="Dexa Chatbot"
APP_ENV=local
APP_URL=http://localhost:8000

# Integrasi Groq AI
GROQ_API_KEY=sk_groq_your_api_key_here
GROQ_MODEL=llama-3.3-70b-versatile

# Sinkronisasi REST API (Opsional)
REST_API=

# Keamanan URL Access (Opsional)
URL_ACCESS=
```

### 4. Persiapan Database
Buat file database SQLite dan jalankan migrasi tabel:
```bash
touch database/database.sqlite
php artisan migrate
```

### 5. Menjalankan Aplikasi Secara Lokal
Jalankan server pengembangan (Laravel Serve + Vite) secara bersamaan menggunakan skrip Composer:
```bash
composer run dev
```
Atau secara manual di dua terminal terpisah:
```bash
# Terminal 1: Laravel Backend
php artisan serve

# Terminal 2: Vite Assets
npm run dev
```

Aplikasi dapat diakses melalui browser di `http://localhost:8000`.

---

## 📖 Cara Penggunaan (Usage)

### Melalui Antarmuka Web (UI)
1. Buka `http://localhost:8000` pada peramban web Anda.
2. Masukkan kata kunci pencarian pada kolom input chat, contohnya:
   - Nama Mahasiswa: `"Asep Tanjung"`
   - Nama + Prodi/Fakultas: `"Asep dari bahasa inggris"`
   - NIM Spesifik: `"532423001"` atau `"1521425001"`
3. Klik tombol kirim untuk menerima balasan profil mahasiswa yang presisi dari Si Dexa.

## 🧪 Pengujian (Testing)

Proyek ini dilengkapi dengan suite pengujian otomatis menggunakan **PHPUnit** untuk memastikan keandalan fungsi backend dan pencarian data.

Untuk menjalankan seluruh pengujian:
```bash
php artisan test
```
Atau menggunakan biner PHPUnit langsung:
```bash
vendor/bin/phpunit
```
---

## 📄 Lisensi (License)

Proyek ini dilisensikan di bawah naungan **[MIT License](LICENSE)**.

---

<p align="center">
  Dikembangkan dengan 💖 oleh <a href="https://whyutams.dev">Wahyu Tams</a>
</p>
