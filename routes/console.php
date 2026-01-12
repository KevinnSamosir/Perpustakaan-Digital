<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule untuk mengecek peminjaman terlambat dan mengirim pengingat
// Jalankan setiap hari jam 8 pagi
Schedule::command('loans:check-overdue')->dailyAt('08:00');
