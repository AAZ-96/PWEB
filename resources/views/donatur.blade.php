<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Donatur — BantuIn</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            colors: {
              brand: { 50:"#fff1f4",100:"#ffe4ea",200:"#fecdd8",300:"#fda4b9",400:"#fb6f94",500:"#f43f73",600:"#e11d5d",700:"#be124c",800:"#9f123f",900:"#881337" },
              ink: { 900:"#0f172a",700:"#334155",600:"#475569",500:"#64748b",200:"#e2e8f0",100:"#f1f5f9",50:"#f8fafc" },
            },
            boxShadow: { soft: "0 10px 30px rgba(2, 6, 23, 0.10)" },
          },
        },
      };
    </script>
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

    <!-- Navbar -->
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
            <a class="font-semibold text-ink-900" href="/donatur">Dashboard</a>
            <a class="hover:text-ink-900" href="/#tentang">Tentang Kami</a>
            <a class="hover:text-ink-900" href="/#kontak">Kontak</a>
          </nav>
          <div class="flex items-center gap-2">
            <a href="/kampanye" class="inline-flex h-10 items-center justify-center rounded-full bg-brand-700 px-5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800">
              Donasi Sekarang
            </a>
            <div class="relative">
              <button id="userMenuButton" class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 text-brand-800 ring-1 ring-brand-100" type="button" aria-expanded="false" aria-controls="userMenu">
                <span id="userInitial" class="text-sm font-semibold">?</span>
              </button>
              <div id="userMenu" class="absolute right-0 mt-2 w-64 overflow-hidden rounded-2xl border border-ink-100 bg-white shadow-soft hidden" role="menu">
                <div class="px-4 py-3">
                  <div id="menuUserName" class="text-sm font-semibold"></div>
                  <div id="menuUserEmail" class="text-xs text-ink-600"></div>
                  <div id="menuUserRole" class="mt-1 text-xs text-brand-700 font-semibold"></div>
                </div>
                <div class="border-t border-ink-100"></div>
                <a href="/donatur" class="flex items-center gap-3 px-4 py-3 text-sm text-ink-700 hover:bg-ink-50" role="menuitem">
                  <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-ink-50">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 13h7V4H4v9Zm0 7h7v-5H4v5Zm9 0h7V11h-7v9Zm0-18v5h7V2h-7Z" fill="currentColor"/></svg>
                  </span>
                  Dashboard
                </a>
                <div class="border-t border-ink-100"></div>
                <button type="button" onclick="logout()" class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm text-ink-700 hover:bg-ink-50" role="menuitem">
                  <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-ink-50">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M10 17l1.4-1.4L8.8 13H20v-2H8.8l2.6-2.6L10 7l-7 7 7 7Z" fill="currentColor"/></svg>
                  </span>
                  Keluar
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <!-- Main -->
    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
      <!-- Welcome Banner -->
      <section class="overflow-hidden rounded-3xl bg-ink-900 px-6 py-8 sm:px-10 mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div class="text-white">
            <p class="text-sm text-white/60">Dashboard Donatur</p>
            <h1 id="welcomeName" class="text-2xl font-semibold mt-1">Selamat datang, {{ auth()->user()->name }}!</h1>
            <p class="mt-2 text-sm text-white/70">Terima kasih telah menjadi bagian dari gerakan perubahan BantuIn.</p>
          </div>
          <a href="/campaigns" class="inline-flex h-11 items-center justify-center rounded-full bg-brand-700 px-6 text-sm font-semibold text-white hover:bg-brand-800 whitespace-nowrap">
            Donasi Sekarang
          </a>
        </div>
        <div class="mt-6 grid grid-cols-3 gap-4">
          <div class="rounded-2xl bg-white/10 p-4">
            <p class="text-xs text-white/60">Total Donasi</p>
            <p class="mt-2 text-xl font-semibold text-white">Rp {{ number_format($totalDonation, 0, ',', '.') }}</p>
          </div>
          <div class="rounded-2xl bg-white/10 p-4">
            <p class="text-xs text-white/60">Kampanye Didukung</p>
            <p class="mt-2 text-xl font-semibold text-white">{{ $supportedCount }}</p>
          </div>
          <div class="rounded-2xl bg-white/10 p-4">
            <p class="text-xs text-white/60">Terakhir Donasi</p>
            <p class="mt-2 text-xl font-semibold text-white">{{ $lastDonation ? $lastDonation->created_at->translatedFormat('d M Y') : '—' }}</p>
          </div>
        </div>
      </section>

      <div class="grid gap-6 lg:grid-cols-[1.4fr_1fr]">
        <!-- Riwayat Donasi -->
        <section class="rounded-2xl border border-ink-100 bg-white shadow-sm">
          <div class="px-6 py-5 border-b border-ink-100">
            <h2 class="text-base font-semibold">Riwayat Donasi</h2>
            <p class="mt-1 text-sm text-ink-600">Semua transaksi donasi Anda</p>
          </div>
          <div class="p-4 sm:p-6 space-y-4">
            @forelse($donations as $d)
              <article class="flex items-center justify-between gap-4 rounded-2xl border border-ink-100 p-4">
                <div class="flex items-center gap-3">
                  <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-brand-50 text-brand-800">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M7 2h10v2H7V2Zm-2 4h14v16H5V6Zm3 4h8v2H8v-2Zm0 4h8v2H8v-2Z" fill="currentColor"/></svg>
                  </span>
                  <div>
                    <div class="text-sm font-semibold">{{ $d->campaign->title ?? 'BantuIn' }}</div>
                    <div class="text-xs text-ink-500">{{ $d->created_at->translatedFormat('d F Y') }}</div>
                  </div>
                </div>
                <div class="text-right">
                  <div class="text-sm font-semibold text-brand-700">Rp {{ number_format($d->amount, 0, ',', '.') }}</div>
                  <span class="mt-1 inline-flex rounded-full bg-green-50 px-2.5 py-1 text-xs font-medium text-green-700 capitalize">{{ $d->status }}</span>
                </div>
              </article>
            @empty
              <p class="text-sm text-ink-500 text-center py-4">Belum ada riwayat transaksi donasi.</p>
            @endforelse
          </div>
        </section>

        <!-- Kampanye yang Didukung -->
        <section class="rounded-2xl border border-ink-100 bg-white shadow-sm">
          <div class="px-6 py-5 border-b border-ink-100">
            <h2 class="text-base font-semibold">Kampanye yang Anda Dukung</h2>
            <p class="mt-1 text-sm text-ink-600">Pantau perkembangan kampanye favorit</p>
          </div>
          <div class="p-4 sm:p-6 space-y-5">
            @forelse($supportedCampaigns as $sc)
              @php
                $scPct = $sc->target > 0 ? round(($sc->collected / $sc->target) * 100) : 0;
              @endphp
              <div>
                <div class="flex items-center justify-between gap-4">
                  <div class="text-sm font-semibold truncate">{{ $sc->title }}</div>
                  <div class="text-xs text-ink-600">{{ $scPct }}%</div>
                </div>
                <div class="mt-2 h-2 rounded-full bg-ink-100"><div class="h-2 rounded-full bg-ink-900" style="width: {{ min($scPct, 100) }}%"></div></div>
                <div class="mt-2 text-xs text-ink-600">
                  Rp 
                  @if($sc->collected >= 1000000000)
                    {{ round($sc->collected / 1000000000, 1) }}M
                  @elseif($sc->collected >= 1000000)
                    {{ round($sc->collected / 1000000) }}jt
                  @else
                    {{ number_format($sc->collected, 0, ',', '.') }}
                  @endif
                  terkumpul <span class="text-ink-500">dari Rp 
                  @if($sc->target >= 1000000000)
                    {{ round($sc->target / 1000000000, 1) }}M
                  @elseif($sc->target >= 1000000)
                    {{ round($sc->target / 1000000) }}jt
                  @else
                    {{ number_format($sc->target, 0, ',', '.') }}
                  @endif
                  </span>
                </div>
              </div>
            @empty
              <p class="text-sm text-ink-500 text-center py-4">Belum mendukung kampanye apapun.</p>
            @endforelse
            <a href="/campaigns" class="mt-2 inline-flex h-11 w-full items-center justify-center rounded-2xl bg-brand-700 text-sm font-semibold text-white hover:bg-brand-800">
              Jelajahi Kampanye Lainnya
            </a>
          </div>
        </section>
      </div>

      <!-- Pindah ke Fundraiser -->
      <section class="mt-6 rounded-3xl border border-brand-100 bg-brand-50 px-6 py-8 sm:px-10">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
          <div class="flex items-start gap-4">
            <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-brand-700 text-white">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M17 12h-5v5h5v-5zM16 1v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-1V1h-2zm3 18H5V8h14v11z"/></svg>
            </span>
            <div>
              <h2 class="text-base font-semibold text-ink-900">Ingin Menjadi Fundraiser?</h2>
              <p class="mt-1 text-sm text-ink-600">Buat kampanye Anda sendiri, galang dana, dan berdampak lebih besar bagi masyarakat. Bergabunglah sebagai fundraiser BantuIn sekarang.</p>
              <ul class="mt-3 space-y-1">
                <li class="flex items-center gap-2 text-sm text-ink-700">
                  <svg class="text-brand-700 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                  Buat dan kelola kampanye donasi sendiri
                </li>
                <li class="flex items-center gap-2 text-sm text-ink-700">
                  <svg class="text-brand-700 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                  Pantau perkembangan penggalangan dana secara real-time
                </li>
                <li class="flex items-center gap-2 text-sm text-ink-700">
                  <svg class="text-brand-700 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                  Tetap bisa berdonasi seperti biasa
                </li>
              </ul>
            </div>
          </div>
          <div class="shrink-0">
            <button
              id="btnJadiFundraiser"
              type="button"
              class="inline-flex h-11 items-center justify-center rounded-full bg-brand-700 px-6 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 whitespace-nowrap"
            >
              Jadilah Fundraiser
            </button>
          </div>
        </div>
      </section>
    </main>

    <!-- Modal Konfirmasi Pindah Role -->
    <div id="modalFundraiser" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm p-4">
      <div class="w-full max-w-md rounded-3xl bg-white shadow-soft p-8">
        <div class="flex justify-center mb-4">
          <span class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-brand-50 text-brand-700">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
          </span>
        </div>
        <h3 class="text-center text-lg font-semibold text-ink-900">Konfirmasi Perpindahan Peran</h3>
        <p class="mt-2 text-center text-sm text-ink-600">
          Apakah Anda yakin ingin beralih menjadi <strong>Fundraiser</strong>? Anda akan diarahkan ke dashboard fundraiser. Anda masih tetap bisa berdonasi seperti biasa.
        </p>
        <div class="mt-6 flex flex-col sm:flex-row gap-3">
          <button
            id="btnBatalFundraiser"
            type="button"
            class="flex-1 h-11 rounded-full border border-ink-200 text-sm font-semibold text-ink-700 hover:bg-ink-50"
          >
            Batal
          </button>
          <button
            id="btnKonfirmasiFundraiser"
            type="button"
            class="flex-1 h-11 rounded-full bg-brand-700 text-sm font-semibold text-white hover:bg-brand-800"
          >
            Ya, Jadilah Fundraiser
          </button>
        </div>
      </div>
    </div>

    <footer class="border-t border-ink-100 bg-ink-50 mt-10">
      <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-6 sm:px-6 lg:px-8 lg:flex-row lg:items-center lg:justify-between">
        <p class="text-sm text-ink-600">© <span id="year"></span> BantuIn. Bersama kita berbagi dan mengubah masa depan.</p>
        <div class="flex flex-wrap gap-3 text-sm text-ink-600">
          <a href="/#tentang" class="hover:text-ink-900">Tentang</a>
          <a href="/#kontak" class="hover:text-ink-900">Kontak</a>
        </div>
      </div>
    </footer>

    <script>
      // ── Shared Auth Helpers ──
      function getCurrentUser() { return JSON.parse(localStorage.getItem('bantuin_current_user') || 'null'); }
      
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

      function getDashboardUrl(role) {
        if (role === 'admin') return '/admin';
        if (role === 'fundraiser') return '/fundraiser';
        return '/donatur';
      }

      document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('year').textContent = new Date().getFullYear();

        const user = getCurrentUser();
        // Guard: redirect to home if not logged in or wrong role
        if (!user) { window.location.href = '/'; return; }
        if (user.role !== 'donatur' && user.role !== 'admin') {
          window.location.href = getDashboardUrl(user.role); return;
        }

        // Populate user info
        document.getElementById('welcomeName').textContent = 'Selamat datang, ' + user.name + '!';
        document.getElementById('userInitial').textContent = user.name.charAt(0).toUpperCase();
        document.getElementById('menuUserName').textContent = user.name;
        document.getElementById('menuUserEmail').textContent = user.email;
        document.getElementById('menuUserRole').textContent = user.role.charAt(0).toUpperCase() + user.role.slice(1);

        // User menu toggle
        const btn = document.getElementById('userMenuButton');
        const menu = document.getElementById('userMenu');
        btn.addEventListener('click', function (e) {
          e.stopPropagation();
          menu.classList.toggle('hidden');
        });
        document.addEventListener('click', function () { menu.classList.add('hidden'); });

        // Pindah ke Fundraiser
        const modal = document.getElementById('modalFundraiser');

        document.getElementById('btnJadiFundraiser').addEventListener('click', function () {
          modal.classList.remove('hidden');
          modal.classList.add('flex');
        });

        document.getElementById('btnBatalFundraiser').addEventListener('click', function () {
          modal.classList.add('hidden');
          modal.classList.remove('flex');
        });

        modal.addEventListener('click', function (e) {
          if (e.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
          }
        });

        document.getElementById('btnKonfirmasiFundraiser').addEventListener('click', async function () {
          try {
            const response = await fetch('/switch-role', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              },
              body: JSON.stringify({ role: 'fundraiser' })
            });
            const result = await response.json();
            if (result.ok) {
              const currentUser = getCurrentUser();
              if (currentUser) {
                currentUser.role = 'fundraiser';
                localStorage.setItem('bantuin_current_user', JSON.stringify(currentUser));
              }
              window.location.href = '/fundraiser';
            }
          } catch (e) {
            console.error(e);
          }
        });
      });
    </script>
  </body>
</html>
