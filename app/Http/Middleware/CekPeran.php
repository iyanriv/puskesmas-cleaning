<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekPeran
{
    public function handle(Request $request, Closure $next, ...$peran): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $namaPeran = auth()->user()->peran?->nama_peran;

        if (! in_array($namaPeran, $peran)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
