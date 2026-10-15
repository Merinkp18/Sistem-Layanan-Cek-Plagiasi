<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $isHistory ? 'Riwayat Saya' : 'Pengecekan Saya' }} | SILACEK</title>
    @vite(['resources/css/app.css'])
</head>
<body class="application-page checking-page">
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
                <a class="sidebar-link {{ $isHistory ? '' : 'is-active' }}" href="{{ route('pengecekan-saya.index') }}" @unless ($isHistory) aria-current="page" @endunless><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="8.5" cy="8.5" r="5.5" stroke="currentColor" stroke-width="1.5"/><path d="m13 13 4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg><span>Pengecekan Saya</span></a>
                <a class="sidebar-link {{ $isHistory ? 'is-active' : '' }}" href="{{ route('riwayat-saya.index') }}" @if ($isHistory) aria-current="page" @endif><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M10 6v4l2.5 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Riwayat Saya</span></a>
                <a class="sidebar-link" href="{{ route('profile.edit') }}"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="6" r="3" stroke="currentColor" stroke-width="1.5"/><path d="M4 17c.4-3.1 2.4-4.8 6-4.8s5.6 1.7 6 4.8H4Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg><span>Profil</span></a>
            </nav>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="sidebar-logout" type="submit"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M8 3H4v14h4M11 6l4 4-4 4M6 10h9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Logout</span></button>
            </form>
        </aside>

        <main class="application-main checking-content">
            <header class="checking-title">
                <h1>{{ $isHistory ? 'Riwayat Saya' : 'Pengecekan Saya' }}</h1>
                <p>{{ $isHistory ? 'Riwayat pengajuan pengecekan yang telah selesai.' : 'Pantau status pengecekanmu.' }}</p>
            </header>

            @forelse ($submissions as $submission)
                <article class="checking-card">
                    <div class="checking-card-top">
                        <div class="queue-number">
                            <span>No. Antrean</span>
                            <strong>#{{ $submission->queue_number }}</strong>
                        </div>
                        <div class="checking-owner">
                            <strong>{{ $submission->tujuan_cek }}</strong>
                            <span>{{ $submission->nama_lengkap }}</span>
                            <span class="applicant-type">{{ ucfirst($submission->jenis_pengaju) }}</span>
                        </div>
                        <div class="checking-date">
                            <span>Tanggal Pengajuan</span>
                            <time datetime="{{ $submission->tanggal_pengajuan }}">{{ $submission->submitted_date }}</time>
                        </div>
                    </div>

                    <ol class="checking-progress" aria-label="Status {{ $submission->status_label }}">
                        <li class="{{ $submission->progress_step >= 1 ? 'is-reached' : '' }} {{ $submission->progress_step === 1 ? 'is-current' : '' }}">
                            <span aria-hidden="true">{{ $submission->progress_step > 1 ? '&#10003;' : '1' }}</span>
                            <small>Dikonfirmasi</small>
                        </li>
                        <li class="{{ $submission->progress_step >= 2 ? 'is-reached' : '' }} {{ $submission->progress_step === 2 ? 'is-current' : '' }}">
                            <span aria-hidden="true">{{ $submission->progress_step > 2 ? '&#10003;' : '2' }}</span>
                            <small>Proses Pengecekan</small>
                        </li>
                        <li class="{{ $submission->progress_step >= 3 ? 'is-reached' : '' }} {{ $submission->progress_step === 3 ? 'is-current' : '' }}">
                            <span aria-hidden="true">3</span>
                            <small>Hasil Pengecekan</small>
                        </li>
                    </ol>

                    <div class="checking-card-bottom">
                        <div class="checking-document-title">
                            <span>Judul Karya Ilmiah</span>
                            <strong>{{ $submission->judul_karya }}</strong>
                        </div>
                        <div class="checking-document-file">
                            <span>Dokumen</span>
                            <strong class="checking-filename" title="{{ $submission->document_name }}">
                                <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M5 2.5h7l3 3v12H5v-15Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M12 2.5v3h3M7.5 9h5M7.5 12h5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                                <span>{{ $submission->document_name }}</span>
                            </strong>
                        </div>
                        <div class="checking-card-action">
                            <span class="checking-status status-{{ $submission->status }}">{{ $submission->status_label }}</span>
                            <a class="checking-detail-link" href="{{ route('pengecekan-saya.show', $submission->id_pengajuan) }}">Lihat Detail <span aria-hidden="true">&#8594;</span></a>
                        </div>
                    </div>
                </article>
            @empty
                <div class="checking-empty">
                    <h2>{{ $isHistory ? 'Belum ada riwayat pengecekan' : 'Belum ada pengajuan' }}</h2>
                    <p>{{ $isHistory ? 'Pengajuan yang telah selesai akan tampil di sini.' : 'Pengajuan yang kamu kirim akan tampil di sini.' }}</p>
                    <a href="{{ route('pengajuan.create') }}">Ajukan Pengecekan</a>
                </div>
            @endforelse
        </main>
    </div>
</body>
</html>