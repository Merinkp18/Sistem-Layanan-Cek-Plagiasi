<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
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

        $throttleKey = Str::transliterate(Str::lower($validated['identifier']).'|'.$request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            throw ValidationException::withMessages([
                'login' => 'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($throttleKey).' detik.',
            ]);
        }

        $admin = DB::table('admin')->where('username', $validated['identifier'])->first();

        if ($admin && Hash::check($validated['password'], $admin->password)) {
            RateLimiter::clear($throttleKey);
            $this->startActorSession($request, 'admin', $admin->id_admin, $admin->nama, $admin->password);

            return redirect()->intended(route('dashboard'));
        }

        $staff = DB::table('registrasi')
            ->where('nip', $validated['identifier'])
            ->orWhere('email', $validated['identifier'])
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
            'nama_lengkap' => ['required', 'string', 'max:100'],
            'nip' => ['required', 'string', 'max:30', 'unique:registrasi,nip'],
            'email' => ['required', 'email', 'max:100', 'unique:registrasi,email'],
            'no_hp' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        DB::table('registrasi')->insert([
            'nama_lengkap' => $validated['nama_lengkap'],
            'nip' => $validated['nip'],
            'email' => $validated['email'],
            'no_hp' => $validated['no_hp'],
            'password' => Hash::make($validated['password']),
            'status' => 'menunggu',
        ]);

        return redirect()->route('login')->with('status', 'Pendaftaran berhasil dikirim. Silakan tunggu persetujuan admin.');
    }

    public function dashboard(): View
    {
        return view('auth.dashboard');
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