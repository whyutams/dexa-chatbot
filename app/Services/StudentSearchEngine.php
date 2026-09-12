<?php

namespace App\Services;

class StudentSearchEngine
{
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

    public const NIM_MAP = [
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

    public function search(array $angkatanData, string $query, array $nimMap = [], array $supportedAngkatan = ['2021', '2022', '2023']): array
    {
        $nimMap = $nimMap !== [] ? $nimMap : self::NIM_MAP;
        $q = trim($query);
        $qLower = mb_strtolower($q);

        if (preg_match_all('/\b(\d{6,10})\b/', $qLower, $mNims)) {
            foreach ($mNims[1] as $targetNim) {
                foreach ($angkatanData as $tahun => $mahasiswa) {
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

                    if (isset($nimMap[$prefix])) {
                        $info = $nimMap[$prefix];
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

        $text = preg_replace('/[^\p{L}\p{N}\s-]/u', ' ', $qLower) ?? $qLower;

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

        foreach ($angkatanData as $tahun => $mahasiswa) {
            if (!in_array((string)$tahun, $supportedAngkatan, true)) {
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

    public function hasSpecificSearchQuery(string $pertanyaan): bool
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
}
