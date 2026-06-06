<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class WelcomeGreetingWidget extends Widget
{
    // Arahkan jalurnya (path) ke blade file yang akan kita buat sendiri
    protected static string $view = 'filament.widgets.welcome-greeting-widget';

    // Buat widgetnya membentang Full 1 Layar Penuh
    protected int | string | array $columnSpan = 'full';
}
