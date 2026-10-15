<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Pengajuan #{{ $submission->queue_number }} | SILACEK</title>
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
                <a class="sidebar-link is-active" href="{{ route('pengecekan-saya.index') }}" aria-current="page"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="8.5" cy="8.5" r="5.5" stroke="currentColor" stroke-width="1.5"/><path d="m13 13 4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg><span>Pengecekan Saya</span></a>
                <a class="sidebar-link" href="{{ route('riwayat-saya.index') }}"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M10 6v4l2.5 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Riwayat Saya</span></a>
                <a class="sidebar-link" href="{{ route('profile.edit') }}"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="6" r="3" stroke="currentColor" stroke-width="1.5"/><path d="M4 17c.4-3.1 2.4-4.8 6-4.8s5.6 1.7 6 4.8H4Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg><span>Profil</span></a>
            </nav>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="sidebar-logout" type="submit"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M8 3H4v14h4M11 6l4 4-4 4M6 10h9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Logout</span></button>
            </form>
        </aside>

        <main class="application-main checking-content">
            <a class="detail-back-link" href="{{ route('pengecekan-saya.index') }}">&#8592; Kembali ke Pengecekan Saya</a>
            <header class="checking-title detail-page-title">
                <div>
                    <h1>Detail Pengajuan</h1>
                    <p>Rincian lengkap pengajuan pengecekan karya ilmiah.</p>
                </div>
                <span class="checking-status status-{{ $submission->status }}">{{ $submission->status_label }}</span>
            </header>

            <section class="checking-card detail-card">
                <div class="detail-overview">
                    <div class="queue-number">
                        <span>No. Antrean</span>
                        <strong>#{{ $submission->queue_number }}</strong>
                    </div>
                    <div class="detail-overview-copy">
                        <span class="detail-kicker">{{ $submission->tujuan_cek }}</span>
                        <h2>{{ $submission->judul_karya }}</h2>
                        <span class="applicant-type">{{ ucfirst($submission->jenis_pengaju) }}</span>
                    </div>
                    <div class="checking-date">
                        <span>Tanggal Pengajuan</span>
                        <time datetime="{{ $submission->tanggal_pengajuan }}">{{ $submission->submitted_datetime }}</time>
                    </div>
                </div>

                <ol class="checking-progress detail-progress" aria-label="Status {{ $submission->status_label }}">
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

                <section class="detail-section">
                    <h2>Data Pengajuan</h2>
                    <dl class="detail-data-grid">
                        <div><dt>Nama Lengkap</dt><dd>{{ $submission->nama_lengkap }}</dd></div>
                        <div><dt>NIM / NIP</dt><dd>{{ $submission->nim_nip }}</dd></div>
                        <div><dt>Email</dt><dd>{{ $submission->email }}</dd></div>
                        <div><dt>Nomor Handphone</dt><dd>{{ $submission->no_hp }}</dd></div>
                        <div><dt>Jurusan</dt><dd>{{ $submission->jurusan ?: '-' }}</dd></div>
                        <div><dt>Jenis Pengaju</dt><dd>{{ ucfirst($submission->jenis_pengaju) }}</dd></div>
                        <div><dt>Tujuan Pengecekan</dt><dd>{{ $submission->tujuan_cek }}</dd></div>
                        <div><dt>Status</dt><dd>{{ $submission->status_label }}</dd></div>
                    </dl>
                </section>

                <section class="detail-section">
                    <h2>Dokumen dan Persyaratan</h2>
                    <div class="detail-file-list">
                        @foreach ($submission->documents as $document)
                            <div class="detail-file-row">
                                <div class="detail-file-label">
                                    <svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M5 2.5h7l3 3v12H5v-15Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M12 2.5v3h3M7.5 9h5M7.5 12h5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg>
                                    <div><span>{{ $document['label'] }}</span><strong title="{{ $document['name'] }}">{{ $document['name'] }}</strong></div>
                                </div>
                                <a class="detail-file-download" href="{{ route('pengecekan-saya.file', [$submission->id_pengajuan, $document['field']]) }}">Unduh</a>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="detail-result-panel" aria-labelledby="detail-result-title">
                    <div class="detail-result-copy">
                        <span class="detail-result-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M12 3v11m0 0 4-4m-4 4-4-4M5 15v5h14v-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
                        <div>
                            <h2 id="detail-result-title">Hasil Pengecekan</h2>
                            @if ($submission->file_hasil_cek)
                                <p>Hasil pengecekan sudah tersedia untuk diunduh.</p>
                            @else
                                <p>Hasil akan tersedia di sini setelah proses pengecekan selesai.</p>
                            @endif
                        </div>
                    </div>
                    @if ($submission->file_hasil_cek)
                        <a class="detail-result-download" href="{{ route('pengecekan-saya.file', [$submission->id_pengajuan, 'file_hasil_cek']) }}">Unduh Hasil</a>
                    @else
                        <span class="detail-result-pending">Belum tersedia</span>
                    @endif
                </section>

                @if ($submission->alasan_penolakan)
                    <section class="detail-rejection">
                        <h2>Alasan Penolakan</h2>
                        <p>{{ $submission->alasan_penolakan }}</p>
                    </section>
                @endif
            </section>
        </main>
    </div>
</body>
</html>