<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk | SILACEK</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="auth-shell">
        <section class="brand-panel" aria-label="SILACEK">
            <div class="brand-copy">
                
                <div class="brand-lockup">
                    <svg class="brand-mark" viewBox="0 0 52 42" fill="none" aria-hidden="true">
                        <path d="M3 5.5c8.6-2 16.3-.8 23 3.7v29c-6.7-4.5-14.4-5.7-23-3.7v-29Z" stroke="currentColor" stroke-width="2.4" stroke-linejoin="round" />
                        <path d="M49 5.5c-8.6-2-16.3-.8-23 3.7v29c6.7-4.5 14.4-5.7 23-3.7v-29Z" stroke="currentColor" stroke-width="2.4" stroke-linejoin="round" />
                        <path d="M26 9v29" stroke="currentColor" stroke-width="2.4" />
                    </svg>
                    
                    <span>SILACEK</span>
                </div>
                <p class="brand-subtitle">Sistem Informasi Layanan Cek Plagiasi Karya Ilmiah</p>
            </div>
            <div class="campus-caption">
                <img src="{{ asset('images/UINSSC(1).png') }}" alt="Logo UIN Siber Syekh Nurjati Cirebon">
                <span>UIN Siber Syekh Nurjati Cirebon</span>
            </div>
        </section>

        <section class="form-panel">
            <div class="form-content">
                <header class="form-heading">
                    <p class="eyebrow">Layanan Perpustakaan</p>
                    <h1>Masuk</h1>
                    <p>Masuk untuk mengakses layanan pengecekan plagiarisme.</p>
                </header>

                @if (session('status'))
                    <div class="notice notice-success" role="status">{{ session('status') }}</div>
                @endif
                @if ($errors->has('login'))
                    <div class="notice notice-error" role="alert">{{ $errors->first('login') }}</div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="login-form">
                    @csrf
                    <div class="field-group">
                        <label for="identifier">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="8" r="3.2" stroke="currentColor" stroke-width="1.8"/><path d="M5 19c.4-3.1 2.8-5 7-5s6.6 1.9 7 5H5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
                            Username
                        </label>
                        <input id="identifier" name="identifier" type="text" value="{{ old('identifier') }}" placeholder="Masukkan Username" autocomplete="username" required autofocus>
                        @error('identifier') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field-group">
                        <label for="password">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="5" y="10" width="14" height="10" rx="2" stroke="currentColor" stroke-width="1.8"/><path d="M8 10V7a4 4 0 1 1 8 0v3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            Password
                        </label>
                        <div class="password-input-wrapper">
                            <input id="password" name="password" type="password" placeholder="Masukkan Password" autocomplete="current-password" required>
                            <button class="password-toggle" type="button" data-password-toggle="password" aria-label="Tampilkan password" aria-pressed="false" title="Tampilkan password">
                                <svg class="eye-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.8"/></svg>
                                <svg class="eye-off-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m3 3 18 18M10.6 6.2A9.7 9.7 0 0 1 12 6c6.1 0 9.5 6 9.5 6a16.7 16.7 0 0 1-3 3.5M6.2 6.7C3.8 8.2 2.5 12 2.5 12s3.4 6 9.5 6c1 0 1.9-.2 2.7-.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            </button>
                        </div>
                        @error('password') <span class="field-error">{{ $message }}</span> @enderror
                    </div>

                    <label class="remember-option" for="remember">
                        <input id="remember" name="remember" type="checkbox" value="1" @checked(old('remember'))>
                        <span>Ingat saya</span>
                    </label>

                    <button class="submit-button" type="submit">Masuk</button>
                </form>

                <p class="register-prompt">Belum punya akun? <a href="{{ route('registration.create') }}">Daftar Sekarang</a></p>
                <p class="sso-note">Mahasiswa dan dosen masuk dengan akun SiAkad.</p>
            </div>
        </section>
    </main>
</body>
</html>