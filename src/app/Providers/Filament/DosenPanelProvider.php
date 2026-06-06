<?php

namespace App\Providers\Filament;

use App\Filament\Dosen\Pages\Auth\CustomLogin;
use Filament\Pages\Auth\Register as FilamentRegister;
use App\Filament\Dosen\Pages\Auth\RegisterDosen;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use DiogoGPinto\AuthUIEnhancer\AuthUIEnhancerPlugin;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class DosenPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('dosen')
            ->path('dosen')
            ->login(CustomLogin::class) // Kita buat Custom Login Class
            ->registration(RegisterDosen::class) // Kita buat Custom Registration Class
            ->profile() // Mengaktifkan menu profil secara otomatis di Sidebar Filament
            ->passwordReset()
            ->colors([
                'primary' => Color::Slate,
            ])
            ->font('Inter') // Menyamakan dengan font welcome page
           ->plugins([
                AuthUIEnhancerPlugin::make()
                    ->showEmptyPanelOnMobile(false)
                    ->formPanelPosition('right')
                    ->formPanelWidth('40%')
                    ->emptyPanelBackgroundImageOpacity('70%')
                    // Jika Anda punya gambar sendiri di public/images/, gunakan: asset('images/ruang-dosen-bg.jpg')
                    // Sementara pakai gambar bawaan (random dari unsplash) agar pasti jalan dulu:
                    ->emptyPanelBackgroundImageUrl('https://images.unsplash.com/photo-1544928147-79a2dbc1f389?q=80&w=2574&auto=format&fit=crop')
            ])
            ->discoverResources(in: app_path('Filament/Dosen/Resources'), for: 'App\\Filament\\Dosen\\Resources')
            ->resources([
                \App\Filament\Admin\Resources\SubmissionResource::class,
                \App\Filament\Admin\Resources\TugasAkhirResource::class,
                \App\Filament\Admin\Resources\BimbinganResource::class,
            ])
            ->discoverPages(in: app_path('Filament/Dosen/Pages'), for: 'App\\Filament\\Dosen\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Dosen/Widgets'), for: 'App\\Filament\\Dosen\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
