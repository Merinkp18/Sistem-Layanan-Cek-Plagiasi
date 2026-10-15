<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class SubmissionController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->session()->get('actor_role') !== 'admin', 403);

        $submissions = $this->ownedSubmissions($request)->orderByDesc('tanggal_pengajuan')->get();

        $this->decorateSubmissions($submissions);

        return view('pengajuan.mine', [
            'submissions' => $submissions,
            'isHistory' => false,
        ]);
    }

    public function history(Request $request): View
    {
        abort_unless($request->session()->get('actor_role') !== 'admin', 403);

        $submissions = $this->ownedSubmissions($request)
            ->where('status', 'selesai')
            ->orderByDesc('tanggal_pengajuan')
            ->get();

        $this->decorateSubmissions($submissions);

        return view('pengajuan.mine', [
            'submissions' => $submissions,
            'isHistory' => true,
        ]);
    }

    private function decorateSubmissions($submissions): void
    {
        foreach ($submissions as $submission) {
            $submission->queue_number = str_pad((string) $submission->id_pengajuan, 3, '0', STR_PAD_LEFT);
            $submission->submitted_date = Carbon::parse($submission->tanggal_pengajuan)
                ->locale('id')
                ->translatedFormat('d F Y');
            $submission->document_name = basename($submission->file_karya);
            $submission->progress_step = match ($submission->status) {
                'diproses' => 2,
                'selesai' => 3,
                default => 1,
            };
            $submission->status_label = match ($submission->status) {
                'diproses' => 'Proses Pengecekan',
                'selesai' => 'Hasil Pengecekan',
                'ditolak' => 'Ditolak',
                default => 'Menunggu',
            };
        }
    }

    public function show(Request $request, int $submission): View
    {
        abort_unless($request->session()->get('actor_role') !== 'admin', 403);

        $record = $this->ownedSubmissions($request)
            ->where('id_pengajuan', $submission)
            ->firstOrFail();

        $record->queue_number = str_pad((string) $record->id_pengajuan, 3, '0', STR_PAD_LEFT);
        $record->submitted_date = Carbon::parse($record->tanggal_pengajuan)
            ->locale('id')
            ->translatedFormat('d F Y');
        $record->submitted_datetime = Carbon::parse($record->tanggal_pengajuan)
            ->locale('id')
            ->translatedFormat('d F Y, H:i');
        $record->progress_step = match ($record->status) {
            'diproses' => 2,
            'selesai' => 3,
            default => 1,
        };
        $record->status_label = match ($record->status) {
            'diproses' => 'Proses Pengecekan',
            'selesai' => 'Hasil Pengecekan',
            'ditolak' => 'Ditolak',
            default => 'Menunggu',
        };
        $record->documents = collect([
            ['label' => 'Karya Ilmiah', 'field' => 'file_karya', 'path' => $record->file_karya],
            ['label' => 'SK Skripsi', 'field' => 'file_sk', 'path' => $record->file_sk],
            ['label' => 'Bukti Instagram Perpustakaan', 'field' => 'ss_ig_perpus', 'path' => $record->ss_ig_perpus],
            ['label' => 'Bukti Instagram Cinta Pustaka', 'field' => 'ss_ig_cinta_pustaka', 'path' => $record->ss_ig_cinta_pustaka],
            ['label' => 'Bukti TikTok Cinta Pustaka', 'field' => 'ss_tiktok_cinta_pustaka', 'path' => $record->ss_tiktok_cinta_pustaka],
        ])->filter(fn (array $file): bool => is_string($file['path']) && $file['path'] !== '')
            ->map(fn (array $file): array => [...$file, 'name' => basename($file['path'])])
            ->values();

        return view('pengajuan.show', ['submission' => $record]);
    }

    public function download(Request $request, int $submission, string $file)
    {
        abort_unless($request->session()->get('actor_role') !== 'admin', 403);

        abort_unless(in_array($file, [
            'file_karya',
            'file_sk',
            'ss_ig_perpus',
            'ss_ig_cinta_pustaka',
            'ss_tiktok_cinta_pustaka',
            'file_hasil_cek',
        ], true), 404);

        $record = $this->ownedSubmissions($request)
            ->where('id_pengajuan', $submission)
            ->firstOrFail();
        $path = $record->{$file};

        abort_unless(is_string($path) && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path, basename($path));
    }

    private function ownedSubmissions(Request $request): Builder
    {
        $query = DB::table('form_pengajuan');

        if ($request->session()->get('actor_role') === 'staff') {
            return $query->where('id_registrasi', $request->session()->get('actor_id'));
        }

        $identityNumber = $request->session()->get('actor_nim_nip');

        if (is_string($identityNumber) && $identityNumber !== '') {
            return $query->where('nim_nip', $identityNumber);
        }

        return $query->whereRaw('1 = 0');
    }

    public function create(Request $request): View
    {
        abort_unless($request->session()->get('actor_role') !== 'admin', 403);

        $identity = $this->applicantIdentity($request);

        return view('pengajuan.create', [
            'applicantName' => $identity[0],
            'applicantNumber' => $identity[1],
            'applicantDepartment' => $request->session()->get('actor_jurusan', ''),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->session()->get('actor_role') !== 'admin', 403);

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:100'],
            'jenis_pengaju' => ['required', Rule::in(['mahasiswa', 'dosen', 'staff'])],
            'no_hp' => ['required', 'string', 'max:20', 'regex:/^[0-9+()\-\s]{8,20}$/'],
            'jurusan' => ['required', 'string', 'max:100'],
            'tujuan_cek' => ['required', Rule::in(['Skripsi', 'Tesis', 'Disertasi', 'Artikel/Jurnal', 'Lainnya'])],
            'judul_karya' => ['required', 'string', 'max:255'],
            'ss_ig_perpus' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'ss_ig_cinta_pustaka' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'ss_tiktok_cinta_pustaka' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'file_karya' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
            'file_sk' => [Rule::requiredIf($request->input('tujuan_cek') === 'Skripsi'), 'nullable', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
        ], [
            'no_hp.regex' => 'Format nomor HP tidak valid.',
            'ss_ig_perpus.required' => 'Screenshot Instagram perpustakaan wajib diunggah.',
            'ss_ig_cinta_pustaka.required' => 'Screenshot Instagram Cinta Pustaka wajib diunggah.',
            'ss_tiktok_cinta_pustaka.required' => 'Screenshot TikTok Cinta Pustaka wajib diunggah.',
            'ss_ig_perpus.max' => 'File Instagram Perpustakaan maksimal 2 MB.',
            'ss_ig_cinta_pustaka.max' => 'File Instagram Cinta Pustaka maksimal 2 MB.',
            'ss_tiktok_cinta_pustaka.max' => 'File TikTok Cinta Pustaka maksimal 2 MB.',
            'file_karya.required' => 'File karya ilmiah wajib diunggah.',
            'file_sk.required' => 'File SK wajib diunggah untuk pengajuan skripsi.',
            '*.max' => 'Ukuran file melebihi batas yang diperbolehkan.',
        ]);

        [$namaLengkap, $nimNip] = $this->applicantIdentity($request);
        $storedPaths = [];

        try {
            foreach ([
                'ss_ig_perpus',
                'ss_ig_cinta_pustaka',
                'ss_tiktok_cinta_pustaka',
                'file_karya',
                'file_sk',
            ] as $field) {
                if ($request->hasFile($field)) {
                    $storedPaths[$field] = $request->file($field)->store('pengajuan/'.$field, 'local');
                }
            }

            $idPengajuan = DB::table('form_pengajuan')->insertGetId([
                'id_registrasi' => $request->session()->get('actor_role') === 'staff'
                    ? $request->session()->get('actor_id')
                    : null,
                'id_admin' => null,
                'nama_lengkap' => $namaLengkap,
                'nim_nip' => $nimNip,
                'jurusan' => $validated['jurusan'] ?? null,
                'email' => $validated['email'],
                'jenis_pengaju' => $validated['jenis_pengaju'],
                'no_hp' => $validated['no_hp'],
                'tujuan_cek' => $validated['tujuan_cek'],
                'judul_karya' => $validated['judul_karya'],
                'file_karya' => $storedPaths['file_karya'],
                'file_sk' => $storedPaths['file_sk'] ?? null,
                'ss_ig_perpus' => $storedPaths['ss_ig_perpus'],
                'ss_ig_cinta_pustaka' => $storedPaths['ss_ig_cinta_pustaka'],
                'ss_tiktok_cinta_pustaka' => $storedPaths['ss_tiktok_cinta_pustaka'],
                'file_hasil_cek' => null,
                'status' => 'menunggu',
                'alasan_penolakan' => null,
                'tanggal_pengajuan' => now(),
            ]);
        } catch (Throwable $exception) {
            Storage::disk('local')->delete(array_values($storedPaths));

            throw $exception;
        }

        return redirect()->route('pengajuan.create')->with([
            'submission_id' => $idPengajuan,
            'submission_title' => $validated['judul_karya'],
            'submission_status' => 'Menunggu',
        ]);
    }

    private function applicantIdentity(Request $request): array
    {
        $name = $request->session()->get('actor_name');
        $identityNumber = $request->session()->get('actor_nim_nip');
        $registrationId = $request->session()->get('actor_id');

        if ($request->session()->get('actor_role') === 'staff') {
            $staff = DB::table('registrasi')->where('id_registrasi', $registrationId)->first();
            $name = $staff->nama_lengkap ?? $name;
            $identityNumber = $staff->nip ?? $identityNumber;
        }

        if (! is_string($name) || $name === '' || ! is_string($identityNumber) || $identityNumber === '') {
            throw ValidationException::withMessages([
                'login' => 'Data nama dan NIM/NIP akun belum tersedia. Silakan lengkapi profil terlebih dahulu.',
            ]);
        }

        return [$name, $identityNumber];
    }
}