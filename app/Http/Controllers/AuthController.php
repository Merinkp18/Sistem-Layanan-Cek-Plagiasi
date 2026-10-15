<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $identifier = trim($validated['identifier']);
        $throttleKey = 'silacek-login:'.Str::transliterate(Str::lower($identifier));

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            throw ValidationException::withMessages([
                'login' => 'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($throttleKey).' detik.',
            ]);
        }

        $admin = DB::table('admin')->where('username', $identifier)->first();

        if ($admin && Hash::check($validated['password'], $admin->password)) {
            RateLimiter::clear($throttleKey);
            $this->startActorSession($request, 'admin', $admin->id_admin, $admin->nama, $admin->password);

            return redirect()->intended(route('dashboard'));
        }

        $staff = DB::table('registrasi')
            ->where('nip', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        if ($staff && Hash::check($validated['password'], $staff->password)) {
            if ($staff->status !== 'disetujui') {
                RateLimiter::hit($throttleKey, 60);

                $message = $staff->status === 'ditolak'
                    ? 'Pendaftaran akun ditolak. Silakan hubungi admin.'
                    : 'Akun masih menunggu persetujuan admin.';

                throw ValidationException::withMessages(['login' => $message]);
            }

            RateLimiter::clear($throttleKey);
            $this->startActorSession($request, 'staff', $staff->id_registrasi, $staff->nama_lengkap, $staff->password);

            return redirect()->intended(route('dashboard'));
        }

        RateLimiter::hit($throttleKey, 60);

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            throw ValidationException::withMessages([
                'login' => 'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($throttleKey).' detik.',
            ]);
        }

        throw ValidationException::withMessages([
            'login' => 'Username/NIP/email atau password tidak sesuai.',
        ]);
    }

    public function showRegistration(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100', 'regex:/^[\p{L} .,\x27-]+$/u'],
            'nip' => ['required', 'digits:18', 'unique:registrasi,nip'],
            'email' => ['required', 'email', 'max:100', 'unique:registrasi,email'],
            'no_hp' => ['required', 'regex:/^628[0-9]{8,12}$/'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ], [
            'nama_lengkap.regex' => 'Nama hanya boleh berisi huruf, spasi, titik, koma, apostrof, dan tanda strip.',
            'nip.digits'         => 'NIP harus berupa 18 digit angka.',
            'nip.unique'         => 'NIP sudah terdaftar.',
            'no_hp.regex'        => 'Nomor HP tidak valid (contoh: 62XXXXXXXXXXX).',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
        ]);

        DB::table('registrasi')->insert([
            'nama_lengkap' => $validated['nama_lengkap'],
            'nip' => $validated['nip'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
            'password' => Hash::make($validated['password']),
            'status' => 'menunggu',
        ]);

        return redirect()->route('login')->with('status', 'Pendaftaran berhasil dikirim. Silakan tunggu persetujuan.');
    }

    public function dashboard(): View
    {
        return view('auth.dashboard');
    }

    public function profile(Request $request): View
    {
        abort_unless($request->session()->get('actor_role') === 'staff', 403);

        $profile = DB::table('registrasi')
            ->where('id_registrasi', $request->session()->get('actor_id'))
            ->firstOrFail();

        return view('profile.edit', compact('profile'));
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        abort_unless($request->session()->get('actor_role') === 'staff', 403);

        $registrationId = $request->session()->get('actor_id');
        $validated = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:100', 'regex:/^[\p{L} .,\x27-]+$/u'],
            'email' => ['required', 'email', 'max:100', Rule::unique('registrasi', 'email')->ignore($registrationId, 'id_registrasi')],
            'no_hp' => ['required', 'regex:/^628[0-9]{8,12}$/'],
        ], [
            'nama_lengkap.regex' => 'Nama hanya boleh berisi huruf, spasi, titik, koma, apostrof, dan tanda strip.',
            'email.unique' => 'Email sudah digunakan akun lain.',
            'no_hp.regex' => 'Nomor HP tidak valid (contoh: 62XXXXXXXXXXX).',
        ]);

        DB::table('registrasi')
            ->where('id_registrasi', $registrationId)
            ->update([
                'nama_lengkap' => $validated['nama_lengkap'],
                'email' => $validated['email'],
                'no_hp' => $validated['no_hp'],
            ]);

        $request->session()->put('actor_name', $validated['nama_lengkap']);

        return redirect()->route('profile.edit')->with('profile_updated', 'Profil berhasil diperbarui.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Cookie::queue(Cookie::forget('silacek_remember'));

        return redirect()->route('login');
    }

    private function startActorSession(Request $request, string $role, int $id, string $name, string $passwordHash): void
    {
        $request->session()->regenerate();
        $request->session()->put([
            'actor_role' => $role,
            'actor_id' => $id,
            'actor_name' => $name,
        ]);

        if ($request->boolean('remember')) {
            Cookie::queue(cookie(
                'silacek_remember',
                json_encode([
                    'role' => $role,
                    'id' => $id,
                    'password_fingerprint' => hash('sha256', $passwordHash),
                ], JSON_THROW_ON_ERROR),
                60 * 24 * 30,
                '/',
                null,
                $request->isSecure(),
                true,
                false,
                'lax',
            ));
        } else {
            Cookie::queue(Cookie::forget('silacek_remember'));
        }
    }
}