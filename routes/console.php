<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// PRD Section 11: Auto-cancel pendaftaran yang DP-nya tidak dibayar dalam 7 hari
Schedule::command('app:cancel-expired-dp')->daily();
