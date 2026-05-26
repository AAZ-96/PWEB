<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dampak Kami — BantuIn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              brand: { 50:"#fff1f4",100:"#ffe4ea",200:"#fecdd8",300:"#fda4b9",400:"#fb6f94",500:"#f43f73",600:"#e11d5d",700:"#be124c",800:"#9f123f",900:"#881337" },
              ink:   { 900:"#0f172a",700:"#334155",600:"#475569",500:"#64748b",200:"#e2e8f0",100:"#f1f5f9",50:"#f8fafc" },
            },
            boxShadow: { soft:"0 10px 30px rgba(2,6,23,0.10)" },
          },
        },
      };
    </script>
    <style>
      @keyframes countUp { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
      @keyframes fadeUp  { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
      @keyframes barGrow { from { width:0; } to { width:var(--target-w); } }
      .fade-up { animation: fadeUp .55s ease forwards; }
      .fade-up:nth-child(1){animation-delay:.05s}
      .fade-up:nth-child(2){animation-delay:.12s}
      .fade-up:nth-child(3){animation-delay:.19s}
      .fade-up:nth-child(4){animation-delay:.26s}
      .fade-up:nth-child(5){animation-delay:.33s}
      .card-hover { transition: box-shadow .22s ease, transform .22s ease; }
      .card-hover:hover { box-shadow: 0 10px 30px rgba(2,6,23,0.13); transform: translateY(-3px); }
      .count-num { font-variant-numeric: tabular-nums; }
      .bar-fill { animation: barGrow 1.2s ease forwards; }
      .timeline-line::before { content:''; position:absolute; left:19px; top:28px; bottom:0; width:2px; background:#e2e8f0; }

      /* map dots pulse */
      @keyframes mapPulse { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.5);opacity:.6} }
      .map-dot { animation: mapPulse 2s infinite; }
    </style>
  </head>

  <body class="bg-white text-ink-900 antialiased">
    <script>
      // Bridge Laravel session to JS localStorage
      @if(auth()->check())
        localStorage.setItem('bantuin_current_user', JSON.stringify({
            id: {{ auth()->id() }},
            name: "{{ auth()->user()->name }}",
            email: "{{ auth()->user()->email }}",
            role: "{{ auth()->user()->role }}"
        }));
      @else
        localStorage.removeItem('bantuin_current_user');
      @endif
    </script>
    <div class="h-2 w-full bg-brand-700"></div>

    <!-- ═══ NAVBAR ═══ -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-ink-100">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-3">
          <a href="/" class="flex items-center gap-2">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-brand-700 text-white">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21s-7.2-4.6-9.6-9C.1 7.2 3.2 3.8 7.1 4.1c1.7.1 3.2 1 3.9 2.2.7-1.2 2.2-2.1 3.9-2.2 3.9-.3 7 3.1 4.7 7.9C19.2 16.4 12 21 12 21z"/></svg>
            </span>
            <span class="text-lg font-semibold tracking-tight">BantuIn</span>
          </a>
          <nav class="hidden md:flex items-center gap-8 text-sm text-ink-700">
            <a class="hover:text-ink-900" href="/kampanye">Kampanye</a>
            <a class="hover:text-ink-900" href="/#tentang">Tentang Kami</a>
            <a class="font-semibold text-ink-900 border-b-2 border-brand-700 pb-0.5" href="/dampak">Dampak Kami</a>
            <a class="hover:text-ink-900" href="/#kontak">Kontak</a>
          </nav>
          <div class="flex items-center gap-2">
            <button id="btnLogin" type="button" data-open-auth class="hidden sm:inline-flex h-10 items-center justify-center rounded-full px-5 text-sm font-medium text-ink-700 hover:bg-ink-50">Masuk</button>
            <div id="userMenuWrapper" class="relative hidden">
              <button id="userMenuButton" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 text-brand-800 ring-1 ring-brand-100" type="button">
                <span id="userInitial" class="text-sm font-semibold">?</span>
              </button>
              <div id="userMenu" class="absolute right-0 mt-2 w-64 overflow-hidden rounded-2xl border border-ink-100 bg-white shadow-soft hidden" role="menu">
                <div class="px-4 py-3">
                  <div id="menuUserName" class="text-sm font-semibold"></div>
                  <div id="menuUserEmail" class="text-xs text-ink-600"></div>
                  <div id="menuUserRole" class="mt-1 text-xs text-brand-700 font-semibold"></div>
                </div>
                <div class="border-t border-ink-100"></div>
                <a id="menuDashboardLink" href="#" class="flex items-center gap-3 px-4 py-3 text-sm text-ink-700 hover:bg-ink-50">
                  <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-ink-50"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 13h7V4H4v9Zm0 7h7v-5H4v5Zm9 0h7V11h-7v9Zm0-18v5h7V2h-7Z" fill="currentColor"/></svg></span>Dashboard
                </a>
                <div class="border-t border-ink-100"></div>
                <button type="button" onclick="logout()" class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm text-ink-700 hover:bg-ink-50">
                  <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-ink-50"><svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M10 17l1.4-1.4L8.8 13H20v-2H8.8l2.6-2.6L10 7l-7 7 7 7Z" fill="currentColor"/></svg></span>Keluar
                </button>
              </div>
            </div>
            <a href="/kampanye" class="inline-flex h-10 items-center justify-center rounded-full bg-brand-700 px-5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800">Donasi Sekarang</a>
            <button id="btnMobileMenu" class="md:hidden inline-flex h-10 w-10 items-center justify-center rounded-full hover:bg-ink-50" type="button">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
          </div>
        </div>
        <div id="mobileMenu" class="md:hidden hidden pb-4">
          <div class="flex flex-col gap-1 rounded-2xl border border-ink-100 bg-white p-3">
            <a class="rounded-xl px-3 py-2 text-sm text-ink-700 hover:bg-ink-50" href="/kampanye">Kampanye</a>
            <a class="rounded-xl px-3 py-2 text-sm text-ink-700 hover:bg-ink-50" href="/#tentang">Tentang Kami</a>
            <a class="rounded-xl px-3 py-2 text-sm font-semibold text-ink-900 bg-ink-50" href="/dampak">Dampak Kami</a>
            <a class="rounded-xl px-3 py-2 text-sm text-ink-700 hover:bg-ink-50" href="/#kontak">Kontak</a>
          </div>
        </div>
      </div>
    </header>

    <!-- ═══ HERO ═══ -->
    <section class="relative overflow-hidden bg-ink-900 py-20 sm:py-28">
      <div class="absolute inset-0 opacity-20" style="background-image:url('https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?auto=format&fit=crop&w=2400&q=40');background-size:cover;background-position:center;"></div>
      <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
          <span class="inline-flex rounded-full bg-brand-700/30 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-brand-300">Dampak Nyata</span>
          <h1 class="mt-5 text-4xl sm:text-5xl lg:text-6xl font-semibold tracking-tight text-white">Setiap Donasi Anda <span class="text-brand-400">Mengubah Nyata</span></h1>
          <p class="mt-5 text-base sm:text-lg text-white/70 max-w-2xl">Sejak 2020, bersama jutaan donatur, BantuIn telah menggerakkan perubahan yang terukur dan transparan di seluruh Indonesia.</p>
          <div class="mt-8 flex flex-col sm:flex-row gap-3">
            <a href="/kampanye" class="inline-flex h-11 items-center justify-center rounded-full bg-brand-700 px-7 text-sm font-semibold text-white hover:bg-brand-800">Mulai Berdonasi</a>
            <a href="#cerita" class="inline-flex h-11 items-center justify-center rounded-full border border-white/30 bg-white/10 px-7 text-sm font-semibold text-white hover:bg-white/15">Lihat Cerita Nyata</a>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ STAT COUNTER STRIP ═══ -->
    <section class="border-b border-ink-100 bg-white">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-ink-100">
          <div class="px-6 py-8 text-center">
            <div class="count-num text-3xl sm:text-4xl font-semibold text-brand-700" id="cnt1">0</div>
            <div class="mt-2 text-sm text-ink-600">Dana Terkumpul</div>
            <div class="mt-0.5 text-xs text-ink-400">Sejak 2020</div>
          </div>
          <div class="px-6 py-8 text-center">
            <div class="count-num text-3xl sm:text-4xl font-semibold text-ink-900" id="cnt2">0</div>
            <div class="mt-2 text-sm text-ink-600">Kampanye Sukses</div>
            <div class="mt-0.5 text-xs text-ink-400">Terverifikasi</div>
          </div>
          <div class="px-6 py-8 text-center">
            <div class="count-num text-3xl sm:text-4xl font-semibold text-ink-900" id="cnt3">0</div>
            <div class="mt-2 text-sm text-ink-600">Donatur Aktif</div>
            <div class="mt-0.5 text-xs text-ink-400">Di seluruh Indonesia</div>
          </div>
          <div class="px-6 py-8 text-center">
            <div class="count-num text-3xl sm:text-4xl font-semibold text-ink-900" id="cnt4">0</div>
            <div class="mt-2 text-sm text-ink-600">Penerima Manfaat</div>
            <div class="mt-0.5 text-xs text-ink-400">Langsung tersentuh</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ DAMPAK PER KATEGORI ═══ -->
    <section class="py-16 sm:py-20 bg-ink-50">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl sm:text-4xl font-semibold tracking-tight">Dampak per Bidang</h2>
          <p class="mt-3 text-ink-600">Kontribusi nyata di 6 bidang utama yang kami fokuskan</p>
        </div>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          <!-- Pendidikan -->
          <div class="card-hover fade-up rounded-3xl bg-white p-6 border border-ink-100 shadow-sm">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 3 2 8l10 5 10-5-10-5ZM2 17l10 5 10-5M2 12l10 5 10-5" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h3 class="mt-4 text-base font-semibold">Pendidikan</h3>
            <p class="mt-1.5 text-sm text-ink-600">Akses pendidikan berkualitas dari SD hingga perguruan tinggi.</p>
            <div class="mt-5 space-y-3">
              <div><div class="flex justify-between text-xs mb-1"><span class="text-ink-600">Beasiswa diberikan</span><span class="font-semibold text-blue-700">12.400+</span></div><div class="h-2 rounded-full bg-ink-100"><div class="h-2 rounded-full bg-blue-500 bar-fill" style="--target-w:82%; width:82%"></div></div></div>
              <div><div class="flex justify-between text-xs mb-1"><span class="text-ink-600">Sekolah dibantu</span><span class="font-semibold text-blue-700">340+</span></div><div class="h-2 rounded-full bg-ink-100"><div class="h-2 rounded-full bg-blue-300 bar-fill" style="--target-w:57%; width:57%"></div></div></div>
              <div><div class="flex justify-between text-xs mb-1"><span class="text-ink-600">Buku disalurkan</span><span class="font-semibold text-blue-700">85.000+</span></div><div class="h-2 rounded-full bg-ink-100"><div class="h-2 rounded-full bg-blue-200 bar-fill" style="--target-w:93%; width:93%"></div></div></div>
            </div>
            <div class="mt-5 pt-4 border-t border-ink-100 flex justify-between text-xs text-ink-500">
              <span>238 kampanye selesai</span><span class="text-blue-700 font-semibold">Rp 1,2M dikumpulkan</span>
            </div>
          </div>

          <!-- Kesehatan -->
          <div class="card-hover fade-up rounded-3xl bg-white p-6 border border-ink-100 shadow-sm">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-green-50">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M22 12h-4l-3 9L9 3l-3 9H2" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h3 class="mt-4 text-base font-semibold">Kesehatan</h3>
            <p class="mt-1.5 text-sm text-ink-600">Layanan kesehatan gratis bagi yang membutuhkan.</p>
            <div class="mt-5 space-y-3">
              <div><div class="flex justify-between text-xs mb-1"><span class="text-ink-600">Pasien dibantu</span><span class="font-semibold text-green-700">8.900+</span></div><div class="h-2 rounded-full bg-ink-100"><div class="h-2 rounded-full bg-green-500 bar-fill" style="--target-w:74%; width:74%"></div></div></div>
              <div><div class="flex justify-between text-xs mb-1"><span class="text-ink-600">Operasi gratis</span><span class="font-semibold text-green-700">2.100+</span></div><div class="h-2 rounded-full bg-ink-100"><div class="h-2 rounded-full bg-green-400 bar-fill" style="--target-w:61%; width:61%"></div></div></div>
              <div><div class="flex justify-between text-xs mb-1"><span class="text-ink-600">Klinik dibantu</span><span class="font-semibold text-green-700">120+</span></div><div class="h-2 rounded-full bg-ink-100"><div class="h-2 rounded-full bg-green-200 bar-fill" style="--target-w:40%; width:40%"></div></div></div>
            </div>
            <div class="mt-5 pt-4 border-t border-ink-100 flex justify-between text-xs text-ink-500">
              <span>185 kampanye selesai</span><span class="text-green-700 font-semibold">Rp 980jt dikumpulkan</span>
            </div>
          </div>

          <!-- Air Bersih -->
          <div class="card-hover fade-up rounded-3xl bg-white p-6 border border-ink-100 shadow-sm">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-cyan-50">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 2C6.48 2 2 7.52 2 12c0 3.5 2 6.58 5 8.13V20h10v-.87c3-1.55 5-4.63 5-8.13C22 7.52 17.52 2 12 2z" stroke="#0891b2" stroke-width="2"/></svg>
            </div>
            <h3 class="mt-4 text-base font-semibold">Air Bersih</h3>
            <p class="mt-1.5 text-sm text-ink-600">Instalasi air bersih di daerah yang kekurangan akses.</p>
            <div class="mt-5 space-y-3">
              <div><div class="flex justify-between text-xs mb-1"><span class="text-ink-600">Desa terlayani</span><span class="font-semibold text-cyan-700">312+</span></div><div class="h-2 rounded-full bg-ink-100"><div class="h-2 rounded-full bg-cyan-500 bar-fill" style="--target-w:78%; width:78%"></div></div></div>
              <div><div class="flex justify-between text-xs mb-1"><span class="text-ink-600">Sumur bor dibangun</span><span class="font-semibold text-cyan-700">890+</span></div><div class="h-2 rounded-full bg-ink-100"><div class="h-2 rounded-full bg-cyan-400 bar-fill" style="--target-w:65%; width:65%"></div></div></div>
              <div><div class="flex justify-between text-xs mb-1"><span class="text-ink-600">Warga terdampak</span><span class="font-semibold text-cyan-700">156K+</span></div><div class="h-2 rounded-full bg-ink-100"><div class="h-2 rounded-full bg-cyan-200 bar-fill" style="--target-w:88%; width:88%"></div></div></div>
            </div>
            <div class="mt-5 pt-4 border-t border-ink-100 flex justify-between text-xs text-ink-500">
              <span>112 kampanye selesai</span><span class="text-cyan-700 font-semibold">Rp 750jt dikumpulkan</span>
            </div>
          </div>

          <!-- Bencana Alam -->
          <div class="card-hover fade-up rounded-3xl bg-white p-6 border border-ink-100 shadow-sm">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-red-50">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 2L2 7l10 5 10-5-10-5ZM2 17l10 5 10-5" stroke="#dc2626" stroke-width="2" stroke-linecap="round"/><path d="M2 12l10 5 10-5" stroke="#dc2626" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <h3 class="mt-4 text-base font-semibold">Bencana Alam</h3>
            <p class="mt-1.5 text-sm text-ink-600">Respons cepat dan pemulihan pasca bencana.</p>
            <div class="mt-5 space-y-3">
              <div><div class="flex justify-between text-xs mb-1"><span class="text-ink-600">Korban dibantu</span><span class="font-semibold text-red-700">45.200+</span></div><div class="h-2 rounded-full bg-ink-100"><div class="h-2 rounded-full bg-red-500 bar-fill" style="--target-w:95%; width:95%"></div></div></div>
              <div><div class="flex justify-between text-xs mb-1"><span class="text-ink-600">Ton logistik disalurkan</span><span class="font-semibold text-red-700">820+</span></div><div class="h-2 rounded-full bg-ink-100"><div class="h-2 rounded-full bg-red-400 bar-fill" style="--target-w:72%; width:72%"></div></div></div>
              <div><div class="flex justify-between text-xs mb-1"><span class="text-ink-600">Rumah direhab</span><span class="font-semibold text-red-700">3.400+</span></div><div class="h-2 rounded-full bg-ink-100"><div class="h-2 rounded-full bg-red-200 bar-fill" style="--target-w:56%; width:56%"></div></div></div>
            </div>
            <div class="mt-5 pt-4 border-t border-ink-100 flex justify-between text-xs text-ink-500">
              <span>97 kampanye selesai</span><span class="text-red-700 font-semibold">Rp 1,5M dikumpulkan</span>
            </div>
          </div>

          <!-- Lingkungan -->
          <div class="card-hover fade-up rounded-3xl bg-white p-6 border border-ink-100 shadow-sm">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M17 8C8 10 5.9 16.17 3.82 19.17L5.26 20l.29-.5c.5-.87 1.13-1.67 1.87-2.34M17 8c0-1.5-.5-2.5-1-3-1 3-3 4-4.5 4.5-2.5 1-4 3.5-4.5 5.5" stroke="#059669" stroke-width="2" stroke-linecap="round"/></svg>
            </div>
            <h3 class="mt-4 text-base font-semibold">Lingkungan</h3>
            <p class="mt-1.5 text-sm text-ink-600">Menghijaukan dan menjaga ekosistem Indonesia.</p>
            <div class="mt-5 space-y-3">
              <div><div class="flex justify-between text-xs mb-1"><span class="text-ink-600">Pohon ditanam</span><span class="font-semibold text-emerald-700">250K+</span></div><div class="h-2 rounded-full bg-ink-100"><div class="h-2 rounded-full bg-emerald-500 bar-fill" style="--target-w:84%; width:84%"></div></div></div>
              <div><div class="flex justify-between text-xs mb-1"><span class="text-ink-600">Ha lahan dipulihkan</span><span class="font-semibold text-emerald-700">1.200+</span></div><div class="h-2 rounded-full bg-ink-100"><div class="h-2 rounded-full bg-emerald-400 bar-fill" style="--target-w:50%; width:50%"></div></div></div>
              <div><div class="flex justify-between text-xs mb-1"><span class="text-ink-600">Panel surya dipasang</span><span class="font-semibold text-emerald-700">480+</span></div><div class="h-2 rounded-full bg-ink-100"><div class="h-2 rounded-full bg-emerald-200 bar-fill" style="--target-w:32%; width:32%"></div></div></div>
            </div>
            <div class="mt-5 pt-4 border-t border-ink-100 flex justify-between text-xs text-ink-500">
              <span>144 kampanye selesai</span><span class="text-emerald-700 font-semibold">Rp 620jt dikumpulkan</span>
            </div>
          </div>

          <!-- Komunitas -->
          <div class="card-hover fade-up rounded-3xl bg-white p-6 border border-ink-100 shadow-sm">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-purple-50">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" stroke="#9333ea" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h3 class="mt-4 text-base font-semibold">Komunitas</h3>
            <p class="mt-1.5 text-sm text-ink-600">Pemberdayaan dan penguatan komunitas lokal.</p>
            <div class="mt-5 space-y-3">
              <div><div class="flex justify-between text-xs mb-1"><span class="text-ink-600">Lansia dibantu</span><span class="font-semibold text-purple-700">9.800+</span></div><div class="h-2 rounded-full bg-ink-100"><div class="h-2 rounded-full bg-purple-500 bar-fill" style="--target-w:69%; width:69%"></div></div></div>
              <div><div class="flex justify-between text-xs mb-1"><span class="text-ink-600">UMKM didukung</span><span class="font-semibold text-purple-700">2.300+</span></div><div class="h-2 rounded-full bg-ink-100"><div class="h-2 rounded-full bg-purple-400 bar-fill" style="--target-w:48%; width:48%"></div></div></div>
              <div><div class="flex justify-between text-xs mb-1"><span class="text-ink-600">Desa internet</span><span class="font-semibold text-purple-700">78+</span></div><div class="h-2 rounded-full bg-ink-100"><div class="h-2 rounded-full bg-purple-200 bar-fill" style="--target-w:26%; width:26%"></div></div></div>
            </div>
            <div class="mt-5 pt-4 border-t border-ink-100 flex justify-between text-xs text-ink-500">
              <span>193 kampanye selesai</span><span class="text-purple-700 font-semibold">Rp 890jt dikumpulkan</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ TREN DONASI BULANAN ═══ -->
    <section class="py-16 sm:py-20 bg-white">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-[1.4fr_1fr] items-center">
          <div>
            <h2 class="text-3xl sm:text-4xl font-semibold tracking-tight">Tren Donasi 2024</h2>
            <p class="mt-3 text-ink-600">Pertumbuhan donasi bulanan sepanjang tahun 2024 yang terus meningkat.</p>
            <!-- bar chart manual -->
            <div class="mt-8" id="barChart"></div>
            <p class="mt-3 text-xs text-ink-400">*dalam juta rupiah · data 2024</p>
          </div>
          <div class="space-y-5">
            <div class="rounded-3xl bg-ink-50 p-6">
              <div class="text-xs font-semibold uppercase tracking-widest text-ink-500">Bulan Terbaik</div>
              <div class="mt-2 text-2xl font-semibold text-ink-900">Desember 2024</div>
              <div class="mt-1 text-sm text-ink-600">Rp 920jt — tertinggi sepanjang sejarah BantuIn</div>
            </div>
            <div class="rounded-3xl bg-ink-50 p-6">
              <div class="text-xs font-semibold uppercase tracking-widest text-ink-500">Rata-rata Donasi</div>
              <div class="mt-2 text-2xl font-semibold text-ink-900">Rp 324.000</div>
              <div class="mt-1 text-sm text-ink-600">Per transaksi di 2024, naik 18% dari 2023</div>
            </div>
            <div class="rounded-3xl bg-brand-50 p-6 border border-brand-100">
              <div class="text-xs font-semibold uppercase tracking-widest text-brand-700">Pertumbuhan YoY</div>
              <div class="mt-2 text-2xl font-semibold text-brand-700">+142%</div>
              <div class="mt-1 text-sm text-ink-600">Dibanding total dana 2023</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ PETA SEBARAN ═══ -->
    <section class="py-16 sm:py-20 bg-ink-50">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
          <h2 class="text-3xl sm:text-4xl font-semibold tracking-tight">Jangkauan Kami</h2>
          <p class="mt-3 text-ink-600">BantuIn telah menyentuh kehidupan di seluruh nusantara</p>
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
          <div class="rounded-3xl bg-white border border-ink-100 shadow-sm p-6 text-center card-hover">
            <div class="text-3xl font-semibold text-brand-700">34</div>
            <div class="mt-2 text-sm font-medium text-ink-900">Provinsi</div>
            <div class="mt-1 text-xs text-ink-500">Dijangkau 100%</div>
          </div>
          <div class="rounded-3xl bg-white border border-ink-100 shadow-sm p-6 text-center card-hover">
            <div class="text-3xl font-semibold text-ink-900">480+</div>
            <div class="mt-2 text-sm font-medium text-ink-900">Kabupaten/Kota</div>
            <div class="mt-1 text-xs text-ink-500">Ada penerima manfaat</div>
          </div>
          <div class="rounded-3xl bg-white border border-ink-100 shadow-sm p-6 text-center card-hover">
            <div class="text-3xl font-semibold text-ink-900">3.200+</div>
            <div class="mt-2 text-sm font-medium text-ink-900">Kecamatan</div>
            <div class="mt-1 text-xs text-ink-500">Terjangkau bantuan</div>
          </div>
          <div class="rounded-3xl bg-white border border-ink-100 shadow-sm p-6 text-center card-hover">
            <div class="text-3xl font-semibold text-ink-900">12.400+</div>
            <div class="mt-2 text-sm font-medium text-ink-900">Desa</div>
            <div class="mt-1 text-xs text-ink-500">Tersentuh program</div>
          </div>
        </div>
        <!-- visual map placeholder -->
        <div class="mt-8 rounded-3xl bg-white border border-ink-100 shadow-sm overflow-hidden">
          <div class="relative" style="padding-bottom:38%; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
            <div class="absolute inset-0 flex items-center justify-center">
              <svg viewBox="0 0 800 300" class="w-full h-full opacity-30" fill="none" xmlns="http://www.w3.org/2000/svg">
                <ellipse cx="400" cy="150" rx="380" ry="120" stroke="white" stroke-width="0.5" stroke-dasharray="4 4"/>
                <ellipse cx="400" cy="150" rx="280" ry="90" stroke="white" stroke-width="0.5" stroke-dasharray="4 4"/>
                <line x1="20" y1="150" x2="780" y2="150" stroke="white" stroke-width="0.3" stroke-dasharray="4 8"/>
                <line x1="400" y1="30" x2="400" y2="270" stroke="white" stroke-width="0.3" stroke-dasharray="4 8"/>
              </svg>
              <!-- map dots for major cities -->
              <div class="absolute inset-0">
                <!-- Sumatera area -->
                <div class="map-dot absolute h-3 w-3 rounded-full bg-brand-400" style="left:18%;top:38%"></div>
                <div class="map-dot absolute h-2 w-2 rounded-full bg-brand-300" style="left:22%;top:55%;animation-delay:.4s"></div>
                <div class="map-dot absolute h-2.5 w-2.5 rounded-full bg-brand-400" style="left:26%;top:45%;animation-delay:.2s"></div>
                <!-- Jawa area -->
                <div class="map-dot absolute h-4 w-4 rounded-full bg-brand-500" style="left:40%;top:62%"></div>
                <div class="map-dot absolute h-3 w-3 rounded-full bg-brand-400" style="left:45%;top:58%;animation-delay:.6s"></div>
                <div class="map-dot absolute h-2 w-2 rounded-full bg-brand-300" style="left:50%;top:64%;animation-delay:.3s"></div>
                <!-- Kalimantan -->
                <div class="map-dot absolute h-3 w-3 rounded-full bg-brand-400" style="left:55%;top:35%;animation-delay:.8s"></div>
                <div class="map-dot absolute h-2 w-2 rounded-full bg-brand-300" style="left:60%;top:50%;animation-delay:.1s"></div>
                <!-- Sulawesi -->
                <div class="map-dot absolute h-2.5 w-2.5 rounded-full bg-brand-400" style="left:68%;top:38%;animation-delay:.5s"></div>
                <div class="map-dot absolute h-2 w-2 rounded-full bg-brand-300" style="left:72%;top:48%;animation-delay:.7s"></div>
                <!-- Papua -->
                <div class="map-dot absolute h-3 w-3 rounded-full bg-brand-400" style="left:82%;top:50%;animation-delay:.9s"></div>
                <div class="map-dot absolute h-2 w-2 rounded-full bg-brand-300" style="left:88%;top:42%;animation-delay:.2s"></div>
              </div>
              <div class="absolute bottom-4 left-0 right-0 text-center">
                <p class="text-white/60 text-xs">Peta Sebaran Penerima Manfaat BantuIn di Seluruh Indonesia</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ CERITA NYATA ═══ -->
    <section id="cerita" class="py-16 sm:py-20 bg-white">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl sm:text-4xl font-semibold tracking-tight">Cerita Nyata Penerima Manfaat</h2>
          <p class="mt-3 text-ink-600">Wajah-wajah di balik angka yang kami capai bersama</p>
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          <article class="card-hover rounded-3xl overflow-hidden border border-ink-100 bg-white shadow-sm">
            <div class="h-52 bg-ink-100 overflow-hidden">
              <img src="https://images.unsplash.com/photo-1497486751825-1233686d5d80?auto=format&fit=crop&w=800&q=60" alt="Siti" class="w-full h-full object-cover"/>
            </div>
            <div class="p-6">
              <span class="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">Pendidikan</span>
              <h3 class="mt-3 text-base font-semibold">Siti Rahayu, 17 tahun</h3>
              <p class="mt-2 text-sm text-ink-600 leading-relaxed">"Berkat beasiswa BantuIn, saya bisa melanjutkan ke SMA terbaik di kota. Impian kuliah saya kini semakin dekat. Terima kasih kepada semua donatur yang peduli."</p>
              <div class="mt-4 flex items-center gap-2">
                <div class="h-1.5 w-1.5 rounded-full bg-brand-700"></div>
                <span class="text-xs text-ink-500">Pelalawan, Riau · Penerima 2023</span>
              </div>
            </div>
          </article>
          <article class="card-hover rounded-3xl overflow-hidden border border-ink-100 bg-white shadow-sm">
            <div class="h-52 bg-ink-100 overflow-hidden">
              <img src="https://images.unsplash.com/photo-1559757148-5c350d0d3c56?auto=format&fit=crop&w=800&q=60" alt="Pak Burhan" class="w-full h-full object-cover"/>
            </div>
            <div class="p-6">
              <span class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">Kesehatan</span>
              <h3 class="mt-3 text-base font-semibold">Pak Burhan, 52 tahun</h3>
              <p class="mt-2 text-sm text-ink-600 leading-relaxed">"Operasi jantung yang biayanya ratusan juta ditanggung sepenuhnya. Saya tidak menyangka ada orang-orang yang mau membantu orang asing seperti saya."</p>
              <div class="mt-4 flex items-center gap-2">
                <div class="h-1.5 w-1.5 rounded-full bg-brand-700"></div>
                <span class="text-xs text-ink-500">Makassar, Sulsel · Penerima 2024</span>
              </div>
            </div>
          </article>
          <article class="card-hover rounded-3xl overflow-hidden border border-ink-100 bg-white shadow-sm">
            <div class="h-52 bg-ink-100 overflow-hidden">
              <img src="https://images.unsplash.com/photo-1509099836639-18ba1795216d?auto=format&fit=crop&w=800&q=60" alt="Desa Wulukan" class="w-full h-full object-cover"/>
            </div>
            <div class="p-6">
              <span class="inline-flex rounded-full bg-cyan-50 px-3 py-1 text-xs font-semibold text-cyan-700">Air Bersih</span>
              <h3 class="mt-3 text-base font-semibold">Desa Wulukan, NTT</h3>
              <p class="mt-2 text-sm text-ink-600 leading-relaxed">"Dulu kami harus berjalan 4 km untuk mendapat air. Kini ada sumur bor di tengah desa. 800 warga kami akhirnya punya akses air bersih setiap hari."</p>
              <div class="mt-4 flex items-center gap-2">
                <div class="h-1.5 w-1.5 rounded-full bg-brand-700"></div>
                <span class="text-xs text-ink-500">Ende, NTT · Penerima 2024</span>
              </div>
            </div>
          </article>
        </div>
      </div>
    </section>

    <!-- ═══ TIMELINE PERJALANAN ═══ -->
    <section class="py-16 sm:py-20 bg-ink-50">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-12 lg:grid-cols-2 items-start">
          <div>
            <h2 class="text-3xl sm:text-4xl font-semibold tracking-tight">Perjalanan Dampak Kami</h2>
            <p class="mt-3 text-ink-600">Dari awal berdiri hingga kini, setiap tahun membawa pencapaian baru.</p>
            <div class="mt-10 relative">
              <!-- timeline items -->
              <div class="space-y-8">
                <div class="flex gap-5 relative timeline-line">
                  <div class="flex-shrink-0 h-10 w-10 rounded-full bg-brand-700 flex items-center justify-center text-white text-sm font-bold z-10">20</div>
                  <div class="pb-8">
                    <div class="text-xs font-semibold uppercase tracking-widest text-brand-700">2020 — Berdiri</div>
                    <h3 class="mt-1 text-sm font-semibold">Kampanye pertama berhasil</h3>
                    <p class="mt-1 text-sm text-ink-600">BantuIn lahir dengan 1 kampanye dan 47 donatur pertama. Dana terkumpul Rp 12jt untuk korban banjir Jakarta.</p>
                  </div>
                </div>
                <div class="flex gap-5 relative">
                  <div class="flex-shrink-0 h-10 w-10 rounded-full bg-ink-900 flex items-center justify-center text-white text-sm font-bold">21</div>
                  <div class="pb-8">
                    <div class="text-xs font-semibold uppercase tracking-widest text-ink-500">2021 — Tumbuh</div>
                    <h3 class="mt-1 text-sm font-semibold">100 kampanye, 10.000 donatur</h3>
                    <p class="mt-1 text-sm text-ink-600">Pandemi mendorong gelombang donasi solidaritas. BantuIn menjadi platform respons COVID terpercaya.</p>
                  </div>
                </div>
                <div class="flex gap-5 relative">
                  <div class="flex-shrink-0 h-10 w-10 rounded-full bg-ink-900 flex items-center justify-center text-white text-sm font-bold">22</div>
                  <div class="pb-8">
                    <div class="text-xs font-semibold uppercase tracking-widest text-ink-500">2022 — Ekspansi</div>
                    <h3 class="mt-1 text-sm font-semibold">Rp 1M pertama terkumpul</h3>
                    <p class="mt-1 text-sm text-ink-600">Milestone besar: Rp 1 miliar total donasi. Jangkauan meluas ke 25 provinsi.</p>
                  </div>
                </div>
                <div class="flex gap-5 relative">
                  <div class="flex-shrink-0 h-10 w-10 rounded-full bg-ink-900 flex items-center justify-center text-white text-sm font-bold">23</div>
                  <div class="pb-8">
                    <div class="text-xs font-semibold uppercase tracking-widest text-ink-500">2023 — Matang</div>
                    <h3 class="mt-1 text-sm font-semibold">Verifikasi & transparansi penuh</h3>
                    <p class="mt-1 text-sm text-ink-600">Sistem audit kampanye diluncurkan. 98% dana tersalurkan langsung ke penerima.</p>
                  </div>
                </div>
                <div class="flex gap-5">
                  <div class="flex-shrink-0 h-10 w-10 rounded-full bg-brand-700 flex items-center justify-center text-white text-sm font-bold">24</div>
                  <div>
                    <div class="text-xs font-semibold uppercase tracking-widest text-brand-700">2024 — Saat Ini</div>
                    <h3 class="mt-1 text-sm font-semibold">Rp 5M+ & 50.000 donatur aktif</h3>
                    <p class="mt-1 text-sm text-ink-600">BantuIn kini menjadi platform donasi digital terbesar dan terpercaya di Indonesia.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Transparansi -->
          <div class="space-y-5">
            <h3 class="text-xl font-semibold">Transparansi Dana</h3>
            <p class="text-sm text-ink-600">Kami berkomitmen untuk melaporkan penggunaan setiap rupiah yang dipercayakan donatur kepada kami.</p>
            <div class="rounded-3xl bg-white border border-ink-100 shadow-sm p-6 space-y-4">
              <div>
                <div class="flex justify-between text-sm mb-2"><span class="font-medium">Langsung ke Penerima</span><span class="font-semibold text-green-600">98%</span></div>
                <div class="h-3 rounded-full bg-ink-100"><div class="h-3 rounded-full bg-green-500 bar-fill" style="--target-w:98%; width:98%"></div></div>
              </div>
              <div>
                <div class="flex justify-between text-sm mb-2"><span class="font-medium">Operasional Platform</span><span class="font-semibold text-blue-600">1.5%</span></div>
                <div class="h-3 rounded-full bg-ink-100"><div class="h-3 rounded-full bg-blue-400 bar-fill" style="--target-w:1.5%; width:1.5%"></div></div>
              </div>
              <div>
                <div class="flex justify-between text-sm mb-2"><span class="font-medium">Audit & Verifikasi</span><span class="font-semibold text-purple-600">0.5%</span></div>
                <div class="h-3 rounded-full bg-ink-100"><div class="h-3 rounded-full bg-purple-400 bar-fill" style="--target-w:0.5%; width:0.5%"></div></div>
              </div>
            </div>
            <div class="rounded-3xl bg-ink-900 p-6 text-white">
              <h4 class="font-semibold">Penghargaan & Sertifikasi</h4>
              <ul class="mt-3 space-y-2 text-sm text-white/80">
                <li class="flex items-center gap-2"><span class="text-brand-400">✓</span> ISO 9001:2015 Manajemen Mutu</li>
                <li class="flex items-center gap-2"><span class="text-brand-400">✓</span> Terdaftar di Kemensos RI</li>
                <li class="flex items-center gap-2"><span class="text-brand-400">✓</span> Platform Terpercaya 2023 — Kominfo</li>
                <li class="flex items-center gap-2"><span class="text-brand-400">✓</span> Top 10 Social Impact Startup 2024</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ TESTIMONI DONATUR ═══ -->
    <section class="py-16 sm:py-20 bg-white">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
          <h2 class="text-3xl sm:text-4xl font-semibold tracking-tight">Apa Kata Donatur Kami</h2>
          <p class="mt-3 text-ink-600">Kepercayaan Anda adalah kekuatan terbesar kami</p>
        </div>
        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          <blockquote class="card-hover rounded-3xl border border-ink-100 bg-ink-50 p-6 shadow-sm">
            <div class="flex gap-1 mb-4">
              <span class="text-brand-700">★★★★★</span>
            </div>
            <p class="text-sm text-ink-700 leading-relaxed">"Saya sudah 3 tahun berdonasi lewat BantuIn. Setiap kampanye selalu ada laporan hasil yang transparan. Saya bisa lihat dampak nyata dari setiap rupiah yang saya sumbangkan."</p>
            <div class="mt-5 flex items-center gap-3">
              <div class="h-10 w-10 rounded-full bg-brand-700 flex items-center justify-center text-white font-bold flex-shrink-0">A</div>
              <div><div class="text-sm font-semibold">Andi Prasetyo</div><div class="text-xs text-ink-500">Donatur Setia · Jakarta</div></div>
            </div>
          </blockquote>
          <blockquote class="card-hover rounded-3xl border border-ink-100 bg-ink-50 p-6 shadow-sm">
            <div class="flex gap-1 mb-4">
              <span class="text-brand-700">★★★★★</span>
            </div>
            <p class="text-sm text-ink-700 leading-relaxed">"Sebagai ibu rumah tangga, saya bisa ikut berdonasi walau nominalnya kecil. BantuIn membuktikan bahwa semua kontribusi berarti, tidak peduli besar kecilnya."</p>
            <div class="mt-5 flex items-center gap-3">
              <div class="h-10 w-10 rounded-full bg-ink-900 flex items-center justify-center text-white font-bold flex-shrink-0">R</div>
              <div><div class="text-sm font-semibold">Ratna Dewi</div><div class="text-xs text-ink-500">Donatur · Surabaya</div></div>
            </div>
          </blockquote>
          <blockquote class="card-hover rounded-3xl border border-ink-100 bg-ink-50 p-6 shadow-sm">
            <div class="flex gap-1 mb-4">
              <span class="text-brand-700">★★★★★</span>
            </div>
            <p class="text-sm text-ink-700 leading-relaxed">"Tim BantuIn sangat responsif ketika saya bertanya soal kampanye yang ingin saya dukung. Platform yang benar-benar menghargai kepercayaan donatur."</p>
            <div class="mt-5 flex items-center gap-3">
              <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold flex-shrink-0">F</div>
              <div><div class="text-sm font-semibold">Fajar Nugroho</div><div class="text-xs text-ink-500">Donatur · Bandung</div></div>
            </div>
          </blockquote>
        </div>
      </div>
    </section>

    <!-- ═══ CTA ═══ -->
    <section class="py-16 bg-brand-700">
      <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl font-semibold text-white tracking-tight">Jadilah Bagian dari Perubahan</h2>
        <p class="mt-4 text-white/80">Setiap donasi Anda akan tercatat dan memberikan dampak nyata. Bersama kita bisa mengubah lebih banyak hidup.</p>
        <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
          <a href="/kampanye" class="inline-flex h-12 items-center justify-center rounded-full bg-white px-8 text-sm font-semibold text-brand-700 hover:bg-brand-50 shadow-soft">
            Mulai Berdonasi Sekarang
          </a>
          <button data-open-auth class="inline-flex h-12 items-center justify-center rounded-full border border-white/40 bg-white/10 px-8 text-sm font-semibold text-white hover:bg-white/20">
            Mulai Kampanye
          </button>
        </div>
      </div>
    </section>

    <!-- ═══ FOOTER ═══ -->
    <footer class="bg-ink-900 text-white py-12 sm:py-16">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
          <div>
            <div class="flex items-center gap-2">
              <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-brand-700"><svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21s-7.2-4.6-9.6-9C.1 7.2 3.2 3.8 7.1 4.1c1.7.1 3.2 1 3.9 2.2.7-1.2 2.2-2.1 3.9-2.2 3.9-.3 7 3.1 4.7 7.9C19.2 16.4 12 21 12 21z"/></svg></span>
              <span class="font-semibold">BantuIn</span>
            </div>
            <p class="mt-4 text-sm text-white/60">Platform donasi digital terpercaya untuk Indonesia yang lebih baik.</p>
          </div>
          <div><h3 class="text-sm font-semibold text-white/90">Platform</h3><ul class="mt-4 space-y-2 text-sm text-white/75"><li><a class="hover:text-white" href="/kampanye">Kampanye</a></li><li><a class="hover:text-white" href="/dampak">Dampak Kami</a></li><li><a class="hover:text-white" href="/#tentang">Tentang Kami</a></li></ul></div>
          <div><h3 class="text-sm font-semibold text-white/90">Terlibat</h3><ul class="mt-4 space-y-2 text-sm text-white/75"><li><a class="hover:text-white" href="/kampanye">Donasi</a></li><li><button data-open-auth class="hover:text-white">Mulai Kampanye</button></li></ul></div>
          <div><h3 class="text-sm font-semibold text-white/90">Kontak</h3><ul class="mt-4 space-y-2 text-sm text-white/75"><li><a class="hover:text-white" href="mailto:contact@bantuin.org">contact@bantuin.org</a></li><li>+62 812-3456-7890</li><li>Jakarta, Indonesia</li></ul></div>
        </div>
        <div class="mt-10 border-t border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
          <p class="text-xs text-white/60">© <span id="year"></span> BantuIn. All rights reserved.</p>
          <div class="flex gap-4 text-xs text-white/60"><a class="hover:text-white" href="#">Privasi</a><a class="hover:text-white" href="#">Syarat</a></div>
        </div>
      </div>
    </footer>

    <!-- AUTH MODAL -->
    <div id="authModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
      <div class="absolute inset-0 bg-black/55" data-close-auth></div>
      <div class="relative mx-auto flex min-h-screen max-w-3xl items-center justify-center p-4">
        <div class="w-full max-w-md rounded-3xl bg-white shadow-soft">
          <div class="flex items-center justify-between px-6 pt-6">
            <div><h3 class="text-base font-semibold">Selamat Datang di BantuIn</h3><p class="mt-1 text-sm text-ink-600">Masuk atau daftar untuk mulai membantu</p></div>
            <button class="inline-flex h-10 w-10 items-center justify-center rounded-full hover:bg-ink-50" data-close-auth type="button">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
          </div>
          <div class="px-6 pb-6">
            <div class="mt-5 rounded-full bg-ink-50 p-1"><div class="grid grid-cols-2 gap-1">
              <button id="tabLogin" class="h-10 rounded-full bg-white text-sm font-semibold text-ink-900 shadow-sm" type="button">Masuk</button>
              <button id="tabRegister" class="h-10 rounded-full text-sm font-semibold text-ink-700" type="button">Daftar</button>
            </div></div>
            <div id="authError" class="hidden mt-3 rounded-xl bg-red-50 px-4 py-2 text-sm text-red-700"></div>
            <div id="authSuccess" class="hidden mt-3 rounded-xl bg-green-50 px-4 py-2 text-sm text-green-700"></div>
            <form id="loginForm" class="mt-5 space-y-3">
              <div><label class="text-xs font-medium text-ink-700">Email</label><input id="loginEmail" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none" type="email" required/></div>
              <div><label class="text-xs font-medium text-ink-700">Password</label><input id="loginPassword" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none" type="password" required/></div>
              <button class="mt-2 inline-flex h-11 w-full items-center justify-center rounded-2xl bg-brand-700 text-sm font-semibold text-white hover:bg-brand-800" type="submit">Masuk</button>
            </form>
            <form id="registerForm" class="mt-5 space-y-3 hidden">
              <div><label class="text-xs font-medium text-ink-700">Nama Lengkap</label><input id="regName" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none" type="text" required/></div>
              <div><label class="text-xs font-medium text-ink-700">Email</label><input id="regEmail" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none" type="email" required/></div>
              <div><label class="text-xs font-medium text-ink-700">Peran</label><select id="regRole" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none"><option value="donatur">Donatur</option><option value="fundraiser">Fundraiser</option></select></div>
              <div><label class="text-xs font-medium text-ink-700">Password</label><input id="regPassword" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none" type="password" minlength="6" required/></div>
              <div><label class="text-xs font-medium text-ink-700">Konfirmasi Password</label><input id="regConfirm" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none" type="password" minlength="6" required/></div>
              <button class="mt-2 inline-flex h-11 w-full items-center justify-center rounded-2xl bg-brand-700 text-sm font-semibold text-white hover:bg-brand-800" type="submit">Daftar</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script>
    // ══════════════════════════════════════════
    // COUNTER ANIMATION
    // ══════════════════════════════════════════
    const counters = [
      { id:'cnt1', target:'Rp 5M+', numeric:5, suffix:'M+', prefix:'Rp ' },
      { id:'cnt2', target:'1.200+', numeric:1200, suffix:'+', prefix:'' },
      { id:'cnt3', target:'50K+', numeric:50, suffix:'K+', prefix:'' },
      { id:'cnt4', target:'280K+', numeric:280, suffix:'K+', prefix:'' },
    ];

    function animateCounter(el, prefix, end, suffix, duration) {
      let start = 0; const step = end / (duration / 16);
      const timer = setInterval(() => {
        start += step;
        if (start >= end) { start = end; clearInterval(timer); }
        el.textContent = prefix + Math.round(start).toLocaleString('id') + suffix;
      }, 16);
    }

    let counted = false;
    function checkCounters() {
      if (counted) return;
      const el = document.getElementById('cnt1');
      if (!el) return;
      const rect = el.getBoundingClientRect();
      if (rect.top < window.innerHeight) {
        counted = true;
        counters.forEach(c => {
          const el = document.getElementById(c.id);
          if (el) animateCounter(el, c.prefix, c.numeric, c.suffix, 2000);
        });
      }
    }

    // ══════════════════════════════════════════
    // BAR CHART (DONASI BULANAN)
    // ══════════════════════════════════════════
    const monthData = [
      {m:'Jan',v:320},{m:'Feb',v:280},{m:'Mar',v:410},{m:'Apr',v:390},{m:'Mei',v:480},
      {m:'Jun',v:520},{m:'Jul',v:470},{m:'Agu',v:610},{m:'Sep',v:580},{m:'Okt',v:690},{m:'Nov',v:750},{m:'Des',v:920},
    ];
    const maxV = Math.max(...monthData.map(d => d.v));

    function buildChart() {
      const container = document.getElementById('barChart');
      if (!container) return;
      container.innerHTML = `
        <div class="flex items-end gap-1.5 h-40">
          ${monthData.map((d,i) => {
            const pct = Math.round(d.v / maxV * 100);
            const isLast = i === monthData.length - 1;
            return `<div class="flex flex-col items-center gap-1 flex-1">
              <div class="w-full rounded-t-lg ${isLast ? 'bg-brand-700' : 'bg-ink-200'} transition-all duration-1000" style="height:${pct}%" title="${d.m}: Rp ${d.v}jt"></div>
              <span class="text-xs text-ink-400" style="font-size:10px">${d.m}</span>
            </div>`;
          }).join('')}
        </div>`;
    }

    // ══════════════════════════════════════════
    // AUTH SYSTEM
    // ══════════════════════════════════════════
    function seedDefaultAccounts() {
      const users = getUsers();
      if (!users.some(u => u.email === 'admin@bantuin.org'))
        users.push({ name:'Admin BantuIn', email:'admin@bantuin.org', password:'admin123', role:'admin' });
      saveUsers(users);
    }
    function getUsers() { return JSON.parse(localStorage.getItem('bantuin_users') || '[]'); }
    function saveUsers(u) { localStorage.setItem('bantuin_users', JSON.stringify(u)); }
    function getCurrentUser() { return JSON.parse(localStorage.getItem('bantuin_current_user') || 'null'); }
    function setCurrentUser(u) { localStorage.setItem('bantuin_current_user', JSON.stringify(u)); }
    function logout() { localStorage.removeItem('bantuin_current_user'); location.href = '/'; }
    function getDashboardUrl(role) { return role==='admin'?'/admin':role==='fundraiser'?'/fundraiser':'/donatur'; }

    async function loginFn(email, password) {
      try {
        const response = await fetch('/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ email, password })
        });
        return await response.json();
      } catch (e) {
        return { ok: false, msg: 'Terjadi kesalahan koneksi.' };
      }
    }
    async function registerFn(name, email, password, role) {
      try {
        const response = await fetch('/register', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ name, email, password, role })
        });
        return await response.json();
      } catch (e) {
        return { ok: false, msg: 'Terjadi kesalahan koneksi.' };
      }
    }

    function logout() {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = '/logout';
      const csrfInput = document.createElement('input');
      csrfInput.type = 'hidden';
      csrfInput.name = '_token';
      csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      form.appendChild(csrfInput);
      document.body.appendChild(form);
      form.submit();
    }

    function updateNavbar() {
      const user = getCurrentUser();
      const btnLogin = document.getElementById('btnLogin');
      const wrapper = document.getElementById('userMenuWrapper');
      if (user) {
        if (btnLogin) btnLogin.style.display = 'none';
        if (wrapper) { wrapper.classList.remove('hidden'); }
        const el = (id, txt) => { const e=document.getElementById(id); if(e) e.textContent=txt; };
        el('userInitial', user.name.charAt(0).toUpperCase());
        el('menuUserName', user.name); el('menuUserEmail', user.email);
        el('menuUserRole', user.role.charAt(0).toUpperCase()+user.role.slice(1));
        const dl = document.getElementById('menuDashboardLink'); if(dl) dl.href = getDashboardUrl(user.role);
      } else {
        if (btnLogin) btnLogin.style.display = '';
        if (wrapper) wrapper.classList.add('hidden');
      }
    }

    function openAuthModal() { document.getElementById('authModal').classList.remove('hidden'); document.body.style.overflow='hidden'; clearAuthMsg(); }
    function closeAuthModal() { document.getElementById('authModal').classList.add('hidden'); document.body.style.overflow=''; }
    function clearAuthMsg() { ['authError','authSuccess'].forEach(id => { const e=document.getElementById(id); if(e){e.classList.add('hidden');e.textContent='';} }); }
    function showErr(msg) { const e=document.getElementById('authError'); if(e){e.textContent=msg;e.classList.remove('hidden');} }
    function showOk(msg) { const e=document.getElementById('authSuccess'); if(e){e.textContent=msg;e.classList.remove('hidden');} }

    function setTab(tab) {
      clearAuthMsg();
      const isLogin = tab === 'login';
      document.getElementById('loginForm').classList.toggle('hidden', !isLogin);
      document.getElementById('registerForm').classList.toggle('hidden', isLogin);
      document.getElementById('tabLogin').className = 'h-10 rounded-full text-sm font-semibold ' + (isLogin ? 'bg-white text-ink-900 shadow-sm' : 'text-ink-700');
      document.getElementById('tabRegister').className = 'h-10 rounded-full text-sm font-semibold ' + (!isLogin ? 'bg-white text-ink-900 shadow-sm' : 'text-ink-700');
    }

    document.addEventListener('DOMContentLoaded', function() {
      seedDefaultAccounts();
      updateNavbar();
      buildChart();
      document.getElementById('year').textContent = new Date().getFullYear();

      window.addEventListener('scroll', checkCounters, { passive:true });
      checkCounters();

      document.querySelectorAll('[data-open-auth]').forEach(el => {
        el.addEventListener('click', function(e) {
          e.preventDefault();
          const user = getCurrentUser();
          if (user) window.location.href = getDashboardUrl(user.role);
          else openAuthModal();
        });
      });
      document.querySelectorAll('[data-close-auth]').forEach(el => el.addEventListener('click', closeAuthModal));
      document.getElementById('tabLogin').addEventListener('click', () => setTab('login'));
      document.getElementById('tabRegister').addEventListener('click', () => setTab('register'));

      document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault(); clearAuthMsg();
        const res = await loginFn(document.getElementById('loginEmail').value.trim(), document.getElementById('loginPassword').value);
        if (!res.ok) { showErr(res.msg); return; }
        showOk('Berhasil masuk! Mengalihkan...');
        setTimeout(() => { closeAuthModal(); updateNavbar(); window.location.href = getDashboardUrl(res.user.role); }, 800);
      });
      document.getElementById('registerForm').addEventListener('submit', async function(e) {
        e.preventDefault(); clearAuthMsg();
        const pw = document.getElementById('regPassword').value;
        if (pw !== document.getElementById('regConfirm').value) { showErr('Password tidak cocok.'); return; }
        const res = await registerFn(document.getElementById('regName').value.trim(), document.getElementById('regEmail').value.trim(), pw, document.getElementById('regRole').value);
        if (!res.ok) { showErr(res.msg); return; }
        showOk('Akun berhasil dibuat! Mengalihkan...');
        setTimeout(() => { closeAuthModal(); updateNavbar(); window.location.href = getDashboardUrl(res.user.role); }, 800);
      });

      const userMenuButton = document.getElementById('userMenuButton');
      const userMenu = document.getElementById('userMenu');
      if (userMenuButton && userMenu) {
        userMenuButton.addEventListener('click', e => { e.stopPropagation(); userMenu.classList.toggle('hidden'); });
        document.addEventListener('click', () => userMenu.classList.add('hidden'));
      }

      const mobileBtn = document.getElementById('btnMobileMenu');
      const mobileMenu = document.getElementById('mobileMenu');
      if (mobileBtn && mobileMenu) mobileBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));

      document.addEventListener('keydown', e => { if(e.key==='Escape') closeAuthModal(); });
    });
    </script>
  </body>
</html>
