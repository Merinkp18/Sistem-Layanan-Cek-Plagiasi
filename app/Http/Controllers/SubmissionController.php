<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class SubmissionController extends Controller
{
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