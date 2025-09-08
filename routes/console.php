<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Güneş Bilgisayar XML import otomatik güncelleme - 30 dakikada bir
Schedule::command('gunes:xml-import')->everyThirtyMinutes();

// Döviz kurları güncelleme - 30 dakikada bir
Schedule::command('currency:update')->everyThirtyMinutes();

// Alternatif olarak job ile de çalıştırabiliriz
// Schedule::job(new \App\Jobs\GunesXmlImportJob())->everyThirtyMinutes();
