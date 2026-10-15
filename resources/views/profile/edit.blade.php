<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profil Saya | SILACEK</title>
    @vite(['resources/css/app.css'])
</head>
<body class="application-page">
    <header class="application-header">
        <a class="application-brand" href="{{ route('dashboard') }}">
            <svg viewBox="0 0 32 26" fill="none" aria-hidden="true"><path d="M2 3.5c5.4-1.4 10-.6 14 2.5v17c-4-3.1-8.6-3.9-14-2.5v-17ZM30 3.5c-5.4-1.4-10-.6-14 2.5v17c4-3.1 8.6-3.9 14-2.5v-17Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="M16 6v17" stroke="currentColor" stroke-width="1.8"/></svg>
            <span>SILACEK</span>
        </a>
        <button class="application-user" type="button" aria-label="Menu akun">
            <span class="user-avatar" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.2" fill="currentColor"/><path d="M5 20c.4-3.6 2.8-5.5 7-5.5s6.6 1.9 7 5.5H5Z" fill="currentColor"/></svg></span>
            <span>{{ session('actor_name') }}</span>
            <svg class="user-chevron" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="m4 6 4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
    </header>

    <div class="application-layout">
        <aside class="application-sidebar">
            <nav aria-label="Navigasi utama">
                <a class="sidebar-link" href="{{ route('pengajuan.create') }}"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M5 2.5h7l3 3v12H5v-15Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M12 2.5v3h3M7.5 9h5M7.5 12h5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg><span>Ajukan Pengecekan</span></a>
                <a class="sidebar-link" href="{{ route('pengecekan-saya.index') }}"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="8.5" cy="8.5" r="5.5" stroke="currentColor" stroke-width="1.5"/><path d="m13 13 4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg><span>Pengecekan Saya</span></a>
                <a class="sidebar-link" href="{{ route('riwayat-saya.index') }}"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M10 6v4l2.5 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Riwayat Saya</span></a>
                <a class="sidebar-link is-active" href="{{ route('profile.edit') }}" aria-current="page"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="6" r="3" stroke="currentColor" stroke-width="1.5"/><path d="M4 17c.4-3.1 2.4-4.8 6-4.8s5.6 1.7 6 4.8H4Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg><span>Profil</span></a>
            </nav>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="sidebar-logout" type="submit"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M8 3H4v14h4M11 6l4 4-4 4M6 10h9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Logout</span></button>
            </form>
        </aside>

        <main class="application-main profile-main">
            <header class="application-title">
                <h1>Profil Saya</h1>
                <p>Periksa dan perbarui informasi akunmu.</p>
            </header>

            @if (session('profile_updated'))
                <div class="profile-notice" role="status">{{ session('profile_updated') }}</div>
            @endif
            @if ($errors->any())
                <div class="submission-alert" role="alert">
                    <strong>Periksa kembali data berikut:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="profile-card">
                <div class="profile-card-heading">
                    <span class="profile-avatar" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.2"/><path d="M5 20c.4-3.6 2.8-5.5 7-5.5s6.6 1.9 7 5.5H5Z"/></svg></span>
                    <div><h2>Informasi Akun</h2><p>Perbarui nama, email, dan nomor handphone.</p></div>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="profile-form">
                    @csrf
                    @method('PUT')
                    <div class="profile-fields">
                        <div class="profile-field">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input id="nama_lengkap" name="nama_lengkap" type="text" value="{{ old('nama_lengkap', $profile->nama_lengkap) }}" maxlength="100" autocomplete="name" required>
                            @error('nama_lengkap') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="profile-field">
                            <label for="nip">NIP</label>
                            <input id="nip" type="text" value="{{ $profile->nip }}" readonly>
                            <small>NIP digunakan sebagai identitas akun dan tidak dapat diubah.</small>
                        </div>
                        <div class="profile-field">
                            <label for="email">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $profile->email) }}" maxlength="100" autocomplete="email" required>
                            @error('email') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                        <div class="profile-field">
                            <label for="no_hp">Nomor Handphone (WhatsApp)</label>
                            <input id="no_hp" name="no_hp" type="tel" value="{{ old('no_hp', $profile->no_hp) }}" placeholder="Contoh: 6281234567890" maxlength="15" autocomplete="tel" required>
                            @error('no_hp') <span class="field-error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="profile-actions">
                        <button class="submit-button" type="submit">Simpan Perubahan</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>