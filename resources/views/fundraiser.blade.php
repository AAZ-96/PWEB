<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BantuIn — Fundraiser Dashboard</title>
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
    <style>
      .hero-overlay { background: linear-gradient(to right, rgba(2,6,23,0.65), rgba(2,6,23,0.15), rgba(2,6,23,0.65)); }
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

    <header class="sticky top-0 z-40 border-b border-ink-100 bg-white/95 backdrop-blur-sm">
      <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-4 sm:px-6 lg:px-8">
        <a href="/" class="flex items-center gap-3">
          <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-brand-700 text-white shadow-soft">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21s-7.2-4.6-9.6-9C.1 7.2 3.2 3.8 7.1 4.1c1.7.1 3.2 1 3.9 2.2.7-1.2 2.2-2.1 3.9-2.2 3.9-.3 7 3.1 4.7 7.9C19.2 16.4 12 21 12 21z"/></svg>
          </span>
          <div>
            <p class="text-sm font-semibold tracking-tight text-ink-900">BantuIn</p>
            <p class="text-xs text-ink-500">Fundraiser Dashboard</p>
          </div>
        </a>
        <nav class="hidden items-center gap-8 text-sm text-ink-700 md:flex">
          <a href="/fundraiser" class="font-semibold text-ink-900">Dashboard</a>
          <a href="/#kampanye" class="hover:text-ink-900">Kampanye</a>
          <a href="/#kontak" class="hover:text-ink-900">Bantuan</a>
        </nav>
        <div class="flex items-center gap-3">
          <a href="/" class="hidden rounded-full border border-ink-200 px-4 py-2 text-sm font-medium text-ink-700 transition hover:border-ink-300 hover:text-ink-900 md:inline-flex">
            Kembali ke Beranda
          </a>
          <div class="relative">
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
              <button type="button" onclick="logout()" class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm text-ink-700 hover:bg-ink-50">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-ink-50">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M10 17l1.4-1.4L8.8 13H20v-2H8.8l2.6-2.6L10 7l-7 7 7 7Z" fill="currentColor"/></svg>
                </span>
                Keluar
              </button>
            </div>
          </div>
        </div>
      </div>
    </header>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
      <!-- Hero Banner -->
      <section class="overflow-hidden rounded-[2rem] bg-ink-900 shadow-soft">
        <div class="relative overflow-hidden px-6 py-10 sm:px-10 lg:px-14 lg:py-16">
          <div class="hero-overlay absolute inset-0"></div>
          <div class="relative grid gap-8 lg:grid-cols-[1.4fr_0.9fr] items-center">
            <div class="max-w-2xl text-white">
              <p class="mb-4 inline-flex rounded-full bg-brand-700/15 px-4 py-2 text-sm font-semibold uppercase tracking-[0.18em] text-brand-100">
                Fundraiser · BantuIn
              </p>
              <h1 id="welcomeName" class="text-3xl font-semibold tracking-tight sm:text-4xl lg:text-5xl">
                Kelola Kampanye Anda
              </h1>
              <p class="mt-5 max-w-xl text-sm leading-7 text-brand-100 sm:text-base">
                Pantau hasil donasi, tenggat waktu, dan dukungan terbaru dalam satu dasbor yang mudah digunakan.
              </p>
              <div class="mt-8 grid gap-4 sm:grid-cols-2">
                <div class="rounded-3xl bg-white/10 p-5 ring-1 ring-white/10 backdrop-blur-sm">
                  <p class="text-sm text-brand-100">Kampanye Aktif</p>
                  <p class="mt-3 text-3xl font-semibold">{{ $activeCount }}</p>
                </div>
                <div class="rounded-3xl bg-white/10 p-5 ring-1 ring-white/10 backdrop-blur-sm">
                  <p class="text-sm text-brand-100">Total Dana Terkumpul</p>
                  <p class="mt-3 text-3xl font-semibold">
                    Rp 
                    @if($totalCollected >= 1000000000)
                      {{ number_format($totalCollected / 1000000000, 1, ',', '.') }}M
                    @elseif($totalCollected >= 1000000)
                      {{ number_format($totalCollected / 1000000, 1, ',', '.') }}jt
                    @else
                      {{ number_format($totalCollected, 0, ',', '.') }}
                    @endif
                  </p>
                </div>
              </div>
            </div>
            <div class="space-y-4 rounded-[2rem] border border-white/10 bg-white/5 p-6 backdrop-blur-sm sm:p-8">
              <div class="flex items-center justify-between gap-4">
                <div>
                  <p class="text-sm uppercase tracking-[0.25em] text-brand-200">Kinerja Mingguan</p>
                  <p class="mt-3 text-2xl font-semibold text-white">94%</p>
                </div>
              </div>
              <div class="mt-4 overflow-hidden rounded-full bg-white/10">
                <div class="h-3 w-[94%] rounded-full bg-brand-500"></div>
              </div>
              <dl class="mt-6 grid gap-4 text-sm text-brand-100 sm:grid-cols-2">
                <div><dt class="font-medium text-white">Donatur Baru</dt><dd class="mt-1">132 orang</dd></div>
                <div><dt class="font-medium text-white">Rata-rata Donasi</dt><dd class="mt-1">Rp 450.000</dd></div>
              </dl>
            </div>
          </div>
        </div>
      </section>

      <!-- Content -->
      <section class="mt-10 grid gap-6 xl:grid-cols-[1.7fr_1fr]">
        <div class="space-y-6">
          <!-- Kampanye Berjalan -->
          <div class="rounded-3xl border border-ink-100 bg-white p-6 shadow-soft">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
              <div>
                <p class="text-sm uppercase tracking-[0.2em] text-ink-500">Ringkasan Kampanye</p>
                <h2 class="mt-2 text-2xl font-semibold text-ink-900">Kampanye yang Sedang Berjalan</h2>
              </div>
              <div class="inline-flex items-center gap-2 rounded-full bg-brand-50 px-4 py-2 text-sm font-semibold text-brand-700">
                <span class="h-2 w-2 rounded-full bg-brand-700"></span> {{ $activeCount }} kampanye aktif
              </div>
            </div>
            <div class="mt-6 space-y-4">
              @forelse($campaigns as $c)
                @php
                  $pct = $c->target > 0 ? min(round(($c->collected / $c->target) * 100), 100) : 0;
                  $statusLabel = 'Menunggu';
                  $statusBg = 'bg-yellow-50 text-yellow-700';
                  if ($c->status === 'active') {
                      $statusLabel = 'Aktif';
                      $statusBg = 'bg-green-50 text-green-700';
                  } elseif ($c->status === 'rejected') {
                      $statusLabel = 'Ditolak';
                      $statusBg = 'bg-red-50 text-red-700';
                  } elseif ($c->status === 'done') {
                      $statusLabel = 'Selesai';
                      $statusBg = 'bg-blue-50 text-blue-700';
                  }
                @endphp
                <article class="rounded-3xl border border-ink-100 p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-soft">
                  <div class="flex items-start justify-between gap-4">
                    <div>
                      <p class="text-sm font-semibold text-ink-900">{{ $c->title }}</p>
                      <p class="mt-1 text-sm text-ink-600 truncate max-w-md">{{ $c->desc }}</p>
                    </div>
                    <span class="rounded-full {{ $statusBg }} px-3 py-1 text-xs font-semibold whitespace-nowrap">{{ $statusLabel }}</span>
                  </div>
                  <div class="mt-4 grid grid-cols-3 gap-3 text-sm">
                    <div>
                      <p class="text-ink-500">Terkumpul</p>
                      <p class="mt-1 font-semibold text-ink-900">
                        Rp 
                        @if($c->collected >= 1000000000)
                          {{ round($c->collected / 1000000000, 1) }}M
                        @elseif($c->collected >= 1000000)
                          {{ round($c->collected / 1000000) }}jt
                        @else
                          {{ number_format($c->collected, 0, ',', '.') }}
                        @endif
                      </p>
                    </div>
                    <div>
                      <p class="text-ink-500">Target</p>
                      <p class="mt-1 font-semibold text-ink-900">
                        Rp 
                        @if($c->target >= 1000000000)
                          {{ round($c->target / 1000000000, 1) }}M
                        @elseif($c->target >= 1000000)
                          {{ round($c->target / 1000000) }}jt
                        @else
                          {{ number_format($c->target, 0, ',', '.') }}
                        @endif
                      </p>
                    </div>
                    <div>
                      <p class="text-ink-500">Durasi</p>
                      <p class="mt-1 font-semibold text-ink-900">{{ $c->days }} hari</p>
                    </div>
                  </div>
                  <div class="mt-4 rounded-full bg-ink-100 p-1">
                    <div class="h-3 rounded-full bg-brand-500" style="width: {{ $pct }}%"></div>
                  </div>
                </article>
              @empty
                <p class="text-sm text-ink-500 text-center py-4">Belum ada kampanye yang dibuat.</p>
              @endforelse
            </div>
          </div>

          <!-- Aksi Cepat -->
          <aside class="space-y-6 rounded-3xl border border-ink-100 bg-white p-6 shadow-soft">
            <div>
              <p class="text-sm uppercase tracking-[0.2em] text-ink-500">Aksi Cepat</p>
              <h2 class="mt-2 text-2xl font-semibold text-ink-900">Tindakan Harian</h2>
            </div>
            <ul class="space-y-3 text-sm text-ink-700">
              <li class="rounded-3xl border border-ink-100 bg-ink-50 p-4">
                <p class="font-semibold text-ink-900">Bagikan kampanye paling banyak dibaca</p>
                <p class="mt-1 text-ink-600">Tingkatkan visibilitas dengan posting ke media sosial.</p>
              </li>
              <li class="rounded-3xl border border-ink-100 bg-ink-50 p-4">
                <p class="font-semibold text-ink-900">Tanggapi pesan donatur</p>
                <p class="mt-1 text-ink-600">Bangun kepercayaan dengan pembaruan cepat.</p>
              </li>
            </ul>
            <a href="/buat-kampanye" class="mt-4 inline-flex w-full items-center justify-center rounded-full bg-brand-700 px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-brand-800">
              Tambah Kampanye Baru
            </a>
          </aside>
        </div>

        <!-- Aktivitas & Donatur -->
        @php
          $totalDonors = $campaigns->sum('donors');
          $conversionRate = $campaigns->count() > 0 ? min(round(($totalDonors / ($campaigns->count() * 100)) * 100), 100) : 18;
        @endphp
        <div class="space-y-6">
          <div class="rounded-3xl border border-ink-100 bg-white p-6 shadow-soft">
            <p class="text-sm uppercase tracking-[0.2em] text-ink-500">Aktivitas Donasi</p>
            <h2 class="mt-2 text-2xl font-semibold text-ink-900">Ringkasan Transaksi</h2>
            <div class="mt-6 grid gap-4 sm:grid-cols-3">
              <div class="rounded-3xl bg-brand-50 p-4">
                <p class="text-sm text-brand-700">Total Donasi</p>
                <p class="mt-2 text-xl font-semibold text-ink-900">Rp {{ number_format($totalCollected, 0, ',', '.') }}</p>
              </div>
              <div class="rounded-3xl bg-ink-50 p-4">
                <p class="text-sm text-ink-600">Jumlah Donatur</p>
                <p class="mt-2 text-xl font-semibold text-ink-900">{{ $totalDonors }}</p>
              </div>
              <div class="rounded-3xl bg-ink-50 p-4">
                <p class="text-sm text-ink-600">Konversi</p>
                <p class="mt-2 text-xl font-semibold text-ink-900">{{ $conversionRate }}%</p>
              </div>
            </div>
          </div>

          <div class="rounded-3xl border border-ink-100 bg-white p-6 shadow-soft">
            <p class="text-sm uppercase tracking-[0.2em] text-ink-500">Komunitas</p>
            <h2 class="mt-2 text-2xl font-semibold text-ink-900">Donatur Terbaru</h2>
            <ul class="mt-6 space-y-4 text-sm text-ink-700">
              @forelse($recentDonations as $rd)
                <li class="rounded-3xl border border-ink-100 bg-ink-50 p-4">
                  <div class="flex items-center justify-between gap-3">
                    <div>
                      <p class="font-semibold text-ink-900">{{ $rd->is_anonymous ? 'Donatur Anonim' : ($rd->user ? $rd->user->name : 'Donatur Peduli') }}</p>
                      <p class="mt-1 text-ink-600">Rp {{ number_format($rd->amount, 0, ',', '.') }} · {{ $rd->created_at->diffForHumans() }}</p>
                    </div>
                    <span class="text-sm text-brand-700 truncate max-w-[120px]">{{ $rd->campaign ? $rd->campaign->title : 'Donasi' }}</span>
                  </div>
                </li>
              @empty
                <li class="rounded-3xl border border-ink-100 bg-ink-50 p-4 text-center py-4 text-ink-500">
                  Belum ada donatur terbaru.
                </li>
              @endforelse
            </ul>
          </div>
        </div>
      </section>
      <!-- Kembali ke Donatur -->
      <section class="mt-6 rounded-3xl border border-ink-100 bg-ink-50 px-6 py-8 sm:px-10">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
          <div class="flex items-start gap-4">
            <span class="inline-flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-ink-200 text-ink-700">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21s-7.2-4.6-9.6-9C.1 7.2 3.2 3.8 7.1 4.1c1.7.1 3.2 1 3.9 2.2.7-1.2 2.2-2.1 3.9-2.2 3.9-.3 7 3.1 4.7 7.9C19.2 16.4 12 21 12 21z"/></svg>
            </span>
            <div>
              <h2 class="text-base font-semibold text-ink-900">Ingin Kembali Menjadi Donatur?</h2>
              <p class="mt-1 text-sm text-ink-600">Anda bisa beralih kembali ke mode donatur biasa. Kampanye yang sudah dibuat tidak akan terhapus.</p>
              <ul class="mt-3 space-y-1">
                <li class="flex items-center gap-2 text-sm text-ink-700">
                  <svg class="text-ink-500 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                  Kampanye yang ada tetap tersimpan
                </li>
                <li class="flex items-center gap-2 text-sm text-ink-700">
                  <svg class="text-ink-500 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                  Anda akan diarahkan ke dashboard donatur
                </li>
                <li class="flex items-center gap-2 text-sm text-ink-700">
                  <svg class="text-ink-500 shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M9 16.17 4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                  Bisa beralih kembali ke fundraiser kapan saja
                </li>
              </ul>
            </div>
          </div>
          <div class="shrink-0">
            <button id="btnJadiDonatur" type="button"
              class="inline-flex h-11 items-center justify-center rounded-full border border-ink-300 bg-white px-6 text-sm font-semibold text-ink-700 shadow-sm hover:bg-ink-100 whitespace-nowrap">
              Kembali ke Donatur
            </button>
          </div>
        </div>
      </section>
    </main>

    <!-- Modal Konfirmasi Kembali ke Donatur -->
    <div id="modalDonatur" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm p-4">
      <div class="w-full max-w-md rounded-3xl bg-white shadow-soft p-8">
        <div class="flex justify-center mb-4">
          <span class="inline-flex h-14 w-14 items-center justify-center rounded-full bg-ink-100 text-ink-700">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M12 21s-7.2-4.6-9.6-9C.1 7.2 3.2 3.8 7.1 4.1c1.7.1 3.2 1 3.9 2.2.7-1.2 2.2-2.1 3.9-2.2 3.9-.3 7 3.1 4.7 7.9C19.2 16.4 12 21 12 21z"/></svg>
          </span>
        </div>
        <h3 class="text-center text-lg font-semibold text-ink-900">Konfirmasi Perpindahan Peran</h3>
        <p class="mt-2 text-center text-sm text-ink-600">
          Apakah Anda yakin ingin beralih kembali menjadi <strong>Donatur</strong>? Kampanye yang sudah dibuat tidak akan terhapus.
        </p>
        <div class="mt-6 flex flex-col sm:flex-row gap-3">
          <button id="btnBatalDonatur" type="button"
            class="flex-1 h-11 rounded-full border border-ink-200 text-sm font-semibold text-ink-700 hover:bg-ink-50">
            Batal
          </button>
          <button id="btnKonfirmasiDonatur" type="button"
            class="flex-1 h-11 rounded-full bg-ink-900 text-sm font-semibold text-white hover:bg-ink-700">
            Ya, Kembali ke Donatur
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
        if (!user) { window.location.href = '/'; return; }
        if (user.role !== 'fundraiser' && user.role !== 'admin') {
          window.location.href = getDashboardUrl(user.role); return;
        }

        document.getElementById('welcomeName').textContent = 'Kelola Kampanye, ' + user.name;
        document.getElementById('userInitial').textContent = user.name.charAt(0).toUpperCase();
        document.getElementById('menuUserName').textContent = user.name;
        document.getElementById('menuUserEmail').textContent = user.email;
        document.getElementById('menuUserRole').textContent = 'Fundraiser';

        const btn = document.getElementById('userMenuButton');
        const menu = document.getElementById('userMenu');
        btn.addEventListener('click', function (e) { e.stopPropagation(); menu.classList.toggle('hidden'); });
        document.addEventListener('click', function () { menu.classList.add('hidden'); });

        // Kembali ke Donatur
        const modal = document.getElementById('modalDonatur');

        document.getElementById('btnJadiDonatur').addEventListener('click', function () {
          modal.classList.remove('hidden');
          modal.classList.add('flex');
        });

        document.getElementById('btnBatalDonatur').addEventListener('click', function () {
          modal.classList.add('hidden');
          modal.classList.remove('flex');
        });

        modal.addEventListener('click', function (e) {
          if (e.target === modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
          }
        });

        document.getElementById('btnKonfirmasiDonatur').addEventListener('click', async function () {
          try {
            const response = await fetch('/switch-role', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              },
              body: JSON.stringify({ role: 'donatur' })
            });
            const result = await response.json();
            if (result.ok) {
              const currentUser = getCurrentUser();
              if (currentUser) {
                currentUser.role = 'donatur';
                localStorage.setItem('bantuin_current_user', JSON.stringify(currentUser));
              }
              window.location.href = '/donatur';
            }
          } catch (e) {
            console.error(e);
          }
        });
      });
    </script>
  </body>
</html>
