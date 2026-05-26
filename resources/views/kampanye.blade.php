<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Kampanye — BantuIn</title>
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
      .card-hover { transition: box-shadow .22s ease, transform .22s ease; }
      .card-hover:hover { box-shadow: 0 10px 30px rgba(2,6,23,0.13); transform: translateY(-3px); }
      .progress-bar-fill { transition: width 1s ease; }
      @keyframes fadeUp { from { opacity:0; transform:translateY(18px); } to { opacity:1; transform:translateY(0); } }
      .fade-up { animation: fadeUp .55s ease forwards; }
      .fade-up:nth-child(1){animation-delay:.05s}
      .fade-up:nth-child(2){animation-delay:.12s}
      .fade-up:nth-child(3){animation-delay:.19s}
      .fade-up:nth-child(4){animation-delay:.26s}
      .fade-up:nth-child(5){animation-delay:.33s}
      .fade-up:nth-child(6){animation-delay:.40s}
      .badge { display:inline-flex; align-items:center; border-radius:9999px; padding:.2rem .65rem; font-size:.72rem; font-weight:600; }
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
            <a class="font-semibold text-ink-900 border-b-2 border-brand-700 pb-0.5" href="/kampanye">Kampanye</a>
            <a class="hover:text-ink-900" href="/#tentang">Tentang Kami</a>
            <a class="hover:text-ink-900" href="/dampak">Dampak Kami</a>
            <a class="hover:text-ink-900" href="/#kontak">Kontak</a>
          </nav>

          <div class="flex items-center gap-2">
            <button id="btnLogin" type="button" data-open-auth
              class="hidden sm:inline-flex h-10 items-center justify-center rounded-full px-5 text-sm font-medium text-ink-700 hover:bg-ink-50">
              Masuk
            </button>
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
                  <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-ink-50">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 13h7V4H4v9Zm0 7h7v-5H4v5Zm9 0h7V11h-7v9Zm0-18v5h7V2h-7Z" fill="currentColor"/></svg>
                  </span>Dashboard
                </a>
                <div class="border-t border-ink-100"></div>
                <button type="button" onclick="logout()" class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm text-ink-700 hover:bg-ink-50">
                  <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-ink-50">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M10 17l1.4-1.4L8.8 13H20v-2H8.8l2.6-2.6L10 7l-7 7 7 7Z" fill="currentColor"/></svg>
                  </span>Keluar
                </button>
              </div>
            </div>
            <a href="#daftar-kampanye" class="inline-flex h-10 items-center justify-center rounded-full bg-brand-700 px-5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800">
              Donasi Sekarang
            </a>
            <button id="btnMobileMenu" class="md:hidden inline-flex h-10 w-10 items-center justify-center rounded-full hover:bg-ink-50" type="button">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
          </div>
        </div>
        <div id="mobileMenu" class="md:hidden hidden pb-4">
          <div class="flex flex-col gap-1 rounded-2xl border border-ink-100 bg-white p-3">
            <a class="rounded-xl px-3 py-2 text-sm font-semibold text-ink-900 bg-ink-50" href="/kampanye">Kampanye</a>
            <a class="rounded-xl px-3 py-2 text-sm text-ink-700 hover:bg-ink-50" href="/#tentang">Tentang Kami</a>
            <a class="rounded-xl px-3 py-2 text-sm text-ink-700 hover:bg-ink-50" href="/dampak">Dampak Kami</a>
            <a class="rounded-xl px-3 py-2 text-sm text-ink-700 hover:bg-ink-50" href="/#kontak">Kontak</a>
            <button data-open-auth class="mt-2 inline-flex h-10 items-center justify-center rounded-xl bg-ink-900 px-4 text-sm font-semibold text-white" type="button">Masuk / Daftar</button>
          </div>
        </div>
      </div>
    </header>

    <!-- ═══ HERO ═══ -->
    <section class="bg-ink-900 py-14 sm:py-20">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
          <span class="inline-flex rounded-full bg-brand-700/20 px-4 py-1.5 text-xs font-semibold uppercase tracking-widest text-brand-300">Semua Kampanye</span>
          <h1 class="mt-4 text-4xl sm:text-5xl font-semibold tracking-tight text-white">Temukan Kampanye yang <span class="text-brand-400">Berarti</span> bagi Anda</h1>
          <p class="mt-4 text-base text-white/70 max-w-xl">Pilih dari ratusan kampanye terverifikasi di berbagai kategori. Setiap donasi, sekecil apapun, membawa perubahan nyata.</p>
          <div class="mt-8 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1 max-w-md">
              <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-ink-500" width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path d="M10.5 18.5a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="currentColor" stroke-width="2"/>
                <path d="M20.5 20.5l-4.2-4.2" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              </svg>
              <input id="searchInput" type="text" placeholder="Cari kampanye..." class="h-11 w-full rounded-full bg-white/10 pl-11 pr-4 text-sm text-white placeholder-white/50 outline-none ring-1 ring-white/20 focus:ring-brand-400" />
            </div>
          </div>
          <!-- stat strip -->
          <div class="mt-10 flex flex-wrap gap-6">
            <div><div class="text-2xl font-semibold text-white">1.200+</div><div class="text-xs text-white/60 mt-0.5">Kampanye Aktif</div></div>
            <div class="w-px bg-white/10"></div>
            <div><div class="text-2xl font-semibold text-white">Rp 5M+</div><div class="text-xs text-white/60 mt-0.5">Dana Terkumpul</div></div>
            <div class="w-px bg-white/10"></div>
            <div><div class="text-2xl font-semibold text-white">50K+</div><div class="text-xs text-white/60 mt-0.5">Donatur Aktif</div></div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ FILTER BAR ═══ -->
    <section class="border-b border-ink-100 bg-white sticky top-16 z-30">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-2 overflow-x-auto py-3 scrollbar-none">
          <button data-cat="semua" onclick="filterCategory(this)" class="cat-btn whitespace-nowrap rounded-full bg-ink-900 px-4 py-2 text-xs font-semibold text-white">Semua</button>
          <button data-cat="pendidikan" onclick="filterCategory(this)" class="cat-btn whitespace-nowrap rounded-full bg-ink-50 px-4 py-2 text-xs font-semibold text-ink-700 hover:bg-ink-100">Pendidikan</button>
          <button data-cat="kesehatan" onclick="filterCategory(this)" class="cat-btn whitespace-nowrap rounded-full bg-ink-50 px-4 py-2 text-xs font-semibold text-ink-700 hover:bg-ink-100">Kesehatan</button>
          <button data-cat="air bersih" onclick="filterCategory(this)" class="cat-btn whitespace-nowrap rounded-full bg-ink-50 px-4 py-2 text-xs font-semibold text-ink-700 hover:bg-ink-100">Air Bersih</button>
          <button data-cat="bencana" onclick="filterCategory(this)" class="cat-btn whitespace-nowrap rounded-full bg-ink-50 px-4 py-2 text-xs font-semibold text-ink-700 hover:bg-ink-100">Bencana Alam</button>
          <button data-cat="lingkungan" onclick="filterCategory(this)" class="cat-btn whitespace-nowrap rounded-full bg-ink-50 px-4 py-2 text-xs font-semibold text-ink-700 hover:bg-ink-100">Lingkungan</button>
          <button data-cat="komunitas" onclick="filterCategory(this)" class="cat-btn whitespace-nowrap rounded-full bg-ink-50 px-4 py-2 text-xs font-semibold text-ink-700 hover:bg-ink-100">Komunitas</button>

          <div class="ml-auto flex items-center gap-2 flex-shrink-0">
            <label class="text-xs text-ink-600">Urutkan:</label>
            <select id="sortSelect" onchange="renderCards()" class="rounded-xl border border-ink-200 bg-white px-3 py-2 text-xs text-ink-700 outline-none">
              <option value="newest">Terbaru</option>
              <option value="progress">Progress Tertinggi</option>
              <option value="urgent">Paling Mendesak</option>
              <option value="popular">Terpopuler</option>
            </select>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ KAMPANYE UNGGULAN (featured) ═══ -->
    <section class="pt-10 pb-4">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3 mb-6">
          <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-brand-700 text-white">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/></svg>
          </span>
          <h2 class="text-lg font-semibold">Kampanye Unggulan</h2>
        </div>
        <!-- featured big card -->
        <div class="grid gap-6 lg:grid-cols-[1.6fr_1fr]">
          <article class="card-hover relative overflow-hidden rounded-3xl bg-ink-900 min-h-[320px] flex flex-col justify-end">
            <img src="https://images.unsplash.com/photo-1529390079861-591de354faf5?auto=format&fit=crop&w=1400&q=60" alt="Pendidikan" class="absolute inset-0 w-full h-full object-cover opacity-40"/>
            <div class="relative p-6 sm:p-8">
              <span class="badge bg-brand-700 text-white mb-3">Pendidikan</span>
              <h3 class="text-xl sm:text-2xl font-semibold text-white">Beasiswa Anak Pelosok Nusantara</h3>
              <p class="mt-2 text-sm text-white/75 line-clamp-2">Menyediakan beasiswa penuh bagi 200 anak berbakat dari daerah terpencil agar bisa melanjutkan pendidikan ke jenjang SMA dan perguruan tinggi.</p>
              <div class="mt-5">
                <div class="flex justify-between text-xs text-white/70 mb-1.5">
                  <span>Rp 820jt terkumpul</span><span class="font-semibold text-white">82%</span>
                </div>
                <div class="h-2 rounded-full bg-white/20"><div class="h-2 w-[82%] rounded-full bg-brand-400"></div></div>
                <div class="mt-4 flex items-center justify-between gap-3">
                  <span class="text-xs text-white/60">dari Rp 1M target · 18 hari lagi</span>
                  <a href="/donasi?id=13" class="inline-flex h-10 items-center justify-center rounded-full bg-brand-700 px-5 text-sm font-semibold text-white hover:bg-brand-800">Donasi</a>
                </div>
              </div>
            </div>
          </article>
          <div class="flex flex-col gap-4">
            <article class="card-hover relative overflow-hidden rounded-3xl bg-ink-900 flex-1 flex flex-col justify-end min-h-[148px]">
              <img src="https://images.unsplash.com/photo-1584308666744-24d5c474f2ae?auto=format&fit=crop&w=900&q=60" alt="Kesehatan" class="absolute inset-0 w-full h-full object-cover opacity-35"/>
              <div class="relative p-5">
                <span class="badge bg-white/20 text-white mb-2">Kesehatan</span>
                <h3 class="text-base font-semibold text-white">Operasi Gratis Katarak</h3>
                <div class="mt-3 flex items-center justify-between gap-2">
                  <div class="flex-1">
                    <div class="h-1.5 rounded-full bg-white/20"><div class="h-1.5 w-[67%] rounded-full bg-brand-400"></div></div>
                    <div class="mt-1 text-xs text-white/60">67% · Rp 335jt / 500jt</div>
                  </div>
                  <a href="/donasi?id=3" class="h-9 rounded-full bg-brand-700 px-4 text-xs font-semibold text-white hover:bg-brand-800">Donasi</a>
                </div>
              </div>
            </article>
            <article class="card-hover relative overflow-hidden rounded-3xl bg-ink-900 flex-1 flex flex-col justify-end min-h-[148px]">
              <img src="https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&w=900&q=60" alt="Bencana" class="absolute inset-0 w-full h-full object-cover opacity-35"/>
              <div class="relative p-5">
                <span class="badge bg-white/20 text-white mb-2">Bencana Alam</span>
                <h3 class="text-base font-semibold text-white">Bantuan Korban Gempa Cianjur</h3>
                <div class="mt-3 flex items-center justify-between gap-2">
                  <div class="flex-1">
                    <div class="h-1.5 rounded-full bg-white/20"><div class="h-1.5 w-[91%] rounded-full bg-brand-400"></div></div>
                    <div class="mt-1 text-xs text-white/60">91% · Rp 455jt / 500jt</div>
                  </div>
                  <a href="/donasi?id=8" class="h-9 rounded-full bg-brand-700 px-4 text-xs font-semibold text-white hover:bg-brand-800">Donasi</a>
                </div>
              </div>
            </article>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ DAFTAR KAMPANYE ═══ -->
    <section id="daftar-kampanye" class="py-10">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
          <h2 class="text-lg font-semibold">Semua Kampanye <span id="countLabel" class="text-sm font-normal text-ink-500"></span></h2>
          <div class="flex items-center gap-2">
            <button id="viewGrid" onclick="setView('grid')" class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-ink-900 text-white" title="Grid">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M3 3h8v8H3V3Zm0 10h8v8H3v-8Zm10-10h8v8h-8V3Zm0 10h8v8h-8v-8Z"/></svg>
            </button>
            <button id="viewList" onclick="setView('list')" class="h-9 w-9 inline-flex items-center justify-center rounded-xl bg-ink-50 text-ink-600 hover:bg-ink-100" title="List">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M4 6h16v2H4V6Zm0 5h16v2H4v-2Zm0 5h16v2H4v-2Z"/></svg>
            </button>
          </div>
        </div>

        <!-- Cards container -->
        <div id="cardsContainer" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3"></div>

        <!-- Empty state -->
        <div id="emptyState" class="hidden py-20 text-center">
          <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-ink-50">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none"><path d="M10.5 18.5a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z" stroke="#94a3b8" stroke-width="2"/><path d="M20.5 20.5l-4.2-4.2" stroke="#94a3b8" stroke-width="2" stroke-linecap="round"/></svg>
          </div>
          <p class="mt-4 text-sm text-ink-500">Tidak ada kampanye yang cocok.</p>
          <button onclick="resetFilter()" class="mt-4 text-sm font-semibold text-brand-700 hover:underline">Reset Filter</button>
        </div>

        <!-- Load more -->
        <div id="loadMoreWrap" class="mt-10 text-center">
          <button id="btnLoadMore" onclick="loadMore()" class="inline-flex h-11 items-center gap-2 rounded-full border border-ink-200 px-7 text-sm font-semibold text-ink-700 hover:bg-ink-50">
            Muat Lebih Banyak
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          </button>
        </div>
      </div>
    </section>

    <!-- ═══ CTA BANNER ═══ -->
    <section class="py-16 bg-ink-900">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-8">
          <div class="text-white text-center lg:text-left">
            <h2 class="text-3xl sm:text-4xl font-semibold">Punya Cause yang Ingin Anda Perjuangkan?</h2>
            <p class="mt-3 text-white/70">Mulai kampanye Anda sendiri dan galang dukungan dari ribuan donatur di seluruh Indonesia.</p>
          </div>
          <div class="flex flex-col sm:flex-row gap-3 flex-shrink-0">
            <button data-open-auth class="inline-flex h-11 items-center justify-center rounded-full bg-brand-700 px-7 text-sm font-semibold text-white hover:bg-brand-800">
              Mulai Kampanye
            </button>
            <a href="/#tentang" class="inline-flex h-11 items-center justify-center rounded-full border border-white/30 bg-white/10 px-7 text-sm font-semibold text-white hover:bg-white/15">
              Pelajari Lebih Lanjut
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══ FOOTER ═══ -->
    <footer class="bg-ink-900 text-white border-t border-white/10 py-10">
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
          <div><h3 class="text-sm font-semibold text-white/90">Terlibat</h3><ul class="mt-4 space-y-2 text-sm text-white/75"><li><button data-open-auth class="hover:text-white">Donasi</button></li><li><button data-open-auth class="hover:text-white">Mulai Kampanye</button></li></ul></div>
          <div><h3 class="text-sm font-semibold text-white/90">Kontak</h3><ul class="mt-4 space-y-2 text-sm text-white/75"><li><a class="hover:text-white" href="mailto:contact@bantuin.org">contact@bantuin.org</a></li><li>+62 812-3456-7890</li><li>Jakarta, Indonesia</li></ul></div>
        </div>
        <div class="mt-10 border-t border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
          <p class="text-xs text-white/60">© <span id="year"></span> BantuIn. All rights reserved.</p>
          <div class="flex gap-4 text-xs text-white/60"><a class="hover:text-white" href="#">Privasi</a><a class="hover:text-white" href="#">Syarat</a></div>
        </div>
      </div>
    </footer>

    <!-- ═══ MODAL DETAIL KAMPANYE ═══ -->
    <div id="detailModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
      <div class="absolute inset-0 bg-black/55" id="detailModalBackdrop"></div>
      <div class="relative flex min-h-screen items-center justify-center p-4">
        <div class="relative w-full max-w-lg rounded-3xl bg-white shadow-soft overflow-hidden">
          <div id="detailModalImg" class="h-48 w-full bg-ink-100 object-cover"></div>
          <button onclick="closeDetail()" class="absolute top-4 right-4 inline-flex h-9 w-9 items-center justify-center rounded-full bg-white/90 shadow text-ink-700 hover:bg-white">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          </button>
          <div class="p-6">
            <span id="detailCat" class="badge text-white bg-brand-700 mb-3"></span>
            <h3 id="detailTitle" class="text-xl font-semibold"></h3>
            <p id="detailDesc" class="mt-2 text-sm text-ink-600 leading-relaxed"></p>
            <div class="mt-5 grid grid-cols-3 gap-3">
              <div class="rounded-2xl bg-ink-50 p-3 text-center"><div id="detailCollected" class="text-sm font-semibold text-ink-900"></div><div class="text-xs text-ink-500 mt-0.5">Terkumpul</div></div>
              <div class="rounded-2xl bg-ink-50 p-3 text-center"><div id="detailTarget" class="text-sm font-semibold text-ink-900"></div><div class="text-xs text-ink-500 mt-0.5">Target</div></div>
              <div class="rounded-2xl bg-ink-50 p-3 text-center"><div id="detailDays" class="text-sm font-semibold text-ink-900"></div><div class="text-xs text-ink-500 mt-0.5">Hari Lagi</div></div>
            </div>
            <div class="mt-4">
              <div class="flex justify-between text-xs text-ink-600 mb-1"><span>Progress</span><span id="detailPct" class="font-semibold text-ink-900"></span></div>
              <div class="h-2.5 rounded-full bg-ink-100"><div id="detailBar" class="h-2.5 rounded-full bg-brand-700 progress-bar-fill" style="width:0%"></div></div>
            </div>
            <div class="mt-5 flex items-center gap-3">
              <div id="detailFundraiser" class="flex items-center gap-2 flex-1">
                <div class="h-8 w-8 rounded-full bg-brand-50 flex items-center justify-center text-brand-800 text-xs font-bold flex-shrink-0"></div>
                <div><div class="text-xs font-semibold"></div><div class="text-xs text-ink-500">Penggalang Dana</div></div>
              </div>
              <a id="detailDonateBtn" href="/donasi?id=1" class="inline-flex h-10 items-center justify-center rounded-full bg-brand-700 px-5 text-sm font-semibold text-white hover:bg-brand-800">Donasi Sekarang</a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══ AUTH MODAL ═══ -->
    <div id="authModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
      <div class="absolute inset-0 bg-black/55" data-close-auth></div>
      <div class="relative mx-auto flex min-h-screen max-w-3xl items-center justify-center p-4">
        <div class="w-full max-w-md rounded-3xl bg-white shadow-soft">
          <div class="flex items-center justify-between px-6 pt-6">
            <div><h3 class="text-base font-semibold">Selamat Datang di BantuIn</h3><p class="mt-1 text-sm text-ink-600">Masuk atau daftar untuk mulai membantu</p></div>
            <button class="inline-flex h-10 w-10 items-center justify-center rounded-full hover:bg-ink-50" type="button" data-close-auth>
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
              <div><label class="text-xs font-medium text-ink-700">Email</label><input id="loginEmail" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none focus:ring-1 focus:ring-ink-200" placeholder="nama@email.com" type="email" required/></div>
              <div><label class="text-xs font-medium text-ink-700">Password</label><input id="loginPassword" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none focus:ring-1 focus:ring-ink-200" placeholder="Masukkan password" type="password" required/></div>
              <button class="mt-2 inline-flex h-11 w-full items-center justify-center rounded-2xl bg-brand-700 text-sm font-semibold text-white hover:bg-brand-800" type="submit">Masuk</button>
            </form>
            <form id="registerForm" class="mt-5 space-y-3 hidden">
              <div><label class="text-xs font-medium text-ink-700">Nama Lengkap</label><input id="regName" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none" type="text" required/></div>
              <div><label class="text-xs font-medium text-ink-700">Email</label><input id="regEmail" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none" type="email" required/></div>
              <div><label class="text-xs font-medium text-ink-700">Peran</label>
                <select id="regRole" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none"><option value="donatur">Donatur</option><option value="fundraiser">Fundraiser</option></select>
              </div>
              <div><label class="text-xs font-medium text-ink-700">Password</label><input id="regPassword" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none" type="password" minlength="6" required/></div>
              <div><label class="text-xs font-medium text-ink-700">Konfirmasi Password</label><input id="regConfirm" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none" type="password" minlength="6" required/></div>
              <button class="mt-2 inline-flex h-11 w-full items-center justify-center rounded-2xl bg-brand-700 text-sm font-semibold text-white hover:bg-brand-800" type="submit">Daftar</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <script>
    // DATA DUMMY KAMPANYE
    // ── Data bawaan (dari database) ──
    const STATIC_CAMPAIGNS = @json($campaigns);

    // ── Gabungkan dengan kampanye dari localStorage ──
    function getLocalCampaigns() {
      return JSON.parse(localStorage.getItem('bantuin_campaigns') || '[]');
    }

    function buildAllCampaigns() {
      const local = getLocalCampaigns().map(c => ({
        id: c.id,
        title: c.title,
        cat: c.cat,
        img: c.img || 'https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&w=800&q=60',
        desc: c.desc,
        fundraiser: c.fundraiser,
        collected: Number(c.collected) || 0,
        target: Number(c.target),
        days: Number(c.days) || 30,
        donors: Number(c.donors) || 0,
        featured: false,
        isNew: true,   // tandai kampanye baru dari fundraiser
      }));
      // Kampanye baru ditampilkan paling atas
      return [...local, ...STATIC_CAMPAIGNS];
    }

    // CAMPAIGNS sekarang dinamis — dibangun ulang tiap render
    let CAMPAIGNS = buildAllCampaigns();

    // ══════════════════════════════════════════
    // STATE
    // ══════════════════════════════════════════
    let currentCat = 'semua';
    let currentView = 'grid';
    let visibleCount = 6;

    function fmt(n) {
      if (n >= 1000000000) return 'Rp ' + (n/1000000000).toFixed(n%1000000000===0?0:1).replace('.0','') + 'M';
      if (n >= 1000000) return 'Rp ' + (n/1000000).toFixed(n%1000000===0?0:1).replace('.0','') + 'jt';
      return 'Rp ' + Number(n).toLocaleString('id');
    }

    function filtered() {
      // Rebuild setiap kali agar kampanye baru dari fundraiser langsung muncul
      CAMPAIGNS = buildAllCampaigns();
      const q = document.getElementById('searchInput').value.toLowerCase();
      const sort = document.getElementById('sortSelect').value;
      let arr = CAMPAIGNS.filter(c => {
        const matchCat = currentCat === 'semua' || c.cat === currentCat;
        const matchQ = !q || c.title.toLowerCase().includes(q) || c.cat.includes(q) || c.fundraiser.toLowerCase().includes(q);
        return matchCat && matchQ;
      });
      if (sort === 'progress') arr.sort((a,b) => (b.collected/b.target) - (a.collected/a.target));
      else if (sort === 'urgent') arr.sort((a,b) => a.days - b.days);
      else if (sort === 'popular') arr.sort((a,b) => b.donors - a.donors);
      // Kalau sort "newest", kampanye baru (isNew) tetap di atas
      if (sort === 'newest') arr.sort((a,b) => (b.isNew ? 1 : 0) - (a.isNew ? 1 : 0));
      return arr;
    }

    function urgencyBadge(days) {
      if (days <= 7) return '<span class="badge bg-red-50 text-red-600">🔥 ' + days + ' hari lagi</span>';
      if (days <= 21) return '<span class="badge bg-orange-50 text-orange-600">⏳ ' + days + ' hari lagi</span>';
      return '<span class="badge bg-ink-50 text-ink-600">' + days + ' hari lagi</span>';
    }

    function catColor(cat) {
      const map = {pendidikan:'bg-blue-50 text-blue-700',kesehatan:'bg-green-50 text-green-700','air bersih':'bg-cyan-50 text-cyan-700',bencana:'bg-red-50 text-red-700',lingkungan:'bg-emerald-50 text-emerald-700',komunitas:'bg-purple-50 text-purple-700'};
      return map[cat] || 'bg-ink-50 text-ink-700';
    }

    function renderCards() {
      const arr = filtered();
      const container = document.getElementById('cardsContainer');
      const empty = document.getElementById('emptyState');
      const loadWrap = document.getElementById('loadMoreWrap');
      const label = document.getElementById('countLabel');

      label.textContent = '(' + arr.length + ' kampanye)';

      if (arr.length === 0) {
        container.innerHTML = '';
        empty.classList.remove('hidden');
        loadWrap.classList.add('hidden');
        return;
      }
      empty.classList.add('hidden');

      const isGrid = currentView === 'grid';
      container.className = isGrid
        ? 'grid gap-5 sm:grid-cols-2 lg:grid-cols-3'
        : 'flex flex-col gap-4';

      const slice = arr.slice(0, visibleCount);
      container.innerHTML = slice.map((c, i) => {
        const pct = Math.round(c.collected / c.target * 100);
        const newBadge = c.isNew ? `<span class="absolute left-3 bottom-3 badge bg-brand-700 text-white text-[10px]">✨ Baru</span>` : '';
        if (isGrid) {
          return `<article class="card-hover fade-up overflow-hidden rounded-2xl border ${c.isNew ? 'border-brand-200 ring-1 ring-brand-100' : 'border-ink-100'} bg-white shadow-sm cursor-pointer" onclick="openDetail(${c.id})" style="animation-delay:${i*0.07}s; opacity:0">
            <div class="relative h-44 bg-ink-100">
              <img alt="${c.title}" class="h-full w-full object-cover" src="${c.img}" loading="lazy"/>
              <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
              <span class="absolute left-3 top-3 badge ${catColor(c.cat)} capitalize">${c.cat}</span>
              <span class="absolute right-3 top-3">${urgencyBadge(c.days)}</span>
              ${newBadge}
            </div>
            <div class="p-4">
              <h3 class="text-sm font-semibold leading-snug line-clamp-2">${c.title}</h3>
              <p class="mt-1.5 line-clamp-2 text-xs text-ink-500">${c.desc}</p>
              <div class="mt-4">
                <div class="h-1.5 rounded-full bg-ink-100"><div class="h-1.5 rounded-full bg-brand-700 progress-bar-fill" style="width:${pct}%"></div></div>
                <div class="mt-2 flex items-center justify-between text-xs text-ink-600">
                  <span><strong class="text-ink-900">${fmt(c.collected)}</strong> terkumpul</span>
                  <span class="font-semibold text-brand-700">${pct}%</span>
                </div>
                <div class="mt-0.5 text-xs text-ink-400">dari ${fmt(c.target)} · ${c.donors.toLocaleString('id')} donatur</div>
              </div>
              <div class="mt-4 flex items-center gap-2">
                <div class="h-6 w-6 rounded-full bg-brand-50 flex items-center justify-center text-brand-800 text-xs font-bold flex-shrink-0">${c.fundraiser.charAt(0)}</div>
                <span class="text-xs text-ink-500 truncate">${c.fundraiser}</span>
              </div>
              <a href="/donasi?id=${c.id}" class="mt-3 inline-flex h-10 w-full items-center justify-center rounded-xl bg-brand-700 text-sm font-semibold text-white hover:bg-brand-800" onclick="event.stopPropagation()">Donasi Sekarang</a>
            </div>
          </article>`;
        } else {
          return `<article class="card-hover fade-up flex gap-4 overflow-hidden rounded-2xl border border-ink-100 bg-white p-4 shadow-sm cursor-pointer" onclick="openDetail(${c.id})" style="animation-delay:${i*0.05}s; opacity:0">
            <div class="relative h-28 w-40 flex-shrink-0 rounded-xl overflow-hidden bg-ink-100">
              <img alt="${c.title}" class="h-full w-full object-cover" src="${c.img}" loading="lazy"/>
            </div>
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between gap-2 flex-wrap">
                <span class="badge ${catColor(c.cat)} capitalize">${c.cat}</span>
                ${urgencyBadge(c.days)}
              </div>
              <h3 class="mt-1 text-sm font-semibold leading-snug">${c.title}</h3>
              <p class="mt-1 line-clamp-1 text-xs text-ink-500">${c.desc}</p>
              <div class="mt-3">
                <div class="h-1.5 rounded-full bg-ink-100"><div class="h-1.5 rounded-full bg-brand-700 progress-bar-fill" style="width:${pct}%"></div></div>
                <div class="mt-1.5 flex items-center justify-between text-xs text-ink-600">
                  <span><strong class="text-ink-900">${fmt(c.collected)}</strong> dari ${fmt(c.target)}</span>
                  <span class="font-semibold text-brand-700">${pct}%</span>
                </div>
              </div>
            </div>
            <div class="flex flex-col items-end justify-between flex-shrink-0">
              <span class="text-xs text-ink-400">${c.donors.toLocaleString('id')} donatur</span>
              <a href="/donasi?id=${c.id}" class="inline-flex h-9 items-center justify-center rounded-xl bg-brand-700 px-4 text-xs font-semibold text-white hover:bg-brand-800" onclick="event.stopPropagation()">Donasi</a>
            </div>
          </article>`;
        }
      }).join('');

      loadWrap.classList.toggle('hidden', slice.length >= arr.length);

      // Re-attach auth listeners for dynamically created buttons
      attachAuthListeners();
    }

    function loadMore() {
      visibleCount += 6;
      renderCards();
    }

    function filterCategory(btn) {
      currentCat = btn.dataset.cat;
      visibleCount = 6;
      document.querySelectorAll('.cat-btn').forEach(b => {
        b.className = 'cat-btn whitespace-nowrap rounded-full px-4 py-2 text-xs font-semibold ' + (b === btn ? 'bg-ink-900 text-white' : 'bg-ink-50 text-ink-700 hover:bg-ink-100');
      });
      renderCards();
    }

    function setView(v) {
      currentView = v;
      const grid = document.getElementById('viewGrid');
      const list = document.getElementById('viewList');
      if (v === 'grid') {
        grid.className = 'h-9 w-9 inline-flex items-center justify-center rounded-xl bg-ink-900 text-white';
        list.className = 'h-9 w-9 inline-flex items-center justify-center rounded-xl bg-ink-50 text-ink-600 hover:bg-ink-100';
      } else {
        list.className = 'h-9 w-9 inline-flex items-center justify-center rounded-xl bg-ink-900 text-white';
        grid.className = 'h-9 w-9 inline-flex items-center justify-center rounded-xl bg-ink-50 text-ink-600 hover:bg-ink-100';
      }
      renderCards();
    }

    function resetFilter() {
      currentCat = 'semua';
      document.getElementById('searchInput').value = '';
      document.querySelectorAll('.cat-btn').forEach((b, i) => {
        b.className = 'cat-btn whitespace-nowrap rounded-full px-4 py-2 text-xs font-semibold ' + (i === 0 ? 'bg-ink-900 text-white' : 'bg-ink-50 text-ink-700 hover:bg-ink-100');
      });
      renderCards();
    }

    // ── Detail Modal ──
    function openDetail(id) {
      const c = CAMPAIGNS.find(x => x.id === id);
      if (!c) return;
      const pct = Math.round(c.collected / c.target * 100);
      const img = document.getElementById('detailModalImg');
      img.style.backgroundImage = `url(${c.img})`;
      img.style.backgroundSize = 'cover';
      img.style.backgroundPosition = 'center';
      document.getElementById('detailCat').textContent = c.cat;
      document.getElementById('detailTitle').textContent = c.title;
      document.getElementById('detailDesc').textContent = c.desc;
      document.getElementById('detailCollected').textContent = fmt(c.collected);
      document.getElementById('detailTarget').textContent = fmt(c.target);
      document.getElementById('detailDays').textContent = c.days;
      document.getElementById('detailPct').textContent = pct + '%';
      setTimeout(() => { document.getElementById('detailBar').style.width = pct + '%'; }, 50);
      const fr = document.getElementById('detailFundraiser');
      fr.querySelector('div:first-child').textContent = c.fundraiser.charAt(0);
      fr.querySelector('.text-xs.font-semibold').textContent = c.fundraiser;
      const donateBtn = document.getElementById('detailDonateBtn');
      if (donateBtn) donateBtn.href = '/donasi?id=' + c.id;
      document.getElementById('detailModal').classList.remove('hidden');
      document.body.style.overflow = 'hidden';
      attachAuthListeners();
    }

    function closeDetail() {
      document.getElementById('detailModal').classList.add('hidden');
      document.body.style.overflow = '';
      setTimeout(() => { document.getElementById('detailBar').style.width = '0%'; }, 300);
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
        if (wrapper) { wrapper.classList.remove('hidden'); wrapper.style.display = ''; }
        const el = (id, txt) => { const e = document.getElementById(id); if(e) e.textContent = txt; };
        el('userInitial', user.name.charAt(0).toUpperCase());
        el('menuUserName', user.name); el('menuUserEmail', user.email);
        el('menuUserRole', user.role.charAt(0).toUpperCase()+user.role.slice(1));
        const dl = document.getElementById('menuDashboardLink'); if(dl) dl.href = getDashboardUrl(user.role);
      } else {
        if (btnLogin) btnLogin.style.display = '';
        if (wrapper) { wrapper.classList.add('hidden'); wrapper.style.display = 'none'; }
      }
    }

    function openAuthModal() { document.getElementById('authModal').classList.remove('hidden'); document.body.style.overflow = 'hidden'; clearAuthMsg(); }
    function closeAuthModal() { document.getElementById('authModal').classList.add('hidden'); document.body.style.overflow = ''; }
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

    function attachAuthListeners() {
      document.querySelectorAll('[data-open-auth]').forEach(el => {
        el.onclick = function(e) {
          e.preventDefault(); e.stopPropagation();
          const user = getCurrentUser();
          if (user) window.location.href = getDashboardUrl(user.role);
          else openAuthModal();
        };
      });
    }

    document.addEventListener('DOMContentLoaded', function() {
      seedDefaultAccounts();
      updateNavbar();
      document.getElementById('year').textContent = new Date().getFullYear();

      renderCards();
      document.getElementById('searchInput').addEventListener('input', () => { visibleCount = 6; renderCards(); });
      document.getElementById('detailModalBackdrop').addEventListener('click', closeDetail);

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

      document.addEventListener('keydown', e => { if(e.key==='Escape'){closeAuthModal();closeDetail();} });

      attachAuthListeners();
    });
    </script>
  </body>
</html>
