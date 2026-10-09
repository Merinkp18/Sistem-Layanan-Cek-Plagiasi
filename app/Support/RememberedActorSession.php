<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class RememberedActorSession
{
    public function restore(Request $request): bool
    {
        if ($request->session()->has('actor_role') && $request->session()->has('actor_id')) {
            return true;
        }

        $remembered = json_decode((string) $request->cookie('silacek_remember'), true);

        if (! is_array($remembered)) {
            return false;
        }

        $role = $remembered['role'] ?? null;
        $id = filter_var($remembered['id'] ?? null, FILTER_VALIDATE_INT);
        $fingerprint = $remembered['password_fingerprint'] ?? null;

        if (! in_array($role, ['admin', 'staff'], true) || $id === false || $id < 1 || ! is_string($fingerprint)) {
            Cookie::queue(Cookie::forget('silacek_remember'));

            return false;
        }

        if ($role === 'admin') {
            $actor = DB::table('admin')->where('id_admin', $id)->first();
            $name = $actor->nama ?? null;
        } else {
            $actor = DB::table('registrasi')
                ->where('id_registrasi', $id)
                ->where('status', 'disetujui')
                ->first();
            $name = $actor->nama_lengkap ?? null;
        }

        if (! $actor || ! hash_equals($fingerprint, hash('sha256', $actor->password))) {
            Cookie::queue(Cookie::forget('silacek_remember'));

            return false;
        }

        $request->session()->regenerate();
        $request->session()->put([
            'actor_role' => $role,
            'actor_id' => $id,
            'actor_name' => $name,
        ]);

        return true;
    }
}