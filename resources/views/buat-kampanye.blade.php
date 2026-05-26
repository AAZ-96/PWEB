<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Buat Kampanye — BantuIn</title>
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
      @keyframes fadeUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
      .fade-up { animation: fadeUp .5s ease forwards; }
      .step-dot { transition: background .25s, transform .25s; }
      .step-dot.active { background: #be124c; transform: scale(1.15); }
      .step-dot.done { background: #be124c; }
      .step-connector { transition: background .4s; }
      .step-connector.done { background: #be124c; }

      /* Step panels */
      .step-panel { display:none; }
      .step-panel.active { display:block; }

      /* Image drop zone */
      #dropZone { border:2px dashed #e2e8f0; transition: border-color .2s, background .2s; }
      #dropZone.dragover { border-color:#be124c; background:#fff1f4; }

      /* Input focus ring */
      .field { display:block; width:100%; border-radius:1rem; border:1px solid #e2e8f0; padding:.65rem 1rem; font-size:.875rem; color:#0f172a; background:#fff; outline:none; transition:border-color .2s, box-shadow .2s; }
      .field:focus { border-color:#be124c; box-shadow: 0 0 0 3px rgba(190,18,76,.12); }
      textarea.field { resize:vertical; min-height:110px; }
      select.field { appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none'%3E%3Cpath d='M6 9l6 6 6-6' stroke='%2364748b' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right .75rem center; padding-right:2.5rem; }

      /* Preview card */
      #previewCard { transition: opacity .3s; }

      /* Toast */
      #toast { transition: opacity .3s, transform .3s; }
      #toast.hide { opacity:0; transform:translateY(-12px); pointer-events:none; }

      /* Progress bar in step indicator */
      .prog-line { height:3px; background:#e2e8f0; flex:1; border-radius:99px; overflow:hidden; }
      .prog-line-fill { height:100%; background:#be124c; border-radius:99px; transition:width .4s ease; }
    </style>
  </head>
  <body class="bg-ink-50 text-ink-900 antialiased min-h-screen">
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

          <div class="flex items-center gap-2">
            <a href="/fundraiser" class="hidden sm:inline-flex h-10 items-center justify-center rounded-full px-5 text-sm font-medium text-ink-700 hover:bg-ink-50 gap-2">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              Dashboard
            </a>
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
                <a href="/fundraiser" class="flex items-center gap-3 px-4 py-3 text-sm text-ink-700 hover:bg-ink-50">
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
          </div>
        </div>
      </div>
    </header>

    <!-- ═══ TOAST ═══ -->
    <div id="toast" class="hide fixed top-20 left-1/2 -translate-x-1/2 z-50 flex items-center gap-3 rounded-2xl bg-ink-900 px-5 py-3 shadow-soft text-sm text-white pointer-events-none min-w-[220px] max-w-sm">
      <span id="toastIcon" class="text-lg">✓</span>
      <span id="toastMsg"></span>
    </div>

    <!-- ═══ MAIN ═══ -->
    <main class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8">

      <!-- Page header -->
      <div class="fade-up mb-8">
        <div class="flex items-center gap-3 mb-1">
          <span class="inline-flex rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700 uppercase tracking-widest">Fundraiser</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight">Buat Kampanye Baru</h1>
        <p class="mt-2 text-sm text-ink-600">Isi detail kampanye Anda agar bisa segera menerima donasi dari para donatur.</p>
      </div>

      <!-- Step Indicator -->
      <div class="fade-up mb-8 bg-white rounded-3xl border border-ink-100 p-5 shadow-soft" style="animation-delay:.07s">
        <div class="flex items-center gap-0">
          <!-- Step 1 -->
          <div class="flex flex-col items-center gap-1.5 flex-shrink-0">
            <div id="dot1" class="step-dot active h-8 w-8 rounded-full bg-brand-700 flex items-center justify-center text-white text-xs font-bold">1</div>
            <span class="text-xs font-medium text-ink-700 whitespace-nowrap hidden sm:block">Info Dasar</span>
          </div>
          <div class="prog-line mx-2 sm:mx-3"><div id="line1" class="prog-line-fill" style="width:0%"></div></div>
          <!-- Step 2 -->
          <div class="flex flex-col items-center gap-1.5 flex-shrink-0">
            <div id="dot2" class="step-dot h-8 w-8 rounded-full bg-ink-200 flex items-center justify-center text-ink-600 text-xs font-bold">2</div>
            <span class="text-xs font-medium text-ink-500 whitespace-nowrap hidden sm:block">Detail & Target</span>
          </div>
          <div class="prog-line mx-2 sm:mx-3"><div id="line2" class="prog-line-fill" style="width:0%"></div></div>
          <!-- Step 3 -->
          <div class="flex flex-col items-center gap-1.5 flex-shrink-0">
            <div id="dot3" class="step-dot h-8 w-8 rounded-full bg-ink-200 flex items-center justify-center text-ink-600 text-xs font-bold">3</div>
            <span class="text-xs font-medium text-ink-500 whitespace-nowrap hidden sm:block">Media</span>
          </div>
          <div class="prog-line mx-2 sm:mx-3"><div id="line3" class="prog-line-fill" style="width:0%"></div></div>
          <!-- Step 4 -->
          <div class="flex flex-col items-center gap-1.5 flex-shrink-0">
            <div id="dot4" class="step-dot h-8 w-8 rounded-full bg-ink-200 flex items-center justify-center text-ink-600 text-xs font-bold">4</div>
            <span class="text-xs font-medium text-ink-500 whitespace-nowrap hidden sm:block">Review</span>
          </div>
        </div>
      </div>

      <div class="grid gap-6 lg:grid-cols-[1fr_340px]">

        <!-- ═══ FORM AREA ═══ -->
        <div class="fade-up space-y-0" style="animation-delay:.12s">

          <!-- ─ STEP 1: Info Dasar ─ -->
          <div id="panel1" class="step-panel active bg-white rounded-3xl border border-ink-100 p-6 shadow-soft">
            <div class="flex items-center gap-3 mb-6">
              <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-700">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 4v16M4 12h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              </span>
              <div>
                <p class="text-xs text-ink-500 uppercase tracking-widest">Langkah 1 dari 4</p>
                <h2 class="text-lg font-semibold">Informasi Dasar</h2>
              </div>
            </div>

            <div class="space-y-5">
              <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Judul Kampanye <span class="text-brand-600">*</span></label>
                <input id="f_title" type="text" class="field" placeholder="cth: Bantuan Korban Gempa Cianjur 2025" maxlength="100" oninput="updatePreview()"/>
                <div class="flex justify-between mt-1">
                  <span class="text-xs text-red-500 hidden" id="err_title">Judul wajib diisi (min. 10 karakter)</span>
                  <span class="text-xs text-ink-400 ml-auto"><span id="cnt_title">0</span>/100</span>
                </div>
              </div>

              <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Kategori <span class="text-brand-600">*</span></label>
                <select id="f_cat" class="field" onchange="updatePreview()">
                  <option value="">— Pilih Kategori —</option>
                  <option value="bencana">🔴 Bencana Alam</option>
                  <option value="pendidikan">📚 Pendidikan</option>
                  <option value="kesehatan">💊 Kesehatan</option>
                  <option value="air bersih">💧 Air Bersih</option>
                  <option value="lingkungan">🌿 Lingkungan</option>
                  <option value="komunitas">🤝 Komunitas</option>
                </select>
                <span class="text-xs text-red-500 hidden mt-1 block" id="err_cat">Kategori wajib dipilih</span>
              </div>

              <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Lokasi Bencana / Wilayah <span class="text-brand-600">*</span></label>
                <input id="f_location" type="text" class="field" placeholder="cth: Cianjur, Jawa Barat" maxlength="80" oninput="updatePreview()"/>
                <span class="text-xs text-red-500 hidden mt-1 block" id="err_location">Lokasi wajib diisi</span>
              </div>

              <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Penyelenggara / Organisasi <span class="text-brand-600">*</span></label>
                <input id="f_org" type="text" class="field" placeholder="Nama lembaga atau organisasi Anda" maxlength="80"/>
                <p class="text-xs text-ink-400 mt-1">Akan tampil sebagai nama fundraiser pada halaman kampanye.</p>
                <span class="text-xs text-red-500 hidden mt-1 block" id="err_org">Nama penyelenggara wajib diisi</span>
              </div>
            </div>

            <div class="mt-8 flex justify-end">
              <button onclick="goStep(2)" class="inline-flex h-11 items-center justify-center rounded-full bg-brand-700 px-7 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 gap-2">
                Lanjut <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              </button>
            </div>
          </div>

          <!-- ─ STEP 2: Detail & Target ─ -->
          <div id="panel2" class="step-panel bg-white rounded-3xl border border-ink-100 p-6 shadow-soft">
            <div class="flex items-center gap-3 mb-6">
              <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-700">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M9 11l3 3 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M20 12a8 8 0 1 1-8-8" stroke="currentColor" stroke-width="2"/></svg>
              </span>
              <div>
                <p class="text-xs text-ink-500 uppercase tracking-widest">Langkah 2 dari 4</p>
                <h2 class="text-lg font-semibold">Detail & Target Dana</h2>
              </div>
            </div>

            <div class="space-y-5">
              <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Deskripsi Kampanye <span class="text-brand-600">*</span></label>
                <textarea id="f_desc" class="field" placeholder="Ceritakan kondisi nyata di lapangan, siapa yang terdampak, dan bagaimana donasi akan digunakan..." maxlength="1000" oninput="updatePreview()"></textarea>
                <div class="flex justify-between mt-1">
                  <span class="text-xs text-red-500 hidden" id="err_desc">Deskripsi wajib diisi (min. 30 karakter)</span>
                  <span class="text-xs text-ink-400 ml-auto"><span id="cnt_desc">0</span>/1000</span>
                </div>
              </div>

              <div class="grid sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-ink-700 mb-1.5">Target Dana (Rp) <span class="text-brand-600">*</span></label>
                  <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm text-ink-500 font-medium pointer-events-none">Rp</span>
                    <input id="f_target" type="number" class="field pl-10" placeholder="50000000" min="1000000" max="10000000000" oninput="updatePreview()"/>
                  </div>
                  <p class="text-xs text-ink-400 mt-1">Min. Rp 1.000.000</p>
                  <span class="text-xs text-red-500 hidden mt-1 block" id="err_target">Target dana wajib diisi (min. Rp 1.000.000)</span>
                </div>

                <div>
                  <label class="block text-sm font-medium text-ink-700 mb-1.5">Durasi Kampanye <span class="text-brand-600">*</span></label>
                  <select id="f_days" class="field" onchange="updatePreview()">
                    <option value="">— Pilih Durasi —</option>
                    <option value="7">7 hari (Darurat)</option>
                    <option value="14">14 hari</option>
                    <option value="30">30 hari</option>
                    <option value="45">45 hari</option>
                    <option value="60">60 hari</option>
                    <option value="90">90 hari</option>
                  </select>
                  <span class="text-xs text-red-500 hidden mt-1 block" id="err_days">Durasi wajib dipilih</span>
                </div>
              </div>

              <!-- Rencana Penggunaan Dana -->
              <div>
                <label class="block text-sm font-medium text-ink-700 mb-2">Rencana Penggunaan Dana</label>
                <div id="budgetItems" class="space-y-2"></div>
                <button type="button" onclick="addBudgetItem()" class="mt-3 inline-flex items-center gap-2 text-sm font-medium text-brand-700 hover:text-brand-800">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                  Tambah Item
                </button>
              </div>

              <!-- Tanggal mulai -->
              <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Tanggal Mulai <span class="text-brand-600">*</span></label>
                <input id="f_startdate" type="date" class="field"/>
                <span class="text-xs text-red-500 hidden mt-1 block" id="err_startdate">Tanggal mulai wajib diisi</span>
              </div>
            </div>

            <div class="mt-8 flex justify-between">
              <button onclick="goStep(1)" class="inline-flex h-11 items-center justify-center rounded-full border border-ink-200 px-6 text-sm font-semibold text-ink-700 hover:bg-ink-50 gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Kembali
              </button>
              <button onclick="goStep(3)" class="inline-flex h-11 items-center justify-center rounded-full bg-brand-700 px-7 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 gap-2">
                Lanjut <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              </button>
            </div>
          </div>

          <!-- ─ STEP 3: Media ─ -->
          <div id="panel3" class="step-panel bg-white rounded-3xl border border-ink-100 p-6 shadow-soft">
            <div class="flex items-center gap-3 mb-6">
              <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-700">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="3" stroke="currentColor" stroke-width="2"/><path d="M3 15l5-5 4 4 3-3 6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              </span>
              <div>
                <p class="text-xs text-ink-500 uppercase tracking-widest">Langkah 3 dari 4</p>
                <h2 class="text-lg font-semibold">Media Kampanye</h2>
              </div>
            </div>

            <div class="space-y-6">
              <!-- Image upload / URL -->
              <div>
                <label class="block text-sm font-medium text-ink-700 mb-2">Gambar Utama Kampanye</label>
                <div id="dropZone" class="rounded-2xl p-8 text-center cursor-pointer" onclick="document.getElementById('imgFileInput').click()" ondragover="event.preventDefault();this.classList.add('dragover')" ondragleave="this.classList.remove('dragover')" ondrop="handleDrop(event)">
                  <div id="dropContent">
                    <svg class="mx-auto text-ink-400" width="40" height="40" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="3" stroke="currentColor" stroke-width="1.5"/><path d="M3 15l5-5 4 4 3-3 6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                    <p class="mt-3 text-sm font-medium text-ink-700">Drag & drop gambar di sini</p>
                    <p class="text-xs text-ink-500 mt-1">atau klik untuk memilih file · JPG, PNG, WebP · maks 5 MB</p>
                  </div>
                  <img id="imgPreviewInZone" class="hidden mx-auto max-h-52 rounded-xl object-cover mt-2" alt="Preview"/>
                </div>
                <input id="imgFileInput" type="file" accept="image/*" class="hidden" onchange="handleFileSelect(event)"/>
                <p class="text-xs text-ink-500 mt-2">— atau gunakan URL gambar —</p>
                <input id="f_imgurl" type="url" class="field mt-2" placeholder="https://images.unsplash.com/..." oninput="handleImgUrl()"/>
                <span class="text-xs text-red-500 hidden mt-1 block" id="err_img">URL gambar tidak valid</span>
              </div>

              <!-- Kontak PIC -->
              <div class="grid sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-ink-700 mb-1.5">Nama Penanggungjawab</label>
                  <input id="f_pic_name" type="text" class="field" placeholder="Nama lengkap"/>
                </div>
                <div>
                  <label class="block text-sm font-medium text-ink-700 mb-1.5">No. WhatsApp / Telepon</label>
                  <input id="f_pic_phone" type="tel" class="field" placeholder="+62 812-xxxx-xxxx"/>
                </div>
              </div>

              <!-- Media sosial (opsional) -->
              <div>
                <label class="block text-sm font-medium text-ink-700 mb-1.5">Tautan Media Sosial (opsional)</label>
                <input id="f_social" type="url" class="field" placeholder="https://instagram.com/..."/>
              </div>

              <!-- Syarat & ketentuan -->
              <label class="flex items-start gap-3 cursor-pointer">
                <input id="f_agree" type="checkbox" class="mt-0.5 h-4 w-4 rounded border-ink-300 accent-brand-700"/>
                <span class="text-sm text-ink-600">Saya menyatakan bahwa informasi yang diisi adalah benar dan kampanye ini sesuai dengan <a href="#" class="text-brand-700 font-medium hover:underline">Syarat & Ketentuan</a> BantuIn.</span>
              </label>
              <span class="text-xs text-red-500 hidden block" id="err_agree">Anda harus menyetujui syarat & ketentuan</span>
            </div>

            <div class="mt-8 flex justify-between">
              <button onclick="goStep(2)" class="inline-flex h-11 items-center justify-center rounded-full border border-ink-200 px-6 text-sm font-semibold text-ink-700 hover:bg-ink-50 gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Kembali
              </button>
              <button onclick="goStep(4)" class="inline-flex h-11 items-center justify-center rounded-full bg-brand-700 px-7 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 gap-2">
                Review <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              </button>
            </div>
          </div>

          <!-- ─ STEP 4: Review & Submit ─ -->
          <div id="panel4" class="step-panel bg-white rounded-3xl border border-ink-100 p-6 shadow-soft">
            <div class="flex items-center gap-3 mb-6">
              <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-700">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              </span>
              <div>
                <p class="text-xs text-ink-500 uppercase tracking-widest">Langkah 4 dari 4</p>
                <h2 class="text-lg font-semibold">Review & Publikasi</h2>
              </div>
            </div>

            <!-- Summary table -->
            <div id="reviewSummary" class="space-y-3 mb-6"></div>

            <div class="rounded-2xl bg-amber-50 border border-amber-200 p-4 flex gap-3 mb-6">
              <svg class="flex-shrink-0 text-amber-500 mt-0.5" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 9v4m0 4h.01M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              <p class="text-sm text-amber-800">Kampanye akan langsung aktif setelah dipublikasikan. Pastikan semua informasi sudah benar.</p>
            </div>

            <div class="mt-8 flex justify-between">
              <button onclick="goStep(3)" class="inline-flex h-11 items-center justify-center rounded-full border border-ink-200 px-6 text-sm font-semibold text-ink-700 hover:bg-ink-50 gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M15 18l-6-6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Kembali
              </button>
              <button id="btnPublish" onclick="publishCampaign()" class="inline-flex h-11 items-center justify-center rounded-full bg-brand-700 px-8 text-sm font-semibold text-white shadow-sm hover:bg-brand-800 gap-2">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M22 2L11 13" stroke="currentColor" stroke-width="2" stroke-linecap="round"/><path d="M22 2L15 22l-4-9-9-4 20-7Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
                Publikasikan Kampanye
              </button>
            </div>
          </div>

        </div>

        <!-- ═══ PREVIEW SIDEBAR ═══ -->
        <div class="fade-up space-y-4" style="animation-delay:.18s">
          <div class="bg-white rounded-3xl border border-ink-100 p-5 shadow-soft sticky top-24">
            <p class="text-xs font-semibold text-ink-500 uppercase tracking-widest mb-4">Preview Kampanye</p>
            <div id="previewCard">
              <!-- Image -->
              <div class="relative h-40 rounded-2xl bg-ink-100 overflow-hidden mb-4 flex items-center justify-center">
                <img id="prev_img" class="h-full w-full object-cover hidden" alt="Preview"/>
                <div id="prev_img_placeholder" class="flex flex-col items-center gap-2 text-ink-300">
                  <svg width="32" height="32" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="3" stroke="currentColor" stroke-width="1.5"/><path d="M3 15l5-5 4 4 3-3 6 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
                  <span class="text-xs">Belum ada gambar</span>
                </div>
                <span id="prev_badge" class="absolute left-3 top-3 hidden text-xs font-semibold rounded-full px-2.5 py-1 bg-ink-50 text-ink-700"></span>
              </div>
              <!-- Info -->
              <h3 id="prev_title" class="text-sm font-semibold leading-snug text-ink-400 italic">Judul kampanye akan muncul di sini...</h3>
              <p id="prev_desc" class="mt-1.5 text-xs text-ink-400 line-clamp-3 italic">Deskripsi kampanye...</p>
              <!-- Progress (dummy 0%) -->
              <div class="mt-4">
                <div class="h-1.5 rounded-full bg-ink-100"><div id="prev_progress" class="h-1.5 rounded-full bg-brand-700 progress-bar-fill" style="width:0%"></div></div>
                <div class="mt-2 flex items-center justify-between text-xs text-ink-600">
                  <span class="font-semibold text-ink-900">Rp 0</span>
                  <span class="text-brand-700 font-semibold">0%</span>
                </div>
                <div class="mt-0.5 text-xs text-ink-400">dari <span id="prev_target">—</span> · <span id="prev_days">—</span></div>
              </div>
              <!-- Fundraiser -->
              <div class="mt-4 flex items-center gap-2 pt-3 border-t border-ink-100">
                <div id="prev_org_avatar" class="h-7 w-7 rounded-full bg-brand-50 flex items-center justify-center text-brand-800 text-xs font-bold flex-shrink-0">?</div>
                <span id="prev_org" class="text-xs text-ink-500 truncate italic">Nama organisasi...</span>
              </div>
            </div>
          </div>

          <!-- Tips -->
          <div class="bg-brand-50 rounded-3xl border border-brand-100 p-5">
            <p class="text-xs font-semibold text-brand-700 uppercase tracking-widest mb-3">Tips Kampanye</p>
            <ul class="space-y-2 text-xs text-ink-700">
              <li class="flex gap-2"><span class="text-brand-600 font-bold">✓</span> Judul spesifik dan mudah diingat meningkatkan donasi hingga 3×</li>
              <li class="flex gap-2"><span class="text-brand-600 font-bold">✓</span> Gunakan foto asli dari lapangan, bukan foto stok</li>
              <li class="flex gap-2"><span class="text-brand-600 font-bold">✓</span> Ceritakan dampak nyata: berapa orang terbantu?</li>
              <li class="flex gap-2"><span class="text-brand-600 font-bold">✓</span> Update progress rutin membangun kepercayaan donatur</li>
            </ul>
          </div>
        </div>

      </div>
    </main>

    <!-- ═══ SUCCESS MODAL ═══ -->
    <div id="successModal" class="fixed inset-0 z-50 hidden">
      <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
      <div class="relative flex min-h-screen items-center justify-center p-4">
        <div class="w-full max-w-md rounded-3xl bg-white shadow-soft p-8 text-center">
          <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-green-50 mx-auto">
            <svg class="text-green-500" width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"/></svg>
          </div>
          <h2 class="mt-5 text-2xl font-semibold text-ink-900">Kampanye Berhasil Dipublikasikan! 🎉</h2>
          <p class="mt-3 text-sm text-ink-600">Kampanye Anda kini aktif dan siap menerima donasi dari seluruh Indonesia.</p>
          <div id="successCampaignName" class="mt-4 rounded-2xl bg-ink-50 px-4 py-3 text-sm font-semibold text-ink-900 break-words"></div>
          <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
            <a href="/kampanye" class="inline-flex h-11 items-center justify-center rounded-full bg-brand-700 px-7 text-sm font-semibold text-white hover:bg-brand-800">
              Lihat Kampanye
            </a>
            <a href="/fundraiser" class="inline-flex h-11 items-center justify-center rounded-full border border-ink-200 px-7 text-sm font-semibold text-ink-700 hover:bg-ink-50">
              Dashboard
            </a>
          </div>
        </div>
      </div>
    </div>

    <script>
      /* ═══════════════════════ UTILS ═══════════════════════ */
      function getUsers()   { return JSON.parse(localStorage.getItem('bantuin_users') || '[]'); }
      function saveUsers(u) { localStorage.setItem('bantuin_users', JSON.stringify(u)); }
      function getCurrentUser() { return JSON.parse(localStorage.getItem('bantuin_current_user') || 'null'); }
      function logout() { localStorage.removeItem('bantuin_current_user'); location.href = '/'; }

      function getCampaigns() { return JSON.parse(localStorage.getItem('bantuin_campaigns') || '[]'); }
      function saveCampaigns(c) { localStorage.setItem('bantuin_campaigns', JSON.stringify(c)); }

      function fmtMoney(n) {
        if (!n || isNaN(n)) return '—';
        const v = Number(n);
        if (v >= 1e9) return 'Rp ' + (v/1e9).toFixed(1).replace('.0','') + 'M';
        if (v >= 1e6) return 'Rp ' + (v/1e6).toFixed(1).replace('.0','') + 'jt';
        return 'Rp ' + v.toLocaleString('id');
      }

      function showToast(msg, icon='✓', dur=3000) {
        const t = document.getElementById('toast');
        document.getElementById('toastMsg').textContent = msg;
        document.getElementById('toastIcon').textContent = icon;
        t.classList.remove('hide');
        clearTimeout(t._timer);
        t._timer = setTimeout(() => t.classList.add('hide'), dur);
      }

      /* ═══════════════════════ AUTH GUARD ═══════════════════════ */
      let currentUser = null;
      document.addEventListener('DOMContentLoaded', function () {
        currentUser = getCurrentUser();
        if (!currentUser) {
          showToast('Anda harus login sebagai fundraiser terlebih dahulu', '⚠', 4000);
          setTimeout(() => location.href = '/', 2000);
          return;
        }
        if (currentUser.role !== 'fundraiser' && currentUser.role !== 'admin') {
          showToast('Halaman ini hanya untuk akun fundraiser', '⚠', 4000);
          setTimeout(() => location.href = '/', 2000);
          return;
        }

        // Show user menu
        document.getElementById('userMenuWrapper').classList.remove('hidden');
        document.getElementById('userInitial').textContent = currentUser.name.charAt(0).toUpperCase();
        document.getElementById('menuUserName').textContent = currentUser.name;
        document.getElementById('menuUserEmail').textContent = currentUser.email;
        document.getElementById('menuUserRole').textContent = currentUser.role === 'admin' ? 'Admin' : 'Fundraiser';

        // Pre-fill org from user name
        document.getElementById('f_org').value = currentUser.name;

        // Set today as min date for start
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('f_startdate').value = today;
        document.getElementById('f_startdate').min = today;

        // User menu toggle
        const btn = document.getElementById('userMenuButton');
        const menu = document.getElementById('userMenu');
        btn.addEventListener('click', e => { e.stopPropagation(); menu.classList.toggle('hidden'); });
        document.addEventListener('click', () => menu.classList.add('hidden'));

        // Init budget items
        addBudgetItem();

        // Title counter
        document.getElementById('f_title').addEventListener('input', function() {
          document.getElementById('cnt_title').textContent = this.value.length;
        });
        document.getElementById('f_desc').addEventListener('input', function() {
          document.getElementById('cnt_desc').textContent = this.value.length;
        });
      });

      /* ═══════════════════════ STEP NAVIGATION ═══════════════════════ */
      let currentStep = 1;

      function goStep(n) {
        if (n > currentStep && !validateStep(currentStep)) return;
        currentStep = n;
        // Hide all panels
        for (let i=1;i<=4;i++) {
          document.getElementById('panel'+i).classList.remove('active');
          const dot = document.getElementById('dot'+i);
          dot.classList.remove('active');
          if (i < n) {
            dot.classList.add('done');
            dot.style.background = '#be124c';
            dot.style.color = '#fff';
          } else {
            if (i !== n) { dot.style.background = '#e2e8f0'; dot.style.color = '#64748b'; dot.classList.remove('done'); }
          }
        }
        // Activate current
        document.getElementById('panel'+n).classList.add('active');
        const activeDot = document.getElementById('dot'+n);
        activeDot.classList.add('active');
        activeDot.style.background = '#be124c';
        activeDot.style.color = '#fff';

        // Progress lines
        for (let i=1;i<=3;i++) {
          document.getElementById('line'+i).style.width = i < n ? '100%' : '0%';
        }

        // Build review if on step 4
        if (n === 4) buildReview();

        // Scroll to top of main
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }

      /* ═══════════════════════ VALIDATION ═══════════════════════ */
      function validateStep(step) {
        let ok = true;

        function err(id, show) {
          const el = document.getElementById(id);
          if (el) show ? el.classList.remove('hidden') : el.classList.add('hidden');
        }

        if (step === 1) {
          const title = document.getElementById('f_title').value.trim();
          const cat   = document.getElementById('f_cat').value;
          const loc   = document.getElementById('f_location').value.trim();
          const org   = document.getElementById('f_org').value.trim();
          if (title.length < 10) { err('err_title', true); ok=false; } else err('err_title', false);
          if (!cat) { err('err_cat', true); ok=false; } else err('err_cat', false);
          if (!loc) { err('err_location', true); ok=false; } else err('err_location', false);
          if (!org) { err('err_org', true); ok=false; } else err('err_org', false);
        }
        if (step === 2) {
          const desc   = document.getElementById('f_desc').value.trim();
          const target = Number(document.getElementById('f_target').value);
          const days   = document.getElementById('f_days').value;
          const sd     = document.getElementById('f_startdate').value;
          if (desc.length < 30) { err('err_desc', true); ok=false; } else err('err_desc', false);
          if (!target || target < 1000000) { err('err_target', true); ok=false; } else err('err_target', false);
          if (!days) { err('err_days', true); ok=false; } else err('err_days', false);
          if (!sd) { err('err_startdate', true); ok=false; } else err('err_startdate', false);
        }
        if (step === 3) {
          const agree = document.getElementById('f_agree').checked;
          if (!agree) { err('err_agree', true); ok=false; } else err('err_agree', false);
        }
        if (!ok) showToast('Mohon lengkapi semua field yang wajib diisi', '⚠');
        return ok;
      }

      /* ═══════════════════════ BUDGET ITEMS ═══════════════════════ */
      let budgetCount = 0;
      function addBudgetItem() {
        budgetCount++;
        const id = 'budget_' + budgetCount;
        const div = document.createElement('div');
        div.id = id;
        div.className = 'flex gap-2 items-center';
        div.innerHTML = `
          <input type="text" class="field flex-1" placeholder="cth: Makanan & minuman darurat" />
          <div class="relative w-32 flex-shrink-0">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-ink-500 pointer-events-none">Rp</span>
            <input type="number" class="field pl-8 text-sm" placeholder="5000000" min="0"/>
          </div>
          <button type="button" onclick="document.getElementById('${id}').remove()" class="flex-shrink-0 h-9 w-9 inline-flex items-center justify-center rounded-xl text-ink-400 hover:text-red-500 hover:bg-red-50">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M18 6L6 18M6 6l12 12" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          </button>`;
        document.getElementById('budgetItems').appendChild(div);
      }

      /* ═══════════════════════ IMAGE HANDLING ═══════════════════════ */
      let selectedImageSrc = '';

      function handleFileSelect(event) {
        const file = event.target.files[0];
        if (!file) return;
        if (file.size > 5 * 1024 * 1024) { showToast('Ukuran file maks. 5 MB', '⚠'); return; }
        const reader = new FileReader();
        reader.onload = function(e) {
          selectedImageSrc = e.target.result;
          showImagePreview(selectedImageSrc);
        };
        reader.readAsDataURL(file);
      }

      function handleDrop(event) {
        event.preventDefault();
        document.getElementById('dropZone').classList.remove('dragover');
        const file = event.dataTransfer.files[0];
        if (!file || !file.type.startsWith('image/')) { showToast('Hanya file gambar yang diterima', '⚠'); return; }
        const reader = new FileReader();
        reader.onload = e => { selectedImageSrc = e.target.result; showImagePreview(selectedImageSrc); };
        reader.readAsDataURL(file);
      }

      function handleImgUrl() {
        const url = document.getElementById('f_imgurl').value.trim();
        if (!url) { selectedImageSrc = ''; showImagePreview(''); return; }
        try {
          new URL(url);
          selectedImageSrc = url;
          showImagePreview(url);
          document.getElementById('err_img').classList.add('hidden');
        } catch { document.getElementById('err_img').classList.remove('hidden'); }
      }

      function showImagePreview(src) {
        const dz = document.getElementById('dropZone');
        const content = document.getElementById('dropContent');
        const img = document.getElementById('imgPreviewInZone');
        const prevImg = document.getElementById('prev_img');
        const prevPlaceholder = document.getElementById('prev_img_placeholder');

        if (src) {
          img.src = src;
          img.classList.remove('hidden');
          content.classList.add('hidden');
          // Sidebar preview
          prevImg.src = src;
          prevImg.classList.remove('hidden');
          prevPlaceholder.classList.add('hidden');
        } else {
          img.classList.add('hidden');
          content.classList.remove('hidden');
          prevImg.classList.add('hidden');
          prevPlaceholder.classList.remove('hidden');
        }
      }

      /* ═══════════════════════ LIVE PREVIEW ═══════════════════════ */
      const catColors = {
        pendidikan:'bg-blue-100 text-blue-700',
        kesehatan:'bg-green-100 text-green-700',
        'air bersih':'bg-cyan-100 text-cyan-700',
        bencana:'bg-red-100 text-red-600',
        lingkungan:'bg-emerald-100 text-emerald-700',
        komunitas:'bg-purple-100 text-purple-700'
      };
      const catEmoji = { bencana:'🔴', pendidikan:'📚', kesehatan:'💊', 'air bersih':'💧', lingkungan:'🌿', komunitas:'🤝' };

      function updatePreview() {
        const title = document.getElementById('f_title').value.trim() || 'Judul kampanye akan muncul di sini...';
        const desc  = document.getElementById('f_desc').value.trim() || 'Deskripsi kampanye...';
        const cat   = document.getElementById('f_cat').value;
        const target= document.getElementById('f_target').value;
        const days  = document.getElementById('f_days').value;
        const org   = document.getElementById('f_org').value.trim() || 'Nama organisasi...';

        document.getElementById('prev_title').textContent = title;
        document.getElementById('prev_title').className = title === 'Judul kampanye akan muncul di sini...' ?
          'text-sm font-semibold leading-snug text-ink-400 italic' :
          'text-sm font-semibold leading-snug text-ink-900';
        document.getElementById('prev_desc').textContent = desc;
        document.getElementById('prev_target').textContent = target ? fmtMoney(target) : '—';
        document.getElementById('prev_days').textContent = days ? days + ' hari' : '—';
        document.getElementById('prev_org').textContent = org;
        document.getElementById('prev_org_avatar').textContent = org.charAt(0).toUpperCase();

        // Category badge
        const badge = document.getElementById('prev_badge');
        if (cat) {
          badge.textContent = (catEmoji[cat] || '') + ' ' + cat;
          badge.className = 'absolute left-3 top-3 text-xs font-semibold rounded-full px-2.5 py-1 ' + (catColors[cat] || 'bg-ink-50 text-ink-700');
          badge.classList.remove('hidden');
        } else {
          badge.classList.add('hidden');
        }
      }

      /* ═══════════════════════ REVIEW SUMMARY ═══════════════════════ */
      function buildReview() {
        const fields = [
          { label:'Judul', val: document.getElementById('f_title').value },
          { label:'Kategori', val: document.getElementById('f_cat').value },
          { label:'Lokasi', val: document.getElementById('f_location').value },
          { label:'Penyelenggara', val: document.getElementById('f_org').value },
          { label:'Target Dana', val: fmtMoney(document.getElementById('f_target').value) },
          { label:'Durasi', val: document.getElementById('f_days').value + ' hari' },
          { label:'Tanggal Mulai', val: document.getElementById('f_startdate').value },
          { label:'Gambar', val: selectedImageSrc ? '✓ Sudah diatur' : '— (tidak ada, akan pakai default)' },
        ];

        const html = fields.map(f => `
          <div class="flex items-start justify-between gap-4 rounded-2xl bg-ink-50 px-4 py-3 text-sm">
            <span class="text-ink-500 font-medium flex-shrink-0 w-32">${f.label}</span>
            <span class="text-ink-900 font-semibold text-right break-all">${f.val || '—'}</span>
          </div>`).join('');

        // Budget
        const budgetItems = [...document.getElementById('budgetItems').children];
        const budgetHtml = budgetItems.map(item => {
          const inputs = item.querySelectorAll('input');
          const nama = inputs[0].value.trim();
          const jml  = inputs[1].value;
          return nama ? `<div class="flex justify-between text-xs py-1"><span>${nama}</span><span class="font-medium">${fmtMoney(jml)}</span></div>` : '';
        }).filter(Boolean).join('');

        document.getElementById('reviewSummary').innerHTML = html +
          (budgetHtml ? `<div class="rounded-2xl bg-ink-50 px-4 py-3 text-sm"><p class="font-medium text-ink-500 mb-2">Rencana Penggunaan Dana</p>${budgetHtml}</div>` : '');
      }

      /* ═══════════════════════ PUBLISH ═══════════════════════ */
      async function publishCampaign() {
        const btn = document.getElementById('btnPublish');
        btn.disabled = true;
        btn.innerHTML = `<svg class="animate-spin" width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="40" stroke-dashoffset="10"/></svg> Mempublikasikan...`;

        // Collect budget
        const budgetItems = [...document.getElementById('budgetItems').children].map(item => {
          const inputs = item.querySelectorAll('input');
          return { label: inputs[0].value.trim(), amount: Number(inputs[1].value) || 0 };
        }).filter(i => i.label);

        const payload = {
          title: document.getElementById('f_title').value.trim(),
          cat: document.getElementById('f_cat').value,
          location: document.getElementById('f_location').value.trim(),
          fundraiser: document.getElementById('f_org').value.trim(),
          desc: document.getElementById('f_desc').value.trim(),
          target: Number(document.getElementById('f_target').value),
          days: Number(document.getElementById('f_days').value),
          startDate: document.getElementById('f_startdate').value,
          img: selectedImageSrc,
          budget: budgetItems,
          pic_name: document.getElementById('f_pic_name').value.trim(),
          pic_phone: document.getElementById('f_pic_phone').value.trim(),
          social: document.getElementById('f_social').value.trim()
        };

        try {
          const response = await fetch('/buat-kampanye', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
          });
          const result = await response.json();
          if (result.ok) {
            document.getElementById('successCampaignName').textContent = payload.title;
            document.getElementById('successModal').classList.remove('hidden');
          } else {
            showToast(result.msg || 'Gagal mempublikasikan kampanye.', '⚠');
            btn.disabled = false;
            btn.innerHTML = 'Mempublikasikan Kampanye';
          }
        } catch (e) {
          showToast('Terjadi kesalahan koneksi.', '⚠');
          btn.disabled = false;
          btn.innerHTML = 'Mempublikasikan Kampanye';
        }
      }
    </script>
  </body>
</html>
