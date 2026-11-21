<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sistem Informasi Monitoring Stunting</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none !important;}</style>
</head>
<body class="min-h-screen bg-slate-100 font-sans antialiased">
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12">

        <!-- Panel Kiri: Branding & Informasi Sistem -->
        <div class="lg:col-span-7 relative overflow-hidden bg-gradient-to-br from-blue-700 via-blue-600 to-cyan-500 text-white flex flex-col justify-between p-8 sm:p-12 lg:p-16">
            <!-- Dekorasi Lingkaran Halus -->
            <div class="pointer-events-none absolute -top-24 -left-24 h-80 w-80 rounded-full bg-white/10 blur-2xl"></div>
            <div class="pointer-events-none absolute -bottom-28 -right-20 h-96 w-96 rounded-full bg-cyan-300/20 blur-3xl"></div>

            <!-- Bagian Atas & Tengah -->
            <div class="relative z-10 my-auto max-w-xl">
                <!-- Badge Sistem -->
                <div class="inline-flex items-center gap-3 mb-8">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/15 backdrop-blur-md border border-white/25 shadow-lg">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-[11px] font-bold uppercase tracking-widest text-blue-100">Sistem Informasi</div>
                        <div class="text-xl sm:text-2xl font-bold tracking-tight text-white">Monitoring Stunting</div>
                    </div>
                </div>

                <!-- Headline Utama -->
                <h1 class="text-3xl sm:text-4xl lg:text-[42px] font-extrabold leading-tight tracking-tight mb-5">
                    Data Terpadu untuk Desa yang Lebih Sehat.
                </h1>

                <!-- Deskripsi -->
                <p class="text-sm sm:text-base text-blue-50/95 leading-relaxed mb-8">
                    Sistem informasi untuk membantu pengelolaan, pemantauan, dan pelaporan data stunting di wilayah Kecamatan Singaparna secara lebih terstruktur dan terintegrasi.
                </p>

                <!-- 2 Kartu Fitur -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="rounded-2xl bg-white/12 backdrop-blur-md border border-white/20 p-4 shadow-sm">
                        <div class="mb-2.5 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white/15 text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                            </svg>
                        </div>
                        <div class="font-semibold text-sm text-white">Monitoring Data</div>
                        <p class="text-xs text-blue-100/90 mt-1 leading-relaxed">
                            Pantau perkembangan data secara terstruktur.
                        </p>
                    </div>

                    <div class="rounded-2xl bg-white/12 backdrop-blur-md border border-white/20 p-4 shadow-sm">
                        <div class="mb-2.5 inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white/15 text-white">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                            </svg>
                        </div>
                        <div class="font-semibold text-sm text-white">Data Terintegrasi</div>
                        <p class="text-xs text-blue-100/90 mt-1 leading-relaxed">
                            Kelola data desa dalam satu sistem.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer Kiri -->
            <div class="relative z-10 pt-10 text-xs text-blue-100/85">
                © 2026 Sistem Informasi Monitoring Stunting Singaparna
            </div>
        </div>

        <!-- Panel Kanan: Form Login -->
        <div class="lg:col-span-5 flex flex-col justify-center items-center bg-gradient-to-b from-slate-50 to-slate-100 p-6 sm:p-10">
            <div class="w-full max-w-md bg-white rounded-3xl shadow-xl shadow-slate-200/70 border border-slate-100 p-7 sm:p-9"
                 x-data="{ showPassword: false }">

                <div class="mb-7">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-blue-600 mb-1">
                        Selamat Datang
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-slate-800 tracking-tight">
                        Login ke Sistem
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">
                        Silakan masuk menggunakan akun petugas Anda.
                    </p>
                </div>

                <!-- Status Session -->
                @if (session('status'))
                    <div class="mb-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-700">
                        {{ session('status') }}
                    </div>
                @endif

                <!-- Error Validasi -->
                @if ($errors->any())
                    <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Email
                        </label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </span>
                            <input id="email"
                                   type="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   autofocus
                                   autocomplete="username"
                                   placeholder="petugas@singaparna.go.id"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50/70 pl-10 pr-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/15 transition">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Password
                        </label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </span>
                            <input id="password"
                                   :type="showPassword ? 'text' : 'password'"
                                   name="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="••••••••••••"
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50/70 pl-10 pr-11 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:bg-white focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-500/15 transition">
                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-slate-600 focus:outline-none"
                                    aria-label="Tampilkan atau sembunyikan password">
                                <svg x-show="!showPassword" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" x-cloak class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Ingat Saya -->
                    <div class="flex items-center justify-between">
                        <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                            <input id="remember_me"
                                   type="checkbox"
                                   name="remember"
                                   class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-sm text-slate-600">Ingat saya</span>
                        </label>
                    </div>

                    <!-- Tombol Masuk -->
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold text-sm py-3 px-4 shadow-md shadow-blue-600/25 transition duration-150">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span>MASUK KE DASHBOARD</span>
                    </button>
                </form>

                <!-- Pemisah & Daftar -->
                @if (Route::has('register'))
                    <div class="relative my-6">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-200"></div>
                        </div>
                        <div class="relative flex justify-center text-xs">
                            <span class="bg-white px-3 text-slate-400">atau</span>
                        </div>
                    </div>

                    <div class="text-center text-sm text-slate-500">
                        Belum memiliki akun?
                        <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                            Daftar sebagai Petugas
                        </a>
                    </div>
                @endif
            </div>

            <div class="mt-5 inline-flex items-center gap-1.5 text-xs text-slate-400">
                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <span>Akses aman untuk pengguna terdaftar</span>
            </div>
        </div>

    </div>
</body>
</html>