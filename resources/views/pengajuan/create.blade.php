<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ajukan Pengecekan | SILACEK</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="application-page">
    @php
        $initialStep = 1;
        if ($errors->hasAny(['ss_ig_perpus', 'ss_ig_cinta_pustaka', 'ss_tiktok_cinta_pustaka'])) {
            $initialStep = 2;
        }
        if ($errors->hasAny(['judul_karya', 'file_karya', 'file_sk'])) {
            $initialStep = 3;
        }
    @endphp
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
                <a class="sidebar-link is-active" href="{{ route('pengajuan.create') }}"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M5 2.5h7l3 3v12H5v-15Z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M12 2.5v3h3M7.5 9h5M7.5 12h5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/></svg><span>Ajukan Pengecekan</span></a>
                <a class="sidebar-link" href="{{ route('pengecekan-saya.index') }}"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="8.5" cy="8.5" r="5.5" stroke="currentColor" stroke-width="1.5"/><path d="m13 13 4 4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg><span>Pengecekan Saya</span></a>
                <a class="sidebar-link" href="{{ route('riwayat-saya.index') }}"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="10" r="7" stroke="currentColor" stroke-width="1.5"/><path d="M10 6v4l2.5 1.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Riwayat Saya</span></a>
                <a class="sidebar-link" href="{{ route('profile.edit') }}"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><circle cx="10" cy="6" r="3" stroke="currentColor" stroke-width="1.5"/><path d="M4 17c.4-3.1 2.4-4.8 6-4.8s5.6 1.7 6 4.8H4Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg><span>Profil</span></a>
            </nav>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="sidebar-logout" type="submit"><svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M8 3H4v14h4M11 6l4 4-4 4M6 10h9" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Logout</span></button>
            </form>
        </aside>

        <main class="application-main">
            @if (session('submission_id'))
                <section class="application-title">
                    <h1>Pengajuan Berhasil!</h1>
                    <p>Pengajuan pengecekan karya ilmiah Anda telah berhasil dikirim.</p>
                </section>
                <ol class="submission-steps is-complete" aria-label="Tahapan pengajuan">
                    <li class="is-done"><span>1</span><small>Data Diri</small></li>
                    <li class="is-done"><span>2</span><small>Persyaratan</small></li>
                    <li class="is-done"><span>3</span><small>Upload File</small></li>
                    <li class="is-current"><span>4</span><small>Selesai</small></li>
                </ol>
                <section class="submission-card submission-success">
                    <div class="success-heading">
                        <span class="success-mark" aria-hidden="true">&#10003;</span>
                        <div>
                            <h2>Pengajuan berhasil dikirim</h2>
                            <p>Silakan menunggu proses pengecekan oleh petugas perpustakaan.</p>
                        </div>
                    </div>
                    <dl class="submission-summary">
                        <div class="summary-queue">
                            <dt>No. Antrean</dt>
                            <dd>#{{ str_pad((string) session('submission_id'), 3, '0', STR_PAD_LEFT) }}</dd>
                        </div>
                        <div class="summary-title">
                            <dt>Judul Karya Ilmiah</dt>
                            <dd>{{ session('submission_title') }}</dd>
                        </div>
                        <div class="summary-status">
                            <dt>Status</dt>
                            <dd><span class="status-badge">{{ session('submission_status', 'Menunggu') }}</span></dd>
                        </div>
                    </dl>
                    <div class="success-note">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="1.5" stroke="currentColor" stroke-width="1.7"/><path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <p>Pengajuan sudah masuk ke antrean. Status akan diperbarui setelah petugas meninjau dokumen Anda.</p>
                    </div>
                    <a class="success-back-link" href="{{ route('dashboard') }}">Kembali ke beranda</a>
                </section>
            @else
                <section class="application-title">
                    <p class="eyebrow">Layanan Perpustakaan</p>
                    <h1>Ajukan Pengecekan</h1>
                    <p>Lengkapi data pengajuan dan dokumen karya ilmiah.</p>
                </section>

                @if ($errors->any())
                    <div class="submission-alert" role="alert" data-submission-error>
                        <strong>Periksa kembali isian berikut:</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <ol class="submission-steps" aria-label="Tahapan pengajuan">
                    <li class="{{ $initialStep === 1 ? 'is-current' : ($initialStep > 1 ? 'is-done' : '') }}" data-step-indicator="1"><span>1</span><small>Data Diri</small></li>
                    <li class="{{ $initialStep === 2 ? 'is-current' : ($initialStep > 2 ? 'is-done' : '') }}" data-step-indicator="2"><span>2</span><small>Persyaratan</small></li>
                    <li class="{{ $initialStep === 3 ? 'is-current' : '' }}" data-step-indicator="3"><span>3</span><small>Upload File</small></li>
                    <li data-step-indicator="4"><span>4</span><small>Selesai</small></li>
                </ol>

                <form class="submission-card" method="POST" action="{{ route('pengajuan.store') }}" enctype="multipart/form-data" data-submission-wizard data-initial-step="{{ $initialStep }}">
                    @csrf
                    <section class="wizard-panel {{ $initialStep === 1 ? 'is-active' : '' }}" data-wizard-panel="1" {{ $initialStep !== 1 ? 'hidden' : '' }}>
                        <h2>Data Diri</h2>
                        <p class="section-intro">Lengkapi data pengajuan. Nama dan NIM/NIP diambil dari akun.</p>
                        <div class="submission-grid">
                            <div class="submission-field">
                                <label for="applicant_name">Nama Lengkap <span>*</span></label>
                                <input id="applicant_name" type="text" value="{{ $applicantName }}" placeholder="Nama dari akun" readonly>
                            </div>
                            <div class="submission-field">
                                <label for="email">Email <span>*</span></label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="Masukkan email" maxlength="100" autocomplete="email" required>
                            </div>
                            <div class="submission-field">
                                <label for="applicant_number">NIM / NIP <span>*</span></label>
                                <input id="applicant_number" type="text" value="{{ $applicantNumber }}" placeholder="Nomor dari akun" readonly>
                            </div>
                            <div class="submission-field">
                                <label for="no_hp">Nomor Handphone (WhatsApp) <span>*</span></label>
                                <input id="no_hp" name="no_hp" type="tel" value="{{ old('no_hp') }}" placeholder="Contoh: 081234567890" maxlength="20" autocomplete="tel" required>
                            </div>
                            <div class="submission-field">
                                <label for="jurusan">Jurusan <span>*</span></label>
                                <input id="jurusan" name="jurusan" type="text" value="{{ old('jurusan', $applicantDepartment) }}" placeholder="Masukkan jurusan" maxlength="100" required>
                            </div>
                            <div class="submission-field">
                                <label for="tujuan_cek">Tujuan Pengecekan <span>*</span></label>
                                <select id="tujuan_cek" name="tujuan_cek" required data-purpose-select>
                                    <option value="" disabled @selected(! old('tujuan_cek'))>Pilih tujuan pengecekan</option>
                                    <option value="Skripsi" @selected(old('tujuan_cek') === 'Skripsi')>Skripsi</option>
                                    <option value="Tesis" @selected(old('tujuan_cek') === 'Tesis')>Tesis</option>
                                    <option value="Disertasi" @selected(old('tujuan_cek') === 'Disertasi')>Disertasi</option>
                                    <option value="Artikel/Jurnal" @selected(old('tujuan_cek') === 'Artikel/Jurnal')>Artikel/Jurnal</option>
                                    <option value="Lainnya" @selected(old('tujuan_cek') === 'Lainnya')>Lainnya</option>
                                </select>
                            </div>
                            <div class="submission-field">
                                <label for="jenis_pengaju">Jenis Pengaju</label>
                                <select id="jenis_pengaju" name="jenis_pengaju" required>
                                    <option value="" disabled @selected(! old('jenis_pengaju'))>Pilih jenis pengaju</option>
                                    <option value="mahasiswa" @selected(old('jenis_pengaju') === 'mahasiswa')>Mahasiswa</option>
                                    <option value="dosen" @selected(old('jenis_pengaju') === 'dosen')>Dosen</option>
                                    <option value="staff" @selected(old('jenis_pengaju') === 'staff')>Staff</option>
                                </select>
                            </div>
                        </div>
                        <div class="wizard-actions wizard-actions-end">
                            <button class="submit-button" type="button" data-wizard-next>Selanjutnya</button>
                        </div>
                    </section>

                    <section class="wizard-panel {{ $initialStep === 2 ? 'is-active' : '' }}" data-wizard-panel="2" {{ $initialStep !== 2 ? 'hidden' : '' }}>
                        <h2>Persyaratan</h2>
                        <p class="section-intro">Unggah bukti follow untuk ketiga akun. Setiap file dapat berupa PNG, JPG, atau PDF dengan ukuran maksimal 2 MB.</p>
                        <div class="requirements-upload-list">
                            <div class="requirement-upload-item">
                                <label class="requirement-upload-label" for="ss_ig_perpus">Instagram Perpustakaan <span>*</span></label>
                                <div class="requirement-dropzone" data-file-dropzone>
                                    <input class="requirement-file-input" id="ss_ig_perpus" name="ss_ig_perpus" type="file" accept=".png,.jpg,.jpeg,.pdf,image/png,image/jpeg,application/pdf" aria-describedby="ss_ig_perpus_info" required>
                                    <svg class="upload-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 15V4m0 0L8 8m4-4 4 4M5 14v5h14v-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <p class="dropzone-prompt">Seret dan lepaskan file atau <label for="ss_ig_perpus">Pilih file</label></p>
                                    <small id="ss_ig_perpus_info">PNG, JPG, atau PDF · Maksimal 2 MB</small>
                                    <span class="selected-file" data-selected-file>Belum ada file dipilih</span>
                                </div>
                            </div>
                            <div class="requirement-upload-item">
                                <label class="requirement-upload-label" for="ss_ig_cinta_pustaka">Instagram Cinta Pustaka <span>*</span></label>
                                <div class="requirement-dropzone" data-file-dropzone>
                                    <input class="requirement-file-input" id="ss_ig_cinta_pustaka" name="ss_ig_cinta_pustaka" type="file" accept=".png,.jpg,.jpeg,.pdf,image/png,image/jpeg,application/pdf" aria-describedby="ss_ig_cinta_pustaka_info" required>
                                    <svg class="upload-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 15V4m0 0L8 8m4-4 4 4M5 14v5h14v-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <p class="dropzone-prompt">Seret dan lepaskan file atau <label for="ss_ig_cinta_pustaka">Pilih file</label></p>
                                    <small id="ss_ig_cinta_pustaka_info">PNG, JPG, atau PDF · Maksimal 2 MB</small>
                                    <span class="selected-file" data-selected-file>Belum ada file dipilih</span>
                                </div>
                            </div>
                            <div class="requirement-upload-item">
                                <label class="requirement-upload-label" for="ss_tiktok_cinta_pustaka">TikTok Cinta Pustaka <span>*</span></label>
                                <div class="requirement-dropzone" data-file-dropzone>
                                    <input class="requirement-file-input" id="ss_tiktok_cinta_pustaka" name="ss_tiktok_cinta_pustaka" type="file" accept=".png,.jpg,.jpeg,.pdf,image/png,image/jpeg,application/pdf" aria-describedby="ss_tiktok_cinta_pustaka_info" required>
                                    <svg class="upload-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 15V4m0 0L8 8m4-4 4 4M5 14v5h14v-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <p class="dropzone-prompt">Seret dan lepaskan file atau <label for="ss_tiktok_cinta_pustaka">Pilih file</label></p>
                                    <small id="ss_tiktok_cinta_pustaka_info">PNG, JPG, atau PDF · Maksimal 2 MB</small>
                                    <span class="selected-file" data-selected-file>Belum ada file dipilih</span>
                                </div>
                            </div>
                        </div>
                        <div class="wizard-actions">
                            <button class="secondary-button" type="button" data-wizard-back>Kembali</button>
                            <button class="submit-button" type="button" data-wizard-next>Selanjutnya</button>
                        </div>
                    </section>

                    <section class="wizard-panel {{ $initialStep === 3 ? 'is-active' : '' }}" data-wizard-panel="3" {{ $initialStep !== 3 ? 'hidden' : '' }}>
                        <h2>Upload File</h2>
                        <p class="section-intro">File karya wajib berformat PDF, DOC, atau DOCX dan maksimal 20 MB. SK wajib untuk pengajuan skripsi.</p>
                        <div class="submission-grid">
                            <div class="submission-field submission-field-full">
                                <label for="judul_karya">Judul Karya</label>
                                <input id="judul_karya" name="judul_karya" type="text" value="{{ old('judul_karya') }}" placeholder="Masukkan judul karya ilmiah" maxlength="255" required>
                            </div>
                            <div class="submission-field submission-field-full">
                                <div class="requirement-upload-item">
                                    <label class="requirement-upload-label" for="file_karya">File Karya Ilmiah <span>*</span></label>
                                    <div class="requirement-dropzone" data-file-dropzone>
                                        <input class="requirement-file-input" id="file_karya" name="file_karya" type="file" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" aria-describedby="file_karya_info" required>
                                        <svg class="upload-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 15V4m0 0L8 8m4-4 4 4M5 14v5h14v-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        <p class="dropzone-prompt">Seret dan lepaskan file atau <label for="file_karya">Pilih file</label></p>
                                        <small id="file_karya_info">PDF, DOC, atau DOCX · Maksimal 20 MB</small>
                                        <span class="selected-file" data-selected-file>Belum ada file dipilih</span>
                                    </div>
                                </div>
                            </div>
                            <div class="submission-field submission-field-full" data-sk-field>
                                <div class="requirement-upload-item">
                                    <label class="requirement-upload-label" for="file_sk">File SK <span>(wajib untuk Skripsi)</span></label>
                                    <div class="requirement-dropzone" data-file-dropzone>
                                        <input class="requirement-file-input" id="file_sk" name="file_sk" type="file" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" aria-describedby="file_sk_info" data-sk-input>
                                        <svg class="upload-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 15V4m0 0L8 8m4-4 4 4M5 14v5h14v-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        <p class="dropzone-prompt">Seret dan lepaskan file atau <label for="file_sk">Pilih file</label></p>
                                        <small id="file_sk_info">PDF, DOC, atau DOCX · Maksimal 20 MB</small>
                                        <span class="selected-file" data-selected-file>Belum ada file dipilih</span>
                                    </div>
                                    <small class="field-hint" data-sk-hint>Wajib diunggah jika tujuan pengecekan adalah Skripsi.</small>
                                </div>
                            </div>
                        </div>
                        <div class="wizard-actions">
                            <button class="secondary-button" type="button" data-wizard-back>Kembali</button>
                            <button class="submit-button" type="submit">Kirim Pengajuan</button>
                        </div>
                    </section>
                </form>
            @endif
        </main>
    </div>
</body>
</html>