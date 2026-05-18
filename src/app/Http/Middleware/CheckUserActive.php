<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Notifications\Notification;

class CheckUserActive
{
    public function handle(Request $request, Closure $next): Response
    {
       if (auth()->check() && !auth()->user()->is_active) {
            // Ambil alasan dari database, atau beri alasan default jika kosong
            $alasan = auth()->user()->nonactive_reason ?: 'Akun Anda dinonaktifkan oleh Administrator.';
            
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect('admin/login')->withErrors([
                'email' => 'DITOLAK: ' . $alasan // <--- MENAMPILKAN ALASAN SPESIFIK
            ]);
        }
        return $next($request);
    }
}