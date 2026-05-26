<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Donasi — BantuIn</title>
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
      /* Transitions */
      .step-panel { display:none; }
      .step-panel.active { display:block; }
      @keyframes fadeUp { from{opacity:0;transform:translateY(14px);}to{opacity:1;transform:translateY(0);} }
      .fade-up { animation:fadeUp .4s ease forwards; }

      /* Nominal chip */
      .chip { cursor:pointer; transition:all .18s ease; }
      .chip.selected { background:#be124c; color:#fff; border-color:#be124c; }

      /* Payment method card */
      .pay-card { cursor:pointer; transition:all .18s ease; }
      .pay-card.selected { border-color:#be124c; background:#fff1f4; }
      .pay-card.selected .pay-dot { background:#be124c; border-color:#be124c; }
      .pay-dot { width:18px;height:18px;border-radius:50%;border:2px solid #cbd5e1;transition:all .18s ease;flex-shrink:0; }

      /* Stepper */
      .stepper-item.done .step-num { background:#be124c; color:#fff; }
      .stepper-item.active .step-num { background:#0f172a; color:#fff; }
      .stepper-item .step-num { background:#f1f5f9; color:#64748b; }

      /* Progress bar */
      .prog-fill { transition:width 1s ease; }

      /* Input focus */
      input:focus, select:focus, textarea:focus { outline:none; box-shadow:0 0 0 2px #be124c55; }

      /* Sukses animation */
      @keyframes pop { 0%{transform:scale(0.6);opacity:0} 70%{transform:scale(1.1)} 100%{transform:scale(1);opacity:1} }
      .pop-in { animation:pop .5s ease forwards; }

      /* Loading spinner */
      @keyframes spin { to{transform:rotate(360deg)} }
      .spinner { animation:spin .8s linear infinite; }
    </style>
  </head>

  <body class="bg-ink-50 text-ink-900 antialiased">
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
            <a class="hover:text-ink-900" href="/dampak">Dampak Kami</a>
            <a class="hover:text-ink-900" href="/#kontak">Kontak</a>
          </nav>
          <div class="flex items-center gap-2">
            <!-- Login button: shown when logged out -->
            <button id="btnLogin" type="button" data-open-auth
              class="hidden sm:inline-flex h-10 items-center justify-center rounded-full px-5 text-sm font-medium text-ink-700 hover:bg-ink-50">
              Masuk
            </button>
            <!-- User menu: shown when logged in -->
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
            <a href="/kampanye" class="inline-flex h-10 items-center justify-center rounded-full bg-brand-700 px-5 text-sm font-semibold text-white shadow-sm hover:bg-brand-800">
              Lihat Kampanye
            </a>
          </div>
        </div>
      </div>
    </header>

    <!-- ═══ LOGIN WALL (shown if not logged in) ═══ -->
    <div id="loginWall" class="hidden min-h-[80vh] flex items-center justify-center px-4 py-20">
      <div class="w-full max-w-md text-center fade-up">
        <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-brand-50 mb-6">
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2Zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3Zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22Z" fill="#be124c"/>
          </svg>
        </div>
        <h2 class="text-2xl font-semibold text-ink-900">Masuk untuk Berdonasi</h2>
        <p class="mt-3 text-sm text-ink-600 leading-relaxed max-w-sm mx-auto">
          Anda perlu masuk atau membuat akun terlebih dahulu untuk melanjutkan donasi. Bergabung gratis dan mulai membantu sesama!
        </p>
        <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
          <button data-open-auth data-tab="login"
            class="inline-flex h-11 items-center justify-center rounded-full bg-brand-700 px-7 text-sm font-semibold text-white hover:bg-brand-800">
            Masuk ke Akun
          </button>
          <button data-open-auth data-tab="register"
            class="inline-flex h-11 items-center justify-center rounded-full border border-ink-200 bg-white px-7 text-sm font-semibold text-ink-700 hover:bg-ink-50">
            Daftar Gratis
          </button>
        </div>
        <p class="mt-6 text-xs text-ink-400">
          Dengan mendaftar, Anda menyetujui <a href="#" class="text-brand-700 hover:underline">Syarat & Ketentuan</a> BantuIn
        </p>
      </div>
    </div>

    <!-- ═══ MAIN DONATION FLOW (shown if logged in) ═══ -->
    <div id="donationFlow" class="hidden">

      <!-- Page header -->
      <div class="bg-white border-b border-ink-100">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-5">
          <div class="flex items-center gap-3 text-sm text-ink-500">
            <a href="/kampanye" class="hover:text-ink-700">Kampanye</a>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            <span id="breadcrumbTitle" class="text-ink-700 font-medium truncate max-w-xs">Donasi</span>
          </div>
        </div>
      </div>

      <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid gap-8 lg:grid-cols-[1fr_380px] items-start">

          <!-- LEFT: Stepper + Form -->
          <div>
            <!-- Stepper -->
            <div class="bg-white rounded-2xl border border-ink-100 p-5 mb-6 shadow-sm">
              <div class="flex items-center">
                <!-- Step 1 -->
                <div class="stepper-item flex flex-col items-center" id="step1Item">
                  <div class="step-num inline-flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold">1</div>
                  <span class="text-xs mt-1 font-medium text-ink-600">Nominal</span>
                </div>
                <div class="flex-1 h-px bg-ink-200 mx-2" id="line12"></div>
                <!-- Step 2 -->
                <div class="stepper-item flex flex-col items-center" id="step2Item">
                  <div class="step-num inline-flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold">2</div>
                  <span class="text-xs mt-1 font-medium text-ink-500">Pembayaran</span>
                </div>
                <div class="flex-1 h-px bg-ink-200 mx-2" id="line23"></div>
                <!-- Step 3 -->
                <div class="stepper-item flex flex-col items-center" id="step3Item">
                  <div class="step-num inline-flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold">3</div>
                  <span class="text-xs mt-1 font-medium text-ink-500">Konfirmasi</span>
                </div>
              </div>
            </div>

            <!-- ── STEP 1: Pilih Nominal ── -->
            <div id="stepPanel1" class="step-panel active fade-up">
              <div class="bg-white rounded-2xl border border-ink-100 p-6 shadow-sm">
                <h2 class="text-lg font-semibold">Pilih Nominal Donasi</h2>
                <p class="mt-1 text-sm text-ink-500">Pilih atau ketik nominal yang ingin Anda donasikan</p>

                <!-- Quick chips -->
                <div class="mt-6 grid grid-cols-3 gap-3">
                  <button type="button" class="chip rounded-2xl border border-ink-200 py-3 text-sm font-semibold text-ink-700 hover:border-brand-300 hover:bg-brand-50" data-val="25000">Rp 25.000</button>
                  <button type="button" class="chip rounded-2xl border border-ink-200 py-3 text-sm font-semibold text-ink-700 hover:border-brand-300 hover:bg-brand-50" data-val="50000">Rp 50.000</button>
                  <button type="button" class="chip rounded-2xl border border-ink-200 py-3 text-sm font-semibold text-ink-700 hover:border-brand-300 hover:bg-brand-50" data-val="100000">Rp 100.000</button>
                  <button type="button" class="chip rounded-2xl border border-ink-200 py-3 text-sm font-semibold text-ink-700 hover:border-brand-300 hover:bg-brand-50" data-val="250000">Rp 250.000</button>
                  <button type="button" class="chip rounded-2xl border border-ink-200 py-3 text-sm font-semibold text-ink-700 hover:border-brand-300 hover:bg-brand-50" data-val="500000">Rp 500.000</button>
                  <button type="button" class="chip rounded-2xl border border-ink-200 py-3 text-sm font-semibold text-ink-700 hover:border-brand-300 hover:bg-brand-50" data-val="1000000">Rp 1.000.000</button>
                </div>

                <!-- Custom input -->
                <div class="mt-4">
                  <label class="text-xs font-medium text-ink-700">Atau masukkan nominal lain</label>
                  <div class="mt-1.5 flex items-center gap-0 rounded-2xl bg-ink-50 ring-1 ring-transparent focus-within:ring-brand-300 focus-within:bg-white">
                    <span class="pl-4 text-sm font-semibold text-ink-500 select-none">Rp</span>
                    <input id="customAmount" type="number" min="5000" step="1000"
                      class="flex-1 h-12 bg-transparent px-3 text-sm font-semibold text-ink-900 outline-none"
                      placeholder="Contoh: 75000"/>
                  </div>
                  <p class="mt-1 text-xs text-ink-400">Minimal donasi Rp 5.000</p>
                </div>

                <!-- Pesan opsional -->
                <div class="mt-5">
                  <label class="text-xs font-medium text-ink-700">Pesan untuk Penggalang Dana <span class="text-ink-400">(opsional)</span></label>
                  <textarea id="donorMessage" rows="3"
                    class="mt-1.5 w-full rounded-2xl bg-ink-50 px-4 py-3 text-sm text-ink-900 outline-none ring-1 ring-transparent focus:bg-white focus:ring-brand-300 resize-none"
                    placeholder="Tuliskan semangat, doa, atau pesan Anda..."></textarea>
                </div>

                <!-- Anonim toggle -->
                <div class="mt-4 flex items-center gap-3">
                  <button type="button" id="anonToggle" onclick="toggleAnon()"
                    class="relative inline-flex h-6 w-11 flex-shrink-0 rounded-full border-2 border-transparent bg-ink-200 transition-colors duration-200 focus:outline-none"
                    role="switch" aria-checked="false">
                    <span class="translate-x-0 inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out" id="anonKnob"></span>
                  </button>
                  <div>
                    <div class="text-sm font-medium text-ink-700">Donasi sebagai Anonim</div>
                    <div class="text-xs text-ink-400">Nama Anda tidak akan ditampilkan di daftar donatur</div>
                  </div>
                </div>

                <!-- Error -->
                <div id="step1Error" class="hidden mt-4 rounded-xl bg-red-50 px-4 py-2 text-sm text-red-700"></div>

                <button type="button" onclick="goToStep(2)"
                  class="mt-6 inline-flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-brand-700 text-sm font-semibold text-white hover:bg-brand-800">
                  Lanjut ke Pembayaran
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                </button>
              </div>
            </div>

            <!-- ── STEP 2: Metode Pembayaran ── -->
            <div id="stepPanel2" class="step-panel fade-up">
              <div class="bg-white rounded-2xl border border-ink-100 p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-1">
                  <button type="button" onclick="goToStep(1)" class="inline-flex h-8 w-8 items-center justify-center rounded-full hover:bg-ink-50 text-ink-500">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                  </button>
                  <h2 class="text-lg font-semibold">Metode Pembayaran</h2>
                </div>
                <p class="ml-11 text-sm text-ink-500 mb-6">Pilih cara Anda ingin membayar</p>

                <!-- Transfer Bank -->
                <div class="mb-4">
                  <p class="text-xs font-semibold uppercase tracking-widest text-ink-400 mb-3">Transfer Bank</p>
                  <div class="space-y-3">
                    <label class="pay-card flex items-center gap-4 rounded-2xl border border-ink-200 p-4 cursor-pointer" onclick="selectPayment(this,'bca')">
                      <div class="pay-dot"></div>
                      <div class="flex items-center gap-3 flex-1">
                        <div class="inline-flex h-10 w-16 items-center justify-center rounded-xl bg-blue-600 text-white text-xs font-bold">BCA</div>
                        <div>
                          <div class="text-sm font-semibold">Bank BCA</div>
                          <div class="text-xs text-ink-500">Transfer via ATM / m-Banking / Internet Banking</div>
                        </div>
                      </div>
                    </label>
                    <label class="pay-card flex items-center gap-4 rounded-2xl border border-ink-200 p-4 cursor-pointer" onclick="selectPayment(this,'bni')">
                      <div class="pay-dot"></div>
                      <div class="flex items-center gap-3 flex-1">
                        <div class="inline-flex h-10 w-16 items-center justify-center rounded-xl bg-orange-500 text-white text-xs font-bold">BNI</div>
                        <div>
                          <div class="text-sm font-semibold">Bank BNI</div>
                          <div class="text-xs text-ink-500">Transfer via ATM / m-Banking / Internet Banking</div>
                        </div>
                      </div>
                    </label>
                    <label class="pay-card flex items-center gap-4 rounded-2xl border border-ink-200 p-4 cursor-pointer" onclick="selectPayment(this,'mandiri')">
                      <div class="pay-dot"></div>
                      <div class="flex items-center gap-3 flex-1">
                        <div class="inline-flex h-10 w-16 items-center justify-center rounded-xl bg-yellow-500 text-white text-xs font-bold">MDR</div>
                        <div>
                          <div class="text-sm font-semibold">Bank Mandiri</div>
                          <div class="text-xs text-ink-500">Transfer via ATM / Livin by Mandiri</div>
                        </div>
                      </div>
                    </label>
                  </div>
                </div>

                <!-- E-Wallet -->
                <div class="mb-4">
                  <p class="text-xs font-semibold uppercase tracking-widest text-ink-400 mb-3">Dompet Digital</p>
                  <div class="space-y-3">
                    <label class="pay-card flex items-center gap-4 rounded-2xl border border-ink-200 p-4 cursor-pointer" onclick="selectPayment(this,'gopay')">
                      <div class="pay-dot"></div>
                      <div class="flex items-center gap-3 flex-1">
                        <div class="inline-flex h-10 w-16 items-center justify-center rounded-xl bg-green-500 text-white text-xs font-bold">GoPay</div>
                        <div>
                          <div class="text-sm font-semibold">GoPay</div>
                          <div class="text-xs text-ink-500">Bayar langsung via aplikasi Gojek</div>
                        </div>
                      </div>
                    </label>
                    <label class="pay-card flex items-center gap-4 rounded-2xl border border-ink-200 p-4 cursor-pointer" onclick="selectPayment(this,'ovo')">
                      <div class="pay-dot"></div>
                      <div class="flex items-center gap-3 flex-1">
                        <div class="inline-flex h-10 w-16 items-center justify-center rounded-xl bg-purple-600 text-white text-xs font-bold">OVO</div>
                        <div>
                          <div class="text-sm font-semibold">OVO</div>
                          <div class="text-xs text-ink-500">Bayar langsung via aplikasi OVO</div>
                        </div>
                      </div>
                    </label>
                    <label class="pay-card flex items-center gap-4 rounded-2xl border border-ink-200 p-4 cursor-pointer" onclick="selectPayment(this,'dana')">
                      <div class="pay-dot"></div>
                      <div class="flex items-center gap-3 flex-1">
                        <div class="inline-flex h-10 w-16 items-center justify-center rounded-xl bg-blue-500 text-white text-xs font-bold">DANA</div>
                        <div>
                          <div class="text-sm font-semibold">DANA</div>
                          <div class="text-xs text-ink-500">Bayar langsung via aplikasi DANA</div>
                        </div>
                      </div>
                    </label>
                  </div>
                </div>

                <!-- QRIS -->
                <div>
                  <p class="text-xs font-semibold uppercase tracking-widest text-ink-400 mb-3">Lainnya</p>
                  <label class="pay-card flex items-center gap-4 rounded-2xl border border-ink-200 p-4 cursor-pointer" onclick="selectPayment(this,'qris')">
                    <div class="pay-dot"></div>
                    <div class="flex items-center gap-3 flex-1">
                      <div class="inline-flex h-10 w-16 items-center justify-center rounded-xl bg-ink-900 text-white text-xs font-bold">QRIS</div>
                      <div>
                        <div class="text-sm font-semibold">QRIS</div>
                        <div class="text-xs text-ink-500">Scan QR dengan aplikasi apapun</div>
                      </div>
                    </div>
                  </label>
                </div>

                <div id="step2Error" class="hidden mt-4 rounded-xl bg-red-50 px-4 py-2 text-sm text-red-700"></div>

                <button type="button" onclick="goToStep(3)"
                  class="mt-6 inline-flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-brand-700 text-sm font-semibold text-white hover:bg-brand-800">
                  Lanjut ke Konfirmasi
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
                </button>
              </div>
            </div>

            <!-- ── STEP 3: Konfirmasi ── -->
            <div id="stepPanel3" class="step-panel fade-up">
              <div class="bg-white rounded-2xl border border-ink-100 p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-1">
                  <button type="button" onclick="goToStep(2)" class="inline-flex h-8 w-8 items-center justify-center rounded-full hover:bg-ink-50 text-ink-500">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M15 6l-6 6 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                  </button>
                  <h2 class="text-lg font-semibold">Konfirmasi Donasi</h2>
                </div>
                <p class="ml-11 text-sm text-ink-500 mb-6">Periksa kembali detail donasi Anda sebelum mengirim</p>

                <!-- Ringkasan -->
                <div class="rounded-2xl bg-ink-50 p-5 space-y-4">
                  <div class="flex items-start justify-between gap-4">
                    <span class="text-sm text-ink-500">Kampanye</span>
                    <span id="conf-campaign" class="text-sm font-semibold text-right text-ink-900 max-w-[55%]">—</span>
                  </div>
                  <div class="flex items-center justify-between gap-4">
                    <span class="text-sm text-ink-500">Donatur</span>
                    <span id="conf-name" class="text-sm font-semibold text-ink-900">—</span>
                  </div>
                  <div class="flex items-center justify-between gap-4">
                    <span class="text-sm text-ink-500">Metode Bayar</span>
                    <span id="conf-method" class="text-sm font-semibold text-ink-900">—</span>
                  </div>
                  <div class="border-t border-ink-200"></div>
                  <div class="flex items-center justify-between gap-4">
                    <span class="text-sm text-ink-500">Nominal Donasi</span>
                    <span id="conf-amount" class="text-sm font-semibold text-ink-900">—</span>
                  </div>
                  <div class="flex items-center justify-between gap-4">
                    <span class="text-sm text-ink-500">Biaya Layanan</span>
                    <span class="text-sm font-semibold text-green-600">Gratis</span>
                  </div>
                  <div class="border-t border-ink-200"></div>
                  <div class="flex items-center justify-between gap-4">
                    <span class="text-sm font-semibold text-ink-900">Total Pembayaran</span>
                    <span id="conf-total" class="text-base font-semibold text-brand-700">—</span>
                  </div>
                </div>

                <!-- Pesan preview -->
                <div id="conf-message-wrap" class="hidden mt-4 rounded-2xl bg-ink-50 p-4">
                  <p class="text-xs font-medium text-ink-500 mb-1">Pesan Anda</p>
                  <p id="conf-message-text" class="text-sm text-ink-700 italic"></p>
                </div>

                <!-- Terms checkbox -->
                <div class="mt-5 flex items-start gap-3">
                  <button type="button" id="termsToggle" onclick="toggleTerms()"
                    class="mt-0.5 relative inline-flex h-5 w-5 flex-shrink-0 rounded border-2 border-ink-200 bg-white transition-colors" role="checkbox" aria-checked="false">
                    <svg id="termsCheck" class="hidden h-full w-full text-white" viewBox="0 0 16 16" fill="currentColor"><path d="M13.3 3.3a1 1 0 0 0-1.4 0L6 9.2 4.1 7.3a1 1 0 0 0-1.4 1.4l2.6 2.6a1 1 0 0 0 1.4 0l6.6-6.6a1 1 0 0 0 0-1.4Z"/></svg>
                  </button>
                  <p class="text-xs text-ink-600 leading-relaxed">
                    Saya menyetujui <a href="#" class="text-brand-700 hover:underline">Syarat & Ketentuan</a> BantuIn dan menyatakan bahwa donasi ini sah serta tidak melanggar hukum yang berlaku.
                  </p>
                </div>

                <div id="step3Error" class="hidden mt-4 rounded-xl bg-red-50 px-4 py-2 text-sm text-red-700"></div>

                <button type="button" id="btnSubmit" onclick="submitDonation()"
                  class="mt-6 inline-flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-brand-700 text-sm font-semibold text-white hover:bg-brand-800 disabled:opacity-60">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="flex-shrink-0"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2Zm-1 13l-4-4 1.4-1.4L11 12.2l5.6-5.6L18 8l-7 7Z" fill="currentColor"/></svg>
                  Kirim Donasi Sekarang
                </button>
              </div>
            </div>

            <!-- ── STEP 4: Sukses ── -->
            <div id="stepPanel4" class="step-panel">
              <div class="bg-white rounded-2xl border border-ink-100 p-8 shadow-sm text-center">
                <div class="pop-in inline-flex h-20 w-20 items-center justify-center rounded-full bg-green-50 mb-5">
                  <svg width="40" height="40" viewBox="0 0 24 24" fill="none">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" stroke="#16a34a" stroke-width="2" stroke-linecap="round"/>
                    <path d="M22 4 12 14.01l-3-3" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </div>
                <h2 class="text-2xl font-semibold text-ink-900">Donasi Berhasil Dikirim!</h2>
                <p class="mt-3 text-sm text-ink-600 max-w-sm mx-auto leading-relaxed">
                  Terima kasih atas kebaikan Anda. Donasi Anda sedang diproses dan akan segera tersalurkan kepada yang membutuhkan.
                </p>

                <!-- Receipt card -->
                <div class="mt-6 rounded-2xl bg-ink-50 p-5 text-left space-y-3 max-w-xs mx-auto">
                  <div class="flex justify-between text-sm"><span class="text-ink-500">No. Transaksi</span><span id="rcpt-id" class="font-semibold font-mono text-ink-900 text-xs"></span></div>
                  <div class="flex justify-between text-sm"><span class="text-ink-500">Nominal</span><span id="rcpt-amount" class="font-semibold text-brand-700"></span></div>
                  <div class="flex justify-between text-sm"><span class="text-ink-500">Metode</span><span id="rcpt-method" class="font-semibold text-ink-900"></span></div>
                  <div class="flex justify-between text-sm"><span class="text-ink-500">Tanggal</span><span id="rcpt-date" class="font-semibold text-ink-900 text-xs"></span></div>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row gap-3 justify-center">
                  <a href="/donatur"
                    class="inline-flex h-11 items-center justify-center rounded-full bg-brand-700 px-7 text-sm font-semibold text-white hover:bg-brand-800">
                    Lihat Dashboard Saya
                  </a>
                  <a href="/kampanye"
                    class="inline-flex h-11 items-center justify-center rounded-full border border-ink-200 bg-white px-7 text-sm font-semibold text-ink-700 hover:bg-ink-50">
                    Donasi Kampanye Lain
                  </a>
                </div>

                <!-- Share -->
                <div class="mt-8 pt-6 border-t border-ink-100">
                  <p class="text-xs text-ink-500 mb-3">Bagikan kebaikan ini kepada teman-teman Anda</p>
                  <div class="flex justify-center gap-3">
                    <button onclick="shareWA()" class="inline-flex items-center gap-2 rounded-full bg-green-50 px-4 py-2 text-xs font-semibold text-green-700 hover:bg-green-100">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347Z"/></svg>
                      WhatsApp
                    </button>
                    <button onclick="shareTW()" class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-4 py-2 text-xs font-semibold text-blue-700 hover:bg-blue-100">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.742l7.332-8.423L1.88 2.25H8.08l4.253 5.622 5.91-5.622Zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                      Twitter
                    </button>
                    <button onclick="copyLink()" id="btnCopy" class="inline-flex items-center gap-2 rounded-full bg-ink-50 px-4 py-2 text-xs font-semibold text-ink-700 hover:bg-ink-100">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M8 5H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1M8 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M8 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m0 0h2a2 2 0 0 1 2 2v3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                      Salin Link
                    </button>
                  </div>
                </div>
              </div>
            </div>

          </div><!-- end LEFT -->

          <!-- RIGHT: Campaign Card + Summary -->
          <div class="space-y-5 lg:sticky lg:top-24">

            <!-- Campaign info card -->
            <div class="bg-white rounded-2xl border border-ink-100 shadow-sm overflow-hidden">
              <div id="campImg" class="h-36 w-full bg-ink-100 bg-center bg-cover"></div>
              <div class="p-5">
                <span id="campCat" class="inline-flex rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 mb-2"></span>
                <h3 id="campTitle" class="text-sm font-semibold text-ink-900 leading-snug"></h3>
                <div class="mt-1 flex items-center gap-2">
                  <div id="campFrAvatar" class="h-5 w-5 rounded-full bg-brand-50 flex items-center justify-center text-brand-800 text-xs font-bold flex-shrink-0"></div>
                  <span id="campFrName" class="text-xs text-ink-500 truncate"></span>
                </div>
                <!-- Progress -->
                <div class="mt-4">
                  <div class="h-2 rounded-full bg-ink-100">
                    <div id="campProgBar" class="h-2 rounded-full bg-brand-700 prog-fill" style="width:0%"></div>
                  </div>
                  <div class="mt-2 flex justify-between text-xs text-ink-600">
                    <span><strong id="campCollected" class="text-ink-900"></strong> terkumpul</span>
                    <span id="campPct" class="font-semibold text-brand-700"></span>
                  </div>
                  <div class="text-xs text-ink-400 mt-0.5">dari <span id="campTarget"></span> target · <span id="campDays" class="text-orange-600 font-medium"></span> hari lagi</div>
                </div>
              </div>
            </div>

            <!-- Donation summary (sticky) -->
            <div id="donateSummaryBox" class="bg-white rounded-2xl border border-ink-100 shadow-sm p-5">
              <h3 class="text-sm font-semibold text-ink-700 mb-4">Ringkasan Donasi</h3>
              <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                  <span class="text-ink-500">Nominal</span>
                  <span id="sum-amount" class="font-semibold text-ink-900">—</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-ink-500">Biaya Layanan</span>
                  <span class="font-semibold text-green-600">Gratis</span>
                </div>
                <div class="flex justify-between">
                  <span class="text-ink-500">Metode</span>
                  <span id="sum-method" class="font-semibold text-ink-900">—</span>
                </div>
                <div class="border-t border-ink-100 pt-3 flex justify-between">
                  <span class="font-semibold text-ink-900">Total</span>
                  <span id="sum-total" class="font-semibold text-brand-700">—</span>
                </div>
              </div>
            </div>

            <!-- Trust badges -->
            <div class="rounded-2xl border border-ink-100 bg-white p-5">
              <p class="text-xs font-semibold text-ink-500 mb-3">Donasi Aman & Terpercaya</p>
              <div class="space-y-2 text-xs text-ink-600">
                <div class="flex items-center gap-2"><span class="text-green-500">✓</span> 100% tersalurkan ke penerima</div>
                <div class="flex items-center gap-2"><span class="text-green-500">✓</span> Terverifikasi & transparan</div>
                <div class="flex items-center gap-2"><span class="text-green-500">✓</span> Laporan disediakan berkala</div>
                <div class="flex items-center gap-2"><span class="text-green-500">✓</span> Terdaftar di Kemensos RI</div>
              </div>
            </div>
          </div><!-- end RIGHT -->

        </div>
      </div>
    </div><!-- end donationFlow -->

    <!-- ═══ AUTH MODAL ═══ -->
    <div id="authModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="authTitle">
      <div class="absolute inset-0 bg-black/55" data-close-auth></div>
      <div class="relative mx-auto flex min-h-screen max-w-3xl items-center justify-center p-4">
        <div class="w-full max-w-md rounded-3xl bg-white shadow-soft">
          <div class="flex items-center justify-between px-6 pt-6">
            <div>
              <h3 id="authTitle" class="text-base font-semibold">Selamat Datang di BantuIn</h3>
              <p class="mt-1 text-sm text-ink-600">Masuk atau daftar untuk mulai berdonasi</p>
            </div>
            <button class="inline-flex h-10 w-10 items-center justify-center rounded-full hover:bg-ink-50" type="button" data-close-auth aria-label="Tutup">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
          </div>
          <div class="px-6 pb-6">
            <div class="mt-5 rounded-full bg-ink-50 p-1">
              <div class="grid grid-cols-2 gap-1">
                <button id="tabLogin" class="h-10 rounded-full bg-white text-sm font-semibold text-ink-900 shadow-sm" type="button">Masuk</button>
                <button id="tabRegister" class="h-10 rounded-full text-sm font-semibold text-ink-700 hover:bg-white/60" type="button">Daftar</button>
              </div>
            </div>
            <div id="authError" class="hidden mt-3 rounded-xl bg-red-50 px-4 py-2 text-sm text-red-700"></div>
            <div id="authSuccess" class="hidden mt-3 rounded-xl bg-green-50 px-4 py-2 text-sm text-green-700"></div>
            <form id="loginForm" class="mt-5 space-y-3">
              <div>
                <label class="text-xs font-medium text-ink-700">Email</label>
                <input id="loginEmail" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none ring-1 ring-transparent focus:bg-white focus:ring-ink-200" placeholder="nama@email.com" type="email" required/>
              </div>
              <div>
                <label class="text-xs font-medium text-ink-700">Password</label>
                <input id="loginPassword" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none ring-1 ring-transparent focus:bg-white focus:ring-ink-200" placeholder="Masukkan password" type="password" required/>
              </div>
              <button class="mt-2 inline-flex h-11 w-full items-center justify-center rounded-2xl bg-brand-700 text-sm font-semibold text-white hover:bg-brand-800" type="submit">Masuk</button>
            </form>
            <form id="registerForm" class="mt-5 space-y-3 hidden">
              <div>
                <label class="text-xs font-medium text-ink-700">Nama Lengkap</label>
                <input id="regName" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none ring-1 ring-transparent focus:bg-white focus:ring-ink-200" placeholder="Nama Anda" type="text" required/>
              </div>
              <div>
                <label class="text-xs font-medium text-ink-700">Email</label>
                <input id="regEmail" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none ring-1 ring-transparent focus:bg-white focus:ring-ink-200" placeholder="nama@email.com" type="email" required/>
              </div>
              <div>
                <label class="text-xs font-medium text-ink-700">Peran</label>
                <select id="regRole" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none ring-1 ring-transparent focus:bg-white focus:ring-ink-200">
                  <option value="donatur">Donatur</option>
                  <option value="fundraiser">Fundraiser</option>
                </select>
              </div>
              <div>
                <label class="text-xs font-medium text-ink-700">Password</label>
                <input id="regPassword" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none ring-1 ring-transparent focus:bg-white focus:ring-ink-200" placeholder="Minimal 6 karakter" type="password" minlength="6" required/>
              </div>
              <div>
                <label class="text-xs font-medium text-ink-700">Konfirmasi Password</label>
                <input id="regConfirm" class="mt-1 h-11 w-full rounded-2xl bg-ink-50 px-4 text-sm outline-none ring-1 ring-transparent focus:bg-white focus:ring-ink-200" placeholder="Ulangi password" type="password" minlength="6" required/>
              </div>
              <button class="mt-2 inline-flex h-11 w-full items-center justify-center rounded-2xl bg-brand-700 text-sm font-semibold text-white hover:bg-brand-800" type="submit">Daftar</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <footer class="border-t border-ink-100 bg-white mt-12">
      <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 py-6 sm:px-6 lg:px-8 lg:flex-row lg:items-center lg:justify-between">
        <p class="text-sm text-ink-600">© <span id="year"></span> BantuIn. Bersama kita berbagi dan mengubah masa depan.</p>
        <div class="flex flex-wrap gap-4 text-sm text-ink-500">
          <a href="/kampanye" class="hover:text-ink-900">Kampanye</a>
          <a href="/dampak" class="hover:text-ink-900">Dampak Kami</a>
          <a href="/#tentang" class="hover:text-ink-900">Tentang</a>
          <a href="/#kontak" class="hover:text-ink-900">Kontak</a>
        </div>
      </div>
    </footer>

    <!-- ═══════════════════════════════════════════ -->
    <!--  JAVASCRIPT                                 -->
    <!-- ═══════════════════════════════════════════ -->
    <script>
    // ─────────────────────────────────────────────
    // ── Data bawaan (dari database) ──
    const CAMPAIGNS = @json($campaigns);

    const PAY_LABELS = { bca:'Bank BCA', bni:'Bank BNI', mandiri:'Bank Mandiri', gopay:'GoPay', ovo:'OVO', dana:'DANA', qris:'QRIS' };

    // ─────────────────────────────────────────────
    // STATE
    // ─────────────────────────────────────────────
    let STATE = {
      campaignId: null,
      amount: 0,
      method: '',
      message: '',
      anon: false,
      termsAccepted: false,
      currentStep: 1,
    };

    function fmt(n) { return 'Rp ' + Number(n).toLocaleString('id'); }
    function fmtJt(n) {
      if (n >= 1000000000) return 'Rp ' + (n/1000000000).toFixed(n%1000000000===0?0:1).replace('.0','') + 'M';
      if (n >= 1000000) return 'Rp ' + (n/1000000).toFixed(n%1000000===0?0:1).replace('.0','') + 'jt';
      return 'Rp ' + Number(n).toLocaleString('id');
    }

    // ─────────────────────────────────────────────
    // AUTH HELPERS
    // ─────────────────────────────────────────────
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
    function getDashboardUrl(r) { return r==='admin'?'/admin':r==='fundraiser'?'/fundraiser':'/donatur'; }

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

    // ─────────────────────────────────────────────
    // NAVBAR
    // ─────────────────────────────────────────────
    function updateNavbar() {
      const user = getCurrentUser();
      const btnLogin = document.getElementById('btnLogin');
      const wrapper = document.getElementById('userMenuWrapper');
      if (user) {
        if (btnLogin) btnLogin.style.display = 'none';
        if (wrapper) { wrapper.classList.remove('hidden'); }
        const s = (id, v) => { const e=document.getElementById(id); if(e) e.textContent=v; };
        s('userInitial', user.name.charAt(0).toUpperCase());
        s('menuUserName', user.name);
        s('menuUserEmail', user.email);
        s('menuUserRole', user.role.charAt(0).toUpperCase()+user.role.slice(1));
        const dl = document.getElementById('menuDashboardLink');
        if (dl) dl.href = getDashboardUrl(user.role);
      } else {
        if (btnLogin) btnLogin.style.display = '';
        if (wrapper) wrapper.classList.add('hidden');
      }
    }

    // ─────────────────────────────────────────────
    // AUTH MODAL
    // ─────────────────────────────────────────────
    function openAuthModal(tab) {
      document.getElementById('authModal').classList.remove('hidden');
      document.body.style.overflow = 'hidden';
      clearAuthMsg();
      setTab(tab || 'login');
    }
    function closeAuthModal() {
      document.getElementById('authModal').classList.add('hidden');
      document.body.style.overflow = '';
    }
    function clearAuthMsg() {
      ['authError','authSuccess'].forEach(id => {
        const e = document.getElementById(id);
        if (e) { e.classList.add('hidden'); e.textContent = ''; }
      });
    }
    function showErr(msg) { const e=document.getElementById('authError'); if(e){e.textContent=msg;e.classList.remove('hidden');} }
    function showOk(msg) { const e=document.getElementById('authSuccess'); if(e){e.textContent=msg;e.classList.remove('hidden');} }
    function setTab(tab) {
      clearAuthMsg();
      const isLogin = tab==='login';
      document.getElementById('loginForm').classList.toggle('hidden',!isLogin);
      document.getElementById('registerForm').classList.toggle('hidden',isLogin);
      document.getElementById('tabLogin').className = 'h-10 rounded-full text-sm font-semibold ' + (isLogin?'bg-white text-ink-900 shadow-sm':'text-ink-700');
      document.getElementById('tabRegister').className = 'h-10 rounded-full text-sm font-semibold ' + (!isLogin?'bg-white text-ink-900 shadow-sm':'text-ink-700 hover:bg-white/60');
    }

    // ─────────────────────────────────────────────
    // PAGE INIT — show login wall or donation flow
    // ─────────────────────────────────────────────
    function initPage() {
      seedDefaultAccounts();
      updateNavbar();
      document.getElementById('year').textContent = new Date().getFullYear();

      const user = getCurrentUser();

      if (!user) {
        // Show login wall
        document.getElementById('loginWall').classList.remove('hidden');
        document.getElementById('loginWall').style.display = 'flex';
        document.getElementById('donationFlow').classList.add('hidden');
      } else {
        // Show donation flow
        document.getElementById('loginWall').classList.add('hidden');
        document.getElementById('donationFlow').classList.remove('hidden');
        initDonationFlow(user);
      }
    }

    // ─────────────────────────────────────────────
    // DONATION FLOW INIT
    // ─────────────────────────────────────────────
    function initDonationFlow(user) {
      // Get campaign ID from URL ?id=
      const params = new URLSearchParams(location.search);
      const idParam = parseInt(params.get('id')) || 1;
      const campaign = CAMPAIGNS.find(c => c.id === idParam) || CAMPAIGNS[0];
      STATE.campaignId = campaign.id;

      // Populate campaign card
      const pct = Math.round(campaign.collected / campaign.target * 100);
      document.getElementById('campImg').style.backgroundImage = `url(${campaign.img})`;
      document.getElementById('campCat').textContent = campaign.cat;
      document.getElementById('campTitle').textContent = campaign.title;
      document.getElementById('campFrAvatar').textContent = campaign.fundraiser.charAt(0);
      document.getElementById('campFrName').textContent = campaign.fundraiser;
      setTimeout(() => { document.getElementById('campProgBar').style.width = pct + '%'; }, 100);
      document.getElementById('campCollected').textContent = fmtJt(campaign.collected);
      document.getElementById('campPct').textContent = pct + '%';
      document.getElementById('campTarget').textContent = fmtJt(campaign.target);
      document.getElementById('campDays').textContent = campaign.days;
      document.getElementById('breadcrumbTitle').textContent = campaign.title;
      document.title = 'Donasi — ' + campaign.title + ' | BantuIn';

      // Stepper: step 1 active
      updateStepper(1);
    }

    // ─────────────────────────────────────────────
    // STEPPER
    // ─────────────────────────────────────────────
    function updateStepper(step) {
      STATE.currentStep = step;
      for (let i = 1; i <= 3; i++) {
        const item = document.getElementById('step'+i+'Item');
        item.classList.remove('done','active');
        if (i < step) item.classList.add('done');
        else if (i === step) item.classList.add('active');
      }
      // Step lines color
      const l12 = document.getElementById('line12');
      const l23 = document.getElementById('line23');
      if (l12) l12.className = 'flex-1 h-px mx-2 ' + (step > 1 ? 'bg-brand-700' : 'bg-ink-200');
      if (l23) l23.className = 'flex-1 h-px mx-2 ' + (step > 2 ? 'bg-brand-700' : 'bg-ink-200');
    }

    function goToStep(step) {
      // Validate before advancing
      if (step === 2) {
        const amount = getAmount();
        if (!amount || amount < 5000) {
          showStepError(1, 'Masukkan nominal donasi minimal Rp 5.000.');
          return;
        }
        STATE.amount = amount;
        STATE.message = document.getElementById('donorMessage').value.trim();
        hideStepError(1);
        updateSummary();
      }
      if (step === 3) {
        if (!STATE.method) {
          showStepError(2, 'Pilih metode pembayaran terlebih dahulu.');
          return;
        }
        hideStepError(2);
        fillConfirmation();
      }

      // Show correct panel
      document.querySelectorAll('.step-panel').forEach(p => p.classList.remove('active'));
      const panel = document.getElementById('stepPanel' + step);
      if (panel) { panel.classList.add('active'); }

      updateStepper(step);
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function showStepError(step, msg) {
      const el = document.getElementById('step'+step+'Error');
      if (el) { el.textContent = msg; el.classList.remove('hidden'); }
    }
    function hideStepError(step) {
      const el = document.getElementById('step'+step+'Error');
      if (el) el.classList.add('hidden');
    }

    // ─────────────────────────────────────────────
    // NOMINAL CHIPS
    // ─────────────────────────────────────────────
    function getAmount() {
      const custom = parseInt(document.getElementById('customAmount').value);
      if (custom && custom >= 5000) return custom;
      const selected = document.querySelector('.chip.selected');
      if (selected) return parseInt(selected.dataset.val);
      return 0;
    }

    document.querySelectorAll('.chip').forEach(chip => {
      chip.addEventListener('click', function() {
        document.querySelectorAll('.chip').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
        document.getElementById('customAmount').value = '';
        updateSummary();
        hideStepError(1);
      });
    });

    document.getElementById('customAmount').addEventListener('input', function() {
      document.querySelectorAll('.chip').forEach(c => c.classList.remove('selected'));
      updateSummary();
    });

    // ─────────────────────────────────────────────
    // PAYMENT METHOD
    // ─────────────────────────────────────────────
    function selectPayment(el, method) {
      document.querySelectorAll('.pay-card').forEach(c => {
        c.classList.remove('selected');
        c.querySelector('.pay-dot').style.backgroundColor = '';
        c.querySelector('.pay-dot').style.borderColor = '';
      });
      el.classList.add('selected');
      STATE.method = method;
      updateSummary();
      hideStepError(2);
    }

    // ─────────────────────────────────────────────
    // ANON TOGGLE
    // ─────────────────────────────────────────────
    function toggleAnon() {
      STATE.anon = !STATE.anon;
      const btn = document.getElementById('anonToggle');
      const knob = document.getElementById('anonKnob');
      if (STATE.anon) {
        btn.style.backgroundColor = '#be124c';
        btn.setAttribute('aria-checked','true');
        knob.style.transform = 'translateX(20px)';
      } else {
        btn.style.backgroundColor = '';
        btn.setAttribute('aria-checked','false');
        knob.style.transform = 'translateX(0)';
      }
    }

    // ─────────────────────────────────────────────
    // TERMS TOGGLE
    // ─────────────────────────────────────────────
    function toggleTerms() {
      STATE.termsAccepted = !STATE.termsAccepted;
      const btn = document.getElementById('termsToggle');
      const chk = document.getElementById('termsCheck');
      if (STATE.termsAccepted) {
        btn.style.backgroundColor = '#be124c';
        btn.style.borderColor = '#be124c';
        btn.setAttribute('aria-checked','true');
        chk.classList.remove('hidden');
      } else {
        btn.style.backgroundColor = '';
        btn.style.borderColor = '';
        btn.setAttribute('aria-checked','false');
        chk.classList.add('hidden');
      }
    }

    // ─────────────────────────────────────────────
    // SUMMARY BOX UPDATE
    // ─────────────────────────────────────────────
    function updateSummary() {
      const amount = getAmount();
      const amtText = amount >= 5000 ? fmt(amount) : '—';
      const methodText = STATE.method ? PAY_LABELS[STATE.method] : '—';
      const s = (id, v) => { const e = document.getElementById(id); if(e) e.textContent = v; };
      s('sum-amount', amtText);
      s('sum-method', methodText);
      s('sum-total', amount >= 5000 ? fmt(amount) : '—');
    }

    // ─────────────────────────────────────────────
    // FILL CONFIRMATION
    // ─────────────────────────────────────────────
    function fillConfirmation() {
      const user = getCurrentUser();
      const campaign = CAMPAIGNS.find(c => c.id === STATE.campaignId);
      const s = (id, v) => { const e = document.getElementById(id); if(e) e.textContent = v; };

      s('conf-campaign', campaign ? campaign.title : '—');
      s('conf-name', STATE.anon ? 'Anonim' : (user ? user.name : '—'));
      s('conf-method', PAY_LABELS[STATE.method] || '—');
      s('conf-amount', fmt(STATE.amount));
      s('conf-total', fmt(STATE.amount));

      const msgWrap = document.getElementById('conf-message-wrap');
      const msgText = document.getElementById('conf-message-text');
      if (STATE.message && msgWrap && msgText) {
        msgWrap.classList.remove('hidden');
        msgText.textContent = '"' + STATE.message + '"';
      } else if (msgWrap) {
        msgWrap.classList.add('hidden');
      }
    }

    // ─────────────────────────────────────────────
    // SUBMIT DONATION
    // ─────────────────────────────────────────────
    async function submitDonation() {
      if (!STATE.termsAccepted) {
        showStepError(3, 'Centang persetujuan syarat & ketentuan untuk melanjutkan.');
        return;
      }
      hideStepError(3);

      const btn = document.getElementById('btnSubmit');
      btn.disabled = true;
      btn.innerHTML = `<svg class="spinner" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Memproses...`;

      const payload = {
        campaign_id: STATE.campaignId,
        amount: STATE.amount,
        payment_method: STATE.method,
        message: STATE.message,
        is_anonymous: STATE.anon ? 1 : 0
      };

      try {
        const response = await fetch('/donasi', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(payload)
        });
        const result = await response.json();
        if (result.ok) {
          const txId = 'BTI' + Date.now().toString().slice(-8);
          const now = new Date();
          const dateStr = now.toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit' });

          // Fill receipt
          const s = (id, v) => { const e=document.getElementById(id); if(e) e.textContent=v; };
          s('rcpt-id', txId);
          s('rcpt-amount', fmt(STATE.amount));
          s('rcpt-method', PAY_LABELS[STATE.method]);
          s('rcpt-date', dateStr);

          document.querySelectorAll('.step-panel').forEach(p => p.classList.remove('active'));
          document.getElementById('stepPanel4').classList.add('active');

          for (let i=1;i<=3;i++) {
            const item = document.getElementById('step'+i+'Item');
            item.classList.remove('active');
            item.classList.add('done');
          }

          window.scrollTo({ top:0, behavior:'smooth' });
        } else {
          showStepError(3, result.msg || 'Gagal memproses donasi.');
          btn.disabled = false;
          btn.innerHTML = 'Bayar Sekarang';
        }
      } catch (e) {
        showStepError(3, 'Terjadi kesalahan koneksi.');
        btn.disabled = false;
        btn.innerHTML = 'Bayar Sekarang';
      }
    }

    // ─────────────────────────────────────────────
    // SHARE
    // ─────────────────────────────────────────────
    function shareWA() {
      const campaign = CAMPAIGNS.find(c => c.id === STATE.campaignId);
      const msg = encodeURIComponent('Saya baru saja berdonasi untuk kampanye "' + (campaign?.title||'BantuIn') + '" di BantuIn. Yuk ikut membantu! ' + location.origin + '/kampanye?donate=' + STATE.campaignId);
      window.open('https://wa.me/?text=' + msg, '_blank');
    }
    function shareTW() {
      const campaign = CAMPAIGNS.find(c => c.id === STATE.campaignId);
      const msg = encodeURIComponent('Saya baru donasi untuk "' + (campaign?.title||'BantuIn') + '" @BantuIn_id #BantuIn #Donasi');
      window.open('https://twitter.com/intent/tweet?text=' + msg, '_blank');
    }
    function copyLink() {
      const url = location.origin + '/kampanye?id=' + STATE.campaignId;
      navigator.clipboard.writeText(url).then(() => {
        const btn = document.getElementById('btnCopy');
        if (btn) { btn.textContent = '✓ Tersalin!'; setTimeout(() => { btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M8 5H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1M8 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M8 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m0 0h2a2 2 0 0 1 2 2v3" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg> Salin Link'; }, 2000); }
      });
    }

    // ─────────────────────────────────────────────
    // AUTH MODAL EVENTS
    // ─────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function() {
      initPage();

      // Close modal backdrop
      document.querySelectorAll('[data-close-auth]').forEach(el => el.addEventListener('click', closeAuthModal));
      document.getElementById('tabLogin').addEventListener('click', () => setTab('login'));
      document.getElementById('tabRegister').addEventListener('click', () => setTab('register'));

      // data-open-auth buttons (login wall + navbar)
      document.querySelectorAll('[data-open-auth]').forEach(el => {
        el.addEventListener('click', function() {
          const tab = this.dataset.tab || 'login';
          openAuthModal(tab);
        });
      });

      // Login submit
      document.getElementById('loginForm').addEventListener('submit', async function(e) {
        e.preventDefault(); clearAuthMsg();
        const res = await loginFn(
          document.getElementById('loginEmail').value.trim(),
          document.getElementById('loginPassword').value
        );
        if (!res.ok) { showErr(res.msg); return; }
        showOk('Berhasil masuk! Memuat halaman donasi...');
        setTimeout(() => {
          closeAuthModal();
          updateNavbar();
          location.reload();
        }, 800);
      });

      // Register submit
      document.getElementById('registerForm').addEventListener('submit', async function(e) {
        e.preventDefault(); clearAuthMsg();
        const pw = document.getElementById('regPassword').value;
        if (pw !== document.getElementById('regConfirm').value) { showErr('Password tidak cocok.'); return; }
        const res = await registerFn(
          document.getElementById('regName').value.trim(),
          document.getElementById('regEmail').value.trim(),
          pw,
          document.getElementById('regRole').value
        );
        if (!res.ok) { showErr(res.msg); return; }
        showOk('Akun dibuat! Memuat halaman donasi...');
        setTimeout(() => {
          closeAuthModal();
          updateNavbar();
          location.reload();
        }, 800);
      });

      // User menu toggle
      const userMenuButton = document.getElementById('userMenuButton');
      const userMenu = document.getElementById('userMenu');
      if (userMenuButton && userMenu) {
        userMenuButton.addEventListener('click', e => { e.stopPropagation(); userMenu.classList.toggle('hidden'); });
        document.addEventListener('click', () => userMenu.classList.add('hidden'));
      }

      // Escape key
      document.addEventListener('keydown', e => { if(e.key==='Escape') closeAuthModal(); });
    });
    </script>
  </body>
</html>
