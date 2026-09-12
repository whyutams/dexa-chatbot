<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#53699f">
    <meta name="description" content="Dexa, Chatbot Asisten Pencarian Mahasiswa UNG membantu mencari data mahasiswa Universitas Negeri Gorontalo">
    <meta name="keywords" content="Dexa, Dexafy, chatbot mahasiswa UNG, pencarian mahasiswa UNG, data mahasiswa UNG, mahasiswa Universitas Negeri Gorontalo">
    <meta name="author" content="Wahyu Tams">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ url('/') }}">
    <title>Dexa Chatbot</title>
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Dexa, Chatbot Asisten Pencarian Mahasiswa UNG">
    <meta property="og:title" content="Dexa, Chatbot Asisten Pencarian Mahasiswa UNG">
    <meta property="og:description" content="Cari data mahasiswa Universitas Negeri Gorontalo berdasarkan nama atau NIM bersama Dexa.">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:locale" content="id_ID">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="Dexa, Chatbot Asisten Pencarian Mahasiswa UNG">
    <meta name="twitter:description" content="Cari data mahasiswa UNG secara cepat">
    <script type="application/ld+json">
        @verbatim
                {
                    "@context": "https://schema.org",
                    "@type": "WebApplication",
                    "name": "Dexa, Chatbot Asisten Pencarian Mahasiswa UNG",
                    "alternateName": "Dexa Chatbot",
                    "url": "/",
                    "description": "Aplikasi pencarian data mahasiswa Universitas Negeri Gorontalo",
                    "applicationCategory": "EducationalApplication",
                    "operatingSystem": "Web",
                    "inLanguage": "id-ID",
                    "author": {
                        "@type": "Person",
                        "name": "Wahyu Tams",
                        "url": "https://whyutams.dev"
                    }
                }
        @endverbatim
    </script>
    <script src="https://cdn.tailwindcss.com/3.4.17"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['DM Sans', 'ui-sans-serif', 'sans-serif'], display: ['Space Grotesk', 'ui-sans-serif', 'sans-serif'] }, colors: { ink: '#202d59', coral: '#53699f', mist: '#eef0f7', mint: '#ddd9ee' } } } };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <style>
        body { background: #fff; background-image: linear-gradient(135deg, rgba(83,105,159,.12), transparent 38%), linear-gradient(315deg, rgba(105,71,141,.12), transparent 42%), radial-gradient(circle at 50% 105%, rgba(221,217,238,.55), transparent 35%); }
        .dexafy-gradient { background: linear-gradient(115deg, #53699f 0%, #5b5f9d 48%, #69478d 100%); }
        .chat-scroll::-webkit-scrollbar { width: 6px; } .chat-scroll::-webkit-scrollbar-thumb { background: #c4c9df; border-radius: 10px; }
        .typing-dot { animation: pulse 1.2s infinite ease-in-out; } .typing-dot:nth-child(2) { animation-delay: .15s; } .typing-dot:nth-child(3) { animation-delay: .3s; }
        @@keyframes pulse { 0%, 60%, 100% { opacity: .3; transform: translateY(0); } 30% { opacity: 1; transform: translateY(-3px); } }    </style>
</head>
<body class="min-h-screen font-sans text-ink">
    <main class="mx-auto flex min-h-screen max-w-6xl flex-col px-4 py-8 sm:px-8 lg:px-12">
        <section class="grid flex-1 gap-8 py-8 lg:grid-cols-[.8fr_1.4fr] lg:items-center">
            <div class="max-w-md" data-aos="fade-right">
                <p class="mb-4 text-sm font-bold uppercase text-coral">Dexa Chatbot (BETA)</p>
                <h1 class="font-display text-4xl font-bold leading-[1.05] tracking-tight sm:text-5xl">Si <span class="text-coral">Dexa</span>, Chatbot Asisten Pencarian Mahasiswa UNG</h1>
                <p class="mt-5 max-w-sm leading-7 text-ink/65">Salah satu fitur populer <a href="https://dexafyx.web.app" target="_blank" rel="noopener noreferrer" class="font-semibold text-coral underline decoration-coral/50 underline-offset-2 hover:text-[#435889]"><strong>Dexafy</strong></a> pada 2024, kini hadir kembali sebagai pencarian mahasiswa Universitas Negeri Gorontalo.</p>
                <p class="mt-4 leading-7 text-coral font-bold">Coming soon! Next update informasi mahasiswa disertakan dengan foto mahasiswa.</p>
            </div>

            <section class="flex h-[min(680px,76vh)] min-h-[520px] flex-col overflow-hidden rounded-[2rem] border border-[#e0e2ef] bg-white/90 shadow-[0_24px_80px_rgba(32,45,89,.14)] backdrop-blur" data-aos="fade-left" data-aos-delay="100">
                <div class="dexafy-gradient flex items-center justify-between px-5 py-4 text-white sm:px-7"><div><h2 class="font-bold">Dexa Chatbot (BETA)</h2><p class="text-xs text-white/70">Chatbot Asisten Pencarian Mahasiswa UNG</p></div><button type="button" id="btn-contoh" class="cursor-pointer rounded-full bg-white/20 px-3.5 py-1 text-xs font-bold text-white transition hover:bg-white/30 border border-white/20 active:scale-95 shadow-sm">Contoh Pencarian</button></div>
                <div id="pesan" class="chat-scroll flex-1 space-y-4 overflow-y-auto p-5 sm:p-7"><div class="flex items-start gap-3"><div class="max-w-[88%] rounded-2xl rounded-tl-sm bg-mist px-4 py-3 text-sm leading-6"><p class="mb-1 text-base font-bold text-coral">Dexa</p><div>Halo! Aku Dexa, Asisten pembantu pencarian mahasiswa UNG yang terdaftar untuk angkatan 2021-2023. Bisa beritahu saya nama atau nomor induk dari mahasiswa tujuanmu? ☺️ <br> Contoh pencarian: <b>Cari Asep Tanjung</b></div><time class="mt-2 block text-[10px] text-ink/40" data-waktu></time><div class="mt-3 flex flex-wrap gap-2"><button type="button" class="saran rounded-full border border-ink/10 bg-white px-3 py-1.5 text-xs font-semibold text-ink/70 transition hover:border-coral hover:text-coral" data-pertanyaan="Apa itu Dexafy?">Apa itu Dexafy?</button><button type="button" class="saran rounded-full border border-ink/10 bg-white px-3 py-1.5 text-xs font-semibold text-ink/70 transition hover:border-coral hover:text-coral" data-pertanyaan="Bagaimana cara mencari mahasiswa?">Bagaimana cara mencari mahasiswa?</button></div></div></div><div id="indikator" class="hidden items-start gap-3"><div class="rounded-2xl rounded-tl-sm bg-mist px-4 py-3"><div class="flex items-center gap-1"><span class="typing-dot h-1.5 w-1.5 rounded-full bg-coral"></span><span class="typing-dot h-1.5 w-1.5 rounded-full bg-coral"></span><span class="typing-dot h-1.5 w-1.5 rounded-full bg-coral"></span></div></div></div></div>
                <form id="form-tanya" class="border-t border-ink/10 p-4 sm:p-5"><div class="flex items-end gap-3 rounded-2xl border border-[#d8dbea] bg-mist/60 p-2 pl-4 transition focus-within:border-coral"><textarea id="pertanyaan" name="pertanyaan" rows="1" maxlength="250" required placeholder="Ketik pesan..." class="max-h-28 min-h-10 flex-1 resize-none bg-transparent py-2 text-sm text-ink outline-none placeholder:text-ink/40"></textarea><button id="tombol-kirim" type="submit" aria-label="Cari mahasiswa" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-coral text-white transition hover:bg-[#435889] disabled:cursor-not-allowed disabled:opacity-50"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg></button></div><div class="flex justify-between items-center"><p class="mt-2 text-right text-[11px] text-ink/40">v{{ $versi }}</p><p class="mt-2 text-right text-[11px] text-ink/40"><span id="jumlah-chat" data-target="{{ $jumlahChat }}">0</span>x chats</p><p class="mt-2 text-right text-[11px] text-ink/40"><span id="hitung">0</span>/250 karakter</p></div></form>
                
            </section>
        </section>
        <footer class="pb-4 text-center text-xs text-ink/40">
            <p class="font-bold mb-1">© 2026 Dexa Chatbot. All rights reserved.</p>
            <p><span id="jumlah-view" data-target="{{ $jumlahView }}">0</span>x views</p>
        </footer>
    </main>

    <div id="modal-contoh" class="fixed inset-0 z-50 hidden items-center justify-center bg-ink/70 p-4 backdrop-blur-sm transition-opacity duration-300">
        <div class="relative max-h-[90vh] max-w-2xl w-full overflow-hidden rounded-2xl bg-white p-4 shadow-2xl sm:p-6">
            <div class="mb-4 flex items-center justify-between border-b border-ink/10 pb-3">
                <div>
                    <h3 class="text-base font-bold text-ink">Contoh Hasil Pencarian Dexa</h3>
                    <p class="text-xs text-ink/60">Gambaran format jawaban & percakapan Dexa</p>
                </div>
                <button type="button" id="tutup-modal-contoh" class="flex h-8 w-8 items-center justify-center rounded-full bg-mist text-ink/70 transition hover:bg-coral hover:text-white cursor-pointer">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="max-h-[72vh] overflow-y-auto rounded-xl border border-ink/10 bg-mist/30 p-2">
                <img src="{{ asset('images/example.png') }}" alt="Contoh Hasil Pencarian Dexa" class="w-full h-auto rounded-lg shadow-sm">
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('form-tanya'); const input = document.getElementById('pertanyaan'); const pesan = document.getElementById('pesan'); const indikator = document.getElementById('indikator'); const tombol = document.getElementById('tombol-kirim'); const hitung = document.getElementById('hitung');
        const token = document.querySelector('meta[name="csrf-token"]').content; const riwayat = [];
        function animasikanAngka(elemen) { const target = Number(elemen.dataset.target || 0); const teksAwal = (elemen.textContent || '').replace(/[^\d]/g, ''); const awal = Number(teksAwal) || 0; if (awal === target) { elemen.textContent = target.toLocaleString('id-ID'); return; } const mulai = performance.now(); const durasi = 600; function frame(waktu) { const progres = Math.min((waktu - mulai) / durasi, 1); const nilaiSekarang = Math.floor(awal + (target - awal) * progres); elemen.textContent = nilaiSekarang.toLocaleString('id-ID'); if (progres < 1) requestAnimationFrame(frame); } requestAnimationFrame(frame); }
        document.querySelectorAll('[data-target]').forEach(animasi => animasikanAngka(animasi));
        async function catatView() { try { const rIp = await fetch('https://api.ipify.org/?format=json'); const dIp = await rIp.json(); if (!dIp || !dIp.ip) return; const responsCatat = await fetch('{{ route('statistik.view') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify({ ip: dIp.ip }) }); const hasil = await responsCatat.json(); const counter = document.getElementById('jumlah-view'); if (counter && typeof hasil.jumlah === 'number') { counter.dataset.target = hasil.jumlah; animasikanAngka(counter); } } catch (error) {} }
        catatView();
        function waktuWita() { return new Intl.DateTimeFormat('id-ID', { timeZone: 'Asia/Makassar', hour: '2-digit', minute: '2-digit' }).format(new Date()) + ' WITA'; }
        function gulirKeBawah() { pesan.scrollTo({ top: pesan.scrollHeight, behavior: 'smooth' }); }
        document.querySelectorAll('[data-waktu]').forEach((elemen) => { elemen.textContent = waktuWita(); });
        input.addEventListener('input', () => { hitung.textContent = input.value.length; input.style.height = 'auto'; input.style.height = `${Math.min(input.scrollHeight, 112)}px`; });
        function tambahkanFormat(target, teks) { const pola = /(\*\*[^*]+\*\*|\[[^\]]+\]\(https?:\/\/[^\s)]+\)|https?:\/\/[^\s<>]+)/g; let posisi = 0; for (const cocok of teks.matchAll(pola)) { const bagian = cocok[0]; const awal = cocok.index; if (awal > posisi) target.append(document.createTextNode(teks.slice(posisi, awal))); if (bagian.startsWith('**')) { const tebal = document.createElement('strong'); tebal.textContent = bagian.slice(2, -2); target.append(tebal); } else { const markdown = bagian.match(/^\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)$/); const url = (markdown ? markdown[2] : bagian).replace(/[.,!?;:]+$/, ''); const tautan = document.createElement('a'); tautan.href = url; tautan.target = '_blank'; tautan.rel = 'noopener noreferrer'; tautan.className = 'font-semibold text-coral underline decoration-coral/50 underline-offset-2 hover:text-[#435889]'; if (markdown) { tambahkanFormat(tautan, markdown[1]); } else { tautan.textContent = url; } target.append(tautan); } posisi = awal + bagian.length; } if (posisi < teks.length) target.append(document.createTextNode(teks.slice(posisi))); }
        function tambahPesan(teks, pengirim, saran = []) { const pembungkus = document.createElement('div'); pembungkus.className = `flex items-start gap-3 ${pengirim === 'pengguna' ? 'justify-end' : ''}`; const gelembung = document.createElement('div'); gelembung.className = pengirim === 'pengguna' ? 'max-w-[88%] rounded-2xl rounded-tr-sm bg-ink px-4 py-3 text-sm leading-6 text-white' : 'max-w-[88%] rounded-2xl rounded-tl-sm bg-mist px-4 py-3 text-sm leading-6'; if (pengirim === 'bot') { const label = document.createElement('p'); label.className = 'mb-1 text-base font-bold text-coral'; label.textContent = 'Dexa'; gelembung.append(label); const isi = document.createElement('div'); teks.split('\n').forEach((baris, indeks) => { if (indeks > 0) isi.append(document.createElement('br')); tambahkanFormat(isi, baris); }); gelembung.append(isi); } else { gelembung.textContent = teks; } const waktu = document.createElement('time'); waktu.className = pengirim === 'pengguna' ? 'mt-2 block text-[10px] text-white/50' : 'mt-2 block text-[10px] text-ink/40'; waktu.textContent = waktuWita(); gelembung.append(waktu); if (pengirim === 'bot' && saran.length) { const daftar = document.createElement('div'); daftar.className = 'mt-3 flex flex-wrap gap-2'; saran.forEach((teksSaran) => { const tombolSaran = document.createElement('button'); tombolSaran.type = 'button'; tombolSaran.className = 'saran rounded-full border border-ink/10 bg-white px-3 py-1.5 text-xs font-semibold text-ink/70 transition hover:border-coral hover:text-coral'; tombolSaran.dataset.pertanyaan = teksSaran; tombolSaran.textContent = teksSaran; daftar.append(tombolSaran); }); gelembung.append(daftar); } pembungkus.append(gelembung); pesan.append(pembungkus); gulirKeBawah(); }
        pesan.addEventListener('click', (event) => { const tombolSaran = event.target.closest('.saran'); if (!tombolSaran || tombol.disabled) return; input.value = tombolSaran.dataset.pertanyaan; input.dispatchEvent(new Event('input')); form.requestSubmit(); });
        form.addEventListener('submit', async (event) => { event.preventDefault(); const pertanyaan = input.value.trim(); if (!pertanyaan || tombol.disabled) return; tambahPesan(pertanyaan, 'pengguna'); pesan.append(indikator); gulirKeBawah(); riwayat.push({ role: 'user', content: pertanyaan }); input.value = ''; hitung.textContent = '0'; input.style.height = 'auto'; tombol.disabled = true; indikator.classList.replace('hidden', 'flex'); gulirKeBawah(); const counterChat = document.getElementById('jumlah-chat'); if (counterChat) { const nilaiAwal = Number(counterChat.dataset.target || (counterChat.textContent || '').replace(/[^\d]/g, '')) || 0; counterChat.dataset.target = nilaiAwal + 1; animasikanAngka(counterChat); } let jawaban; const waktuMulai = Date.now(); try { const [respons] = await Promise.all([fetch('{{ route('tanya.dexa') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': token }, body: JSON.stringify({ pertanyaan, riwayat: riwayat.slice(0, -1) }) }), new Promise((selesai) => setTimeout(selesai, 3000))]); const data = await respons.json(); jawaban = data.pesan || data.message || 'Maaf, pencarian belum dapat diproses.'; if (typeof data.jumlahChat === 'number' && data.jumlahChat > 0) { if (counterChat) { counterChat.dataset.target = data.jumlahChat; animasikanAngka(counterChat); } } } catch (error) { const sisaWaktu = Math.max(0, 3000 - (Date.now() - waktuMulai)); if (sisaWaktu) await new Promise((selesai) => setTimeout(selesai, sisaWaktu)); jawaban = 'Maaf, koneksi sedang bermasalah. Silakan coba lagi.'; } finally { riwayat.push({ role: 'assistant', content: jawaban }); tambahPesan(jawaban, 'bot'); indikator.classList.replace('flex', 'hidden'); gulirKeBawah(); tombol.disabled = false; } });

        const btnContoh = document.getElementById('btn-contoh');
        const modalContoh = document.getElementById('modal-contoh');
        const tutupModalContoh = document.getElementById('tutup-modal-contoh');
        if (btnContoh && modalContoh && tutupModalContoh) {
            btnContoh.addEventListener('click', () => { modalContoh.classList.replace('hidden', 'flex'); });
            tutupModalContoh.addEventListener('click', () => { modalContoh.classList.replace('flex', 'hidden'); });
            modalContoh.addEventListener('click', (e) => { if (e.target === modalContoh) modalContoh.classList.replace('flex', 'hidden'); });
            document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && !modalContoh.classList.contains('hidden')) modalContoh.classList.replace('flex', 'hidden'); });
        }
    </script>
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>AOS.init({ once: true, duration: 1000, easing: 'ease-out-cubic' });</script>
</body>
</html>
