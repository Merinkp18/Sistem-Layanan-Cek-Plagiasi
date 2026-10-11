<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registrasi | SILACEK</title>
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

        <section class="form-panel register-panel">
            <div class="form-content">
                <header class="form-heading">
                    <p class="eyebrow">Untuk Akun Staff UIN SSC</p>
                    <h1>Registrasi</h1>
                    <p>Isi data berikut. Akun dapat digunakan setelah disetujui.</p>
                </header>

                <form method="POST" action="{{ route('registration.store') }}" class="login-form">
                    @csrf
                    <div class="field-group">
                        <label for="nama_lengkap">Nama lengkap</label>
                        <input id="nama_lengkap" name="nama_lengkap" type="text" value="{{ old('nama_lengkap') }}" placeholder="Masukkan nama lengkap" maxlength="100" autocomplete="name" required>
                        @error('nama_lengkap') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field-group">
                        <label for="nip">NIP</label>
                        <input id="nip" name="nip" type="text" value="{{ old('nip') }}" placeholder="Masukkan NIP" required>
                        @error('nip') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field-group">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Masukkan email aktif" autocomplete="email" required>
                        @error('email') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field-group">
                        <label for="no_hp">Nomor HP</label>
                        <input id="no_hp" name="no_hp" type="tel" value="{{ old('no_hp') }}" placeholder="contoh 62XXXXXXXXXXX" autocomplete="tel" required>
                        @error('no_hp') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field-group">
                        <label for="password">Password</label>
                        <div class="password-input-wrapper">
                            <input id="password" name="password" type="password" placeholder="Masukkan password" autocomplete="new-password" required>
                            <button class="password-toggle" type="button" data-password-toggle="password" aria-label="Tampilkan password" aria-pressed="false" title="Tampilkan password">
                                <svg class="eye-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.8"/></svg>
                                <svg class="eye-off-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m3 3 18 18M10.6 6.2A9.7 9.7 0 0 1 12 6c6.1 0 9.5 6 9.5 6a16.7 16.7 0 0 1-3 3.5M6.2 6.7C3.8 8.2 2.5 12 2.5 12s3.4 6 9.5 6c1 0 1.9-.2 2.7-.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            </button>
                        </div>
                        @error('password') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field-group">
                        <label for="password_confirmation">Konfirmasi password</label>
                        <div class="password-input-wrapper">
                            <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Ulangi password" autocomplete="new-password" required>
                            <button class="password-toggle" type="button" data-password-toggle="password_confirmation" aria-label="Tampilkan password" aria-pressed="false" title="Tampilkan password">
                                <svg class="eye-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.8"/></svg>
                                <svg class="eye-off-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m3 3 18 18M10.6 6.2A9.7 9.7 0 0 1 12 6c6.1 0 9.5 6 9.5 6a16.7 16.7 0 0 1-3 3.5M6.2 6.7C3.8 8.2 2.5 12 2.5 12s3.4 6 9.5 6c1 0 1.9-.2 2.7-.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                            </button>
                        </div>
                    </div>
                    <button class="submit-button" type="submit">Daftar</button>
                </form>

                <p class="register-prompt">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
            </div>
        </section>
    </main>
</body>
</html>