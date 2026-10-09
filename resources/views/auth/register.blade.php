<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Registrasi Staff | SILACEK</title>
    @vite(['resources/css/app.css'])
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
                <p class="brand-subtitle">Sistem Informasi Layanan<br> Cek Plagiasi Karya Ilmiah</p>
            </div>
            <div class="campus-caption">UIN Siber Syekh Nurjati Cirebon</div>
        </section>

        <section class="form-panel register-panel">
            <div class="form-content">
                <header class="form-heading">
                    <p class="eyebrow">Akun Staff</p>
                    <h1>Registrasi</h1>
                    <p>Isi data berikut. Akun dapat digunakan setelah disetujui admin.</p>
                </header>

                <form method="POST" action="{{ route('registration.store') }}" class="login-form">
                    @csrf
                    <div class="field-group">
                        <label for="nama_lengkap">Nama lengkap</label>
                        <input id="nama_lengkap" name="nama_lengkap" type="text" value="{{ old('nama_lengkap') }}" autocomplete="name" required>
                        @error('nama_lengkap') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field-group">
                        <label for="nip">NIP</label>
                        <input id="nip" name="nip" type="text" value="{{ old('nip') }}" required>
                        @error('nip') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field-group">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required>
                        @error('email') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field-group">
                        <label for="no_hp">Nomor HP</label>
                        <input id="no_hp" name="no_hp" type="tel" value="{{ old('no_hp') }}" autocomplete="tel" required>
                        @error('no_hp') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field-group">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password" required>
                        @error('password') <span class="field-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="field-group">
                        <label for="password_confirmation">Konfirmasi password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>
                    </div>
                    <button class="submit-button" type="submit">Kirim Pendaftaran</button>
                </form>

                <p class="register-prompt">Sudah punya akun? <a href="{{ route('login') }}">Kembali masuk</a></p>
            </div>
        </section>
    </main>
</body>
</html>